<?php
class PurchaseOrder
{
    private $db;
    private $table = "purchase_orders";
    private $itemsTable = 'purchase_order_items';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        // Ensure branch_id column exists (lazy migration)
        try {
            // Ensure currency column on purchase_orders (global order currency)
            $col = $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'currency'")->fetch();
            if(!$col){
                try { $this->db->exec("ALTER TABLE {$this->table} ADD COLUMN currency VARCHAR(10) NULL AFTER total_net"); } catch(Exception $e) { /* ignore */ }
            }
            $col = $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'branch_id'")->fetch();
            if (!$col) {
                $this->db->exec("ALTER TABLE {$this->table} ADD COLUMN branch_id INT NULL AFTER supplier_id, ADD INDEX (branch_id)");
            }
            // Composite index to speed listing by supplier+branch
            $idx = $this->db->query("SHOW INDEX FROM {$this->table} WHERE Key_name='idx_supplier_branch'")->fetch();
            if (!$idx) {
                $this->db->exec("ALTER TABLE {$this->table} ADD INDEX idx_supplier_branch (supplier_id, branch_id)");
            }
            // Ensure supplier_reference column exists (lazy migration)
            $col = $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'supplier_reference'")->fetch();
            if (!$col) {
                // Try to place after notes if exists, else append
                $hasNotes = $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'notes'")->fetch();
                if ($hasNotes) {
                    $this->db->exec("ALTER TABLE {$this->table} ADD COLUMN supplier_reference VARCHAR(190) NULL AFTER notes");
                } else {
                    $this->db->exec("ALTER TABLE {$this->table} ADD COLUMN supplier_reference VARCHAR(190) NULL");
                }
            }
            // Ensure financial summary columns exist (subtotal, discounts, net)
            $neededCols = [
                'subtotal' => "ALTER TABLE {$this->table} ADD COLUMN subtotal DECIMAL(15,4) NULL AFTER discount",
                'total_discount_lines' => "ALTER TABLE {$this->table} ADD COLUMN total_discount_lines DECIMAL(15,4) NULL AFTER subtotal",
                'total_discount_order' => "ALTER TABLE {$this->table} ADD COLUMN total_discount_order DECIMAL(15,4) NULL AFTER total_discount_lines",
                'total_net' => "ALTER TABLE {$this->table} ADD COLUMN total_net DECIMAL(15,4) NULL AFTER total_discount_order"
            ];
            foreach ($neededCols as $cName => $ddl) {
                $exists = $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE '" . $cName . "'")->fetch();
                if (!$exists) {
                    try { $this->db->exec($ddl); } catch (Exception $e) { /* ignore */ }
                }
            }
            // Ensure currency column for purchase_order_items (per-item currency)
            $col = $this->db->query("SHOW COLUMNS FROM {$this->itemsTable} LIKE 'currency'")->fetch();
            if(!$col){
                try { $this->db->exec("ALTER TABLE {$this->itemsTable} ADD COLUMN currency VARCHAR(10) NULL AFTER price"); } catch(Exception $e) { /* ignore */ }
            }
        } catch (Exception $e) { /* ignore */ }
    }

    public function datatable($start, $length, $search)
    {
        $where = "";
        if ($search) {
            $where = "WHERE s.name LIKE :search";
        }
        // Branch filtering (non super-admin users)
        $branchFilterSql = '';
        $branchParams = [];
        if (isset($_SESSION['user_id']) && !isSuperAdmin($_SESSION['user_id'])) {
            $branchIds = getUserBranchIds($_SESSION['user_id']);
            if (empty($branchIds)) {
                return ['data' => [], 'total' => 0, 'filtered' => 0];
            }
            $placeholders = implode(',', array_fill(0, count($branchIds), '?'));
            $branchFilterSql = ($where ? ' AND ' : ' WHERE ') . " (po.branch_id IN ($placeholders) OR po.branch_id IS NULL)";
            $branchParams = $branchIds;
        }

        $sql = "SELECT po.*, s.name AS supplier_name, b.name AS branch_name 
                FROM {$this->table} po
                JOIN suppliers s ON po.supplier_id = s.id
                LEFT JOIN branches b ON po.branch_id = b.id
                $where
                $branchFilterSql
                ORDER BY po.id DESC
                LIMIT :start, :length";
        $stmt = $this->db->prepare($sql);
        $positionalIndex = 1;
        if (!$search) {
            // only branch params positional
            foreach ($branchParams as $val) {
                $stmt->bindValue($positionalIndex++, $val, PDO::PARAM_INT);
            }
        }
        if ($search) $stmt->bindValue(':search', "%$search%");
        $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
        $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);
        if ($search) {
            foreach ($branchParams as $val) {
                $stmt->bindValue($positionalIndex++, $val, PDO::PARAM_INT);
            }
        }
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Total rows overall (no branch filter for super admin, else filtered)
        $totalQuery = "SELECT COUNT(*) FROM {$this->table}";
        $total = $this->db->query($totalQuery)->fetchColumn();

        // Filtered count (apply same WHERE without LIMIT)
        $countBase = "SELECT COUNT(*) AS cnt FROM {$this->table} po JOIN suppliers s ON po.supplier_id = s.id LEFT JOIN branches b ON po.branch_id = b.id $where $branchFilterSql";
        $countStmt = $this->db->prepare($countBase);
        $positionalIndex = 1;
        if (!$search) {
            foreach ($branchParams as $val) { $countStmt->bindValue($positionalIndex++, $val, PDO::PARAM_INT); }
        }
        if ($search) {
            $countStmt->bindValue(':search', "%$search%");
            foreach ($branchParams as $val) { $countStmt->bindValue($positionalIndex++, $val, PDO::PARAM_INT); }
        }
        $countStmt->execute();
        $filtered = (int)$countStmt->fetchColumn();

        return ['data' => $data, 'total' => $total, 'filtered' => $filtered];
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (supplier_id, branch_id, discount, notes, supplier_reference) 
                VALUES (:supplier_id, :branch_id, :discount, :notes, :supplier_reference)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':supplier_id' => $data['supplier_id'],
            ':branch_id' => $data['branch_id'] ?? null,
            ':discount' => $data['discount'],
            ':notes' => $data['notes'],
            ':supplier_reference' => $data['supplier_reference'] ?? null,
        ]);
        return $this->db->lastInsertId();
    }

    public function getSuppliers()
    {
        $stmt = $this->db->query("SELECT id, name FROM suppliers WHERE is_active = 1 ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addItems($po_id, $products, $quantities, $units, $prices = [], $currencies = [])
    {
        $sql = "INSERT INTO purchase_order_items (purchase_order_id, product_id, supplier_product_id, unit_id, quantity, price, currency)
            VALUES (:po_id, :product_id, :supplier_product_id, :unit_id, :quantity, :price, :currency)";
        $stmt = $this->db->prepare($sql);

        for ($i = 0; $i < count($products); $i++) {
            $priceVal = isset($prices[$i]) && $prices[$i] !== '' ? (float)$prices[$i] : null;
            $curVal = isset($currencies[$i]) ? substr(preg_replace('/[^A-Z]/i','', $currencies[$i]),0,10) : null;
            $stmt->execute([
                ':po_id' => $po_id,
                ':product_id' => (int)$products[$i],
                ':supplier_product_id' => (int)$products[$i], // provvisorio
                ':unit_id' => isset($units[$i]) ? (int)$units[$i] : null,
                ':quantity' => (float)$quantities[$i],
                ':price' => $priceVal,
                ':currency' => $curVal
            ]);
        }
    }

    /**
     * Replace all items of a purchase order with new ones (used in edit)
     */
    public function replaceItems(int $po_id, array $products, array $quantities, array $units, array $prices = [], array $currencies = []): void
    {
        $del = $this->db->prepare("DELETE FROM purchase_order_items WHERE purchase_order_id = :id");
        $del->execute([':id' => $po_id]);
        if (empty($products)) return;
        $this->addItems($po_id, $products, $quantities, $units, $prices, $currencies);
    }

        public function find($id) {
    $sql = "SELECT po.*, s.name AS supplier_name, s.email1 AS supplier_email, b.name AS branch_name
                FROM {$this->table} po
                JOIN suppliers s ON po.supplier_id = s.id
                LEFT JOIN branches b ON po.branch_id = b.id
                WHERE po.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($order) {
            // Recupera i prodotti dell'ordine
            $sqlItems = "SELECT poi.*, p.name AS product_name, p.requires_expiry, u.name AS unit_name
                         FROM purchase_order_items poi
                         JOIN products p ON poi.product_id = p.id
                         LEFT JOIN units u ON poi.unit_id = u.id
                         WHERE poi.purchase_order_id = :id";
            $stmtItems = $this->db->prepare($sqlItems);
            $stmtItems->execute([':id' => $id]);
            $order['items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

                // Conta i barcode generati (se tabella esiste)
                try {
                    $exists = $this->db->query("SHOW TABLES LIKE 'purchase_order_barcodes'")->fetch();
                    if ($exists) {
                        $stmtBc = $this->db->prepare("SELECT COUNT(*) FROM purchase_order_barcodes WHERE purchase_order_id = :oid");
                        $stmtBc->execute([':oid' => $id]);
                        $order['barcode_count'] = (int)$stmtBc->fetchColumn();
                    } else {
                        $order['barcode_count'] = 0;
                    }
                } catch (Exception $e) {
                    $order['barcode_count'] = 0; // fallback
                }
        }

        return $order ?: null;
    }
    public function updatePDF($id, $pdfPath) {
        $sql = "UPDATE {$this->table} SET pdf_path = :pdf_path WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':pdf_path' => $pdfPath, ':id' => $id]);
    }

    /**
     * Aggiorna lo stato dell'ordine
     */
    public function updateStatus($id, $status) {
        // fetch old status
        $old = $this->db->prepare("SELECT status FROM {$this->table} WHERE id = :id LIMIT 1");
        $old->execute([':id' => $id]);
        $prev = $old->fetchColumn();
        if ($prev === $status) return true; // nothing to do
        $sql = "UPDATE {$this->table} SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $res = $stmt->execute([':status' => $status, ':id' => $id]);
        if ($res) {
            $this->logStatusChange($id, $prev, $status);
        }
        return $res;
    }

    /** Update order meta fields (discount, supplier_reference) */
    public function updateMeta(int $id, array $data): bool {
        $allowed = ['discount', 'supplier_reference', 'subtotal', 'total_discount_lines', 'total_discount_order', 'total_net'];
        $sets = [];
        $params = [':id' => $id];
        foreach ($allowed as $col) {
            if (array_key_exists($col, $data)) {
                $sets[] = "$col = :$col";
                $params[":$col"] = $data[$col];
            }
        }
        if (empty($sets)) return false;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    private function logStatusChange(int $order_id, ?string $old_status, string $new_status): void {
        try {
            $this->db->exec("CREATE TABLE IF NOT EXISTS purchase_order_status_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                purchase_order_id INT NOT NULL,
                branch_id INT NULL,
                old_status VARCHAR(30) NULL,
                new_status VARCHAR(30) NOT NULL,
                changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX (purchase_order_id),
                INDEX (branch_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            // Ensure branch_id column exists if old table
            $col = $this->db->query("SHOW COLUMNS FROM purchase_order_status_logs LIKE 'branch_id'")->fetch();
            if (!$col) {
                $this->db->exec("ALTER TABLE purchase_order_status_logs ADD COLUMN branch_id INT NULL AFTER purchase_order_id, ADD INDEX (branch_id)");
            }
            $branchId = $this->db->prepare("SELECT branch_id FROM {$this->table} WHERE id = :id LIMIT 1");
            $branchId->execute([':id' => $order_id]);
            $bVal = $branchId->fetchColumn();
            $stmt = $this->db->prepare("INSERT INTO purchase_order_status_logs (purchase_order_id, branch_id, old_status, new_status) VALUES (:oid, :bid, :old, :new)");
            $stmt->execute([':oid' => $order_id, ':bid' => $bVal, ':old' => $old_status, ':new' => $new_status]);
        } catch (Exception $e) {
            // swallow logging errors
        }
    }


    public function getItems(int $order_id): array {
        $sql = "
            SELECT 
                poi.*,
                p.name AS product_name,
                u.name AS unit_name
            FROM {$this->itemsTable} poi
            JOIN {$this->table} po          ON po.id = poi.purchase_order_id
            LEFT JOIN products p            ON p.id = poi.product_id
            LEFT JOIN units u               ON u.id = poi.unit_id
            LEFT JOIN supplier_products sp  ON sp.product_id = poi.product_id
                                           AND sp.supplier_id = po.supplier_id
            WHERE poi.purchase_order_id = :order_id
            ORDER BY poi.id ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':order_id' => $order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Singola riga (item) con join utili
     */
    public function findItem(int $item_id): ?array {
        $sql = "
            SELECT 
                poi.*,
                p.name AS product_name,
                u.name AS unit_name,
                po.supplier_id
            FROM {$this->itemsTable} poi
            JOIN {$this->table} po          ON po.id = poi.purchase_order_id
            LEFT JOIN products p            ON p.id = poi.product_id
            LEFT JOIN units u               ON u.id = poi.unit_id
            LEFT JOIN supplier_products sp  ON sp.product_id = poi.product_id
                                           AND sp.supplier_id = po.supplier_id
            WHERE poi.id = :id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $item_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Aggiorna campi dell’item (price, discount, expiry_date, quantity opzionale)
     * Passa solo i campi che vuoi aggiornare in $data
     */
    public function updateItem(int $item_id, array $data): bool {
        // Ensure discount_type column exists
        static $checked = false;
        if (!$checked) {
            try {
                $col = $this->db->query("SHOW COLUMNS FROM {$this->itemsTable} LIKE 'discount_type'")->fetch();
                if (!$col) {
                    $this->db->exec("ALTER TABLE {$this->itemsTable} ADD COLUMN discount_type VARCHAR(10) NULL AFTER discount");
                }
            } catch (Exception $e) { /* ignore */ }
            $checked = true;
        }

        $allowed = ['price', 'discount', 'discount_type', 'expiry_date', 'quantity'];
        $sets = [];
        $params = [':id' => $item_id];

        foreach ($allowed as $col) {
            if (array_key_exists($col, $data)) {
                // expiry_date può essere NULL
                if ($col === 'expiry_date') {
                    if (empty($data[$col])) {
                        $sets[] = "expiry_date = NULL";
                    } else {
                        $sets[] = "expiry_date = :expiry_date";
                        $params[':expiry_date'] = $data[$col];
                    }
                } else {
                    $sets[] = "{$col} = :{$col}";
                    $params[":{$col}"] = $data[$col];
                }
            }
        }

        if (empty($sets)) {
            return false; // niente da aggiornare
        }

        $sql = "UPDATE {$this->itemsTable} SET " . implode(', ', $sets) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /** Ritorna i barcode associati all'ordine */
    public function getBarcodes(int $order_id): array {
        try {
            $exists = $this->db->query("SHOW TABLES LIKE 'purchase_order_barcodes'")->fetch();
            if (!$exists) return [];
            $sql = "SELECT * FROM purchase_order_barcodes WHERE purchase_order_id = :oid ORDER BY id ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':oid' => $order_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) { return []; }
    }

    /** Numero barcode per ordine */
    public function countBarcodes(int $order_id): int {
        try {
            $exists = $this->db->query("SHOW TABLES LIKE 'purchase_order_barcodes'")->fetch();
            if (!$exists) return 0;
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM purchase_order_barcodes WHERE purchase_order_id = :oid");
            $stmt->execute([':oid' => $order_id]);
            return (int)$stmt->fetchColumn();
        } catch (Exception $e) { return 0; }
    }

}

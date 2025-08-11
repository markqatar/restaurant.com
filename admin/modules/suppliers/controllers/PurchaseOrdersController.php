<?php
require_once admin_module_path('/models/PurchaseOrder.php', 'suppliers');

class PurchaseOrdersController
{
    private $model;
    private $currentPoId = 0; // context for barcode persistence

    public function __construct()
    {
        $this->model = new PurchaseOrder();
    // Load module translations
    TranslationManager::loadModuleTranslations('suppliers');
    }

    public function index()
    {
    $page_title = TranslationManager::t('purchase_order.list_title');
        include admin_module_path('/views/purchase_orders/index.php', 'suppliers');
    }

    public function datatable()
    {
    $export = $_GET['export'] ?? $_POST['export'] ?? null;
    $draw = intval($_POST['draw'] ?? 1);
    $start = intval($_POST['start'] ?? 0);
    $length = intval($_POST['length'] ?? 10);
    $search = sanitize_input($_GET['search'] ?? ($_POST['search']['value'] ?? ''));

        if($export){ $start=0; $length=1000000; }
        $data = $this->model->datatable($start, $length, $search);
        if($export){
            require_once get_setting('base_path').'includes/export.php';
            $rowsExport=[]; foreach($data['data'] as $row){ $rowsExport[]=[ $row['id'], $row['supplier_name'] ?? '', $row['status'], $row['currency'], $row['created_at'] ]; }
            $headers=['ID','Supplier','Status','Currency','Created'];
            if($export==='csv') export_csv('purchase_orders.csv',$headers,$rowsExport); else export_pdf('purchase_orders.pdf','Purchase Orders',$headers,$rowsExport);
        }

        echo json_encode([
            'draw' => $draw,
            'recordsTotal' => $data['total'],
            'recordsFiltered' => $data['filtered'],
            'data' => $data['data']
        ]);
    }

    public function view($id)
    {
        $order = $this->model->find((int)$id);
        if (!$order) {
            send_notification(TranslationManager::t('purchase_order.msg.not_found'), 'danger');
            redirect(get_setting('site_url') . '/admin/suppliers/purchaseorders');
        }
        include admin_module_path('/views/purchase_orders/view.php', 'suppliers');
    }

    public function create()
    {
        $suppliers = $this->model->getSuppliers(); // Per Select2
        include admin_module_path('/views/purchase_orders/create.php', 'suppliers');
    }

    public function edit($id)
    {
        $order = $this->model->find((int)$id);
        if (!$order) {
            send_notification(TranslationManager::t('purchase_order.msg.not_found'), 'danger');
            redirect(get_setting('site_url') . '/admin/suppliers/purchaseorders');
        }
        // Allow editing only in draft status
        if ($order['status'] !== 'draft') {
            redirect(get_setting('site_url') . '/admin/suppliers/purchaseorders/view/' . (int)$id);
        }
        $items = $order['items'] ?? [];
        include admin_module_path('/views/purchase_orders/edit.php', 'suppliers');
    }

    public function update($id)
    {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            echo json_encode(['success' => false, 'message' => TranslationManager::t('purchase_order.msg.invalid_token')]);
            return;
        }
        $order = $this->model->find((int)$id);
        if (!$order) {
            echo json_encode(['success' => false, 'message' => TranslationManager::t('purchase_order.msg.not_found')]);
            return;
        }
        if ($order['status'] !== 'draft') {
            echo json_encode(['success' => false, 'message' => TranslationManager::t('purchase_order.msg.not_receivable')]);
            return;
        }
    $products = $_POST['products'] ?? [];
    $quantities = $_POST['quantities'] ?? [];
    $units = $_POST['units'] ?? [];
    $prices = $_POST['prices'] ?? [];
    $currencies = $_POST['currencies'] ?? [];
    // Validate currencies
    $allowedCurrencies = [];
    $currSetting = get_setting('currencies') ?: (get_setting('currency') ?: 'QAR');
    foreach (preg_split('/[,\n]+/',$currSetting) as $c){ $c=trim($c); if($c!=='') $allowedCurrencies[] = strtoupper($c); }
    $allowedCurrencies = array_unique($allowedCurrencies);
    foreach($currencies as $c){ if($c!=='' && !in_array(strtoupper($c), $allowedCurrencies)){ echo json_encode(['success'=>false,'message'=>'Invalid currency: '.htmlspecialchars($c)]); return; } }
        if (empty($products)) {
            echo json_encode(['success' => false, 'message' => TranslationManager::t('purchase_order.msg.add_at_least_one_product')]);
            return;
        }
    $this->model->replaceItems((int)$id, $products, $quantities, $units, $prices, $currencies);
        echo json_encode(['success' => true, 'message' => TranslationManager::t('purchase_order.msg.updated_successfully')]);
    }

    public function store()
    {
        $supplier_id = (int)$_POST['supplier_id'];
        $branch_id = isset($_POST['branch_id']) ? (int)$_POST['branch_id'] : null;
        if ($branch_id && !validateBranchAccess($_SESSION['user_id'], $branch_id)) {
            echo json_encode(['success' => false, 'message' => TranslationManager::t('purchase_order.msg.invalid_branch')]);
            return;
        }
        if (!$branch_id) { // fallback to user default
            $branch_id = getDefaultBranchId($_SESSION['user_id']);
        }
        $discount = (float)($_POST['discount'] ?? 0);
        $notes = sanitize_input($_POST['notes']);

    $products = $_POST['products'] ?? [];
    $quantities = $_POST['quantities'] ?? [];
    $units = $_POST['units'] ?? [];
    $prices = $_POST['prices'] ?? [];
    $currencies = $_POST['currencies'] ?? [];
    // Validate currencies
    $allowedCurrencies = [];
    $currSetting = get_setting('currencies') ?: (get_setting('currency') ?: 'QAR');
    foreach (preg_split('/[,\n]+/',$currSetting) as $c){ $c=trim($c); if($c!=='') $allowedCurrencies[] = strtoupper($c); }
    $allowedCurrencies = array_unique($allowedCurrencies);
    foreach($currencies as $c){ if($c!=='' && !in_array(strtoupper($c), $allowedCurrencies)){ echo json_encode(['success'=>false,'message'=>'Invalid currency: '.htmlspecialchars($c)]); return; } }
    // Determine order-level currency as first provided (or system default)
    $orderCurrency = null;
    if(!empty($currencies)) { $orderCurrency = preg_replace('/[^A-Z]/i','', $currencies[0]); }
    if(!$orderCurrency){ $orderCurrency = get_setting('currency') ?: 'QAR'; }

        if (empty($products)) {
            echo json_encode(['success' => false, 'message' => TranslationManager::t('purchase_order.msg.add_at_least_one_product')]);
            return;
        }

        $po_id = $this->model->create([
            'supplier_id' => $supplier_id,
            'branch_id' => $branch_id,
            'discount' => $discount,
            'notes' => $notes,
            'currency' => $orderCurrency
        ]);

        // Inserisci righe ordine
    $this->model->addItems($po_id, $products, $quantities, $units, $prices, $currencies);

    echo json_encode(['success' => true, 'id' => $po_id, 'message' => TranslationManager::t('purchase_order.msg.created_successfully')]);
    }

    /**
     * Return last purchased unit price & currency for a supplier/product across received or sent orders, fallback supplier_products price.
     * GET params: supplier_id, product_id
     */
    public function lastprice()
    {
        $supplier_id = (int)($_GET['supplier_id'] ?? 0);
        $product_id = (int)($_GET['product_id'] ?? 0);
        if(!$supplier_id || !$product_id){ echo json_encode(['success'=>false]); return; }
        $db = Database::getInstance()->getConnection();
        $price = null; $currency = null; $date = null;
        try {
            $sql = "SELECT poi.price, poi.currency, po.created_at AS created_at FROM purchase_order_items poi
                    JOIN purchase_orders po ON po.id = poi.purchase_order_id
                    WHERE po.supplier_id = :sid AND poi.product_id = :pid AND poi.price IS NOT NULL
                    AND po.status IN ('sent','received')
                    ORDER BY po.id DESC, poi.id DESC LIMIT 1";
            $st = $db->prepare($sql); $st->execute([':sid'=>$supplier_id, ':pid'=>$product_id]);
            $row = $st->fetch(PDO::FETCH_ASSOC);
            if($row){ $price=$row['price']; $currency=$row['currency']; $date=$row['created_at']; }
            if($price===null){
                // fallback to supplier_products table
                $st = $db->prepare("SELECT price, currency FROM supplier_products WHERE supplier_id=:sid AND product_id=:pid ORDER BY id DESC LIMIT 1");
                $st->execute([':sid'=>$supplier_id, ':pid'=>$product_id]);
                $row = $st->fetch(PDO::FETCH_ASSOC);
                if($row){ $price=$row['price']; $currency=$row['currency']; }
            }
        } catch(Exception $e){ /* ignore */ }
        echo json_encode(['success'=>true,'price'=>$price,'currency'=>$currency,'date'=>$date]);
    }

    public function send()
    {
        $id = (int)$_POST['id'];
        $order = $this->model->find($id);

        if (!$order) {
            echo json_encode(['success' => false, 'message' => TranslationManager::t('purchase_order.msg.not_found')]);
            return;
        }

        // Genera PDF
        $pdfPath = $this->generatePDF($order);
        $this->model->updatePDF($id, $pdfPath);

        // Aggiorna stato
        $this->model->updateStatus($id, 'sent');

        // Invia email se disponibile
        if (!empty($order['supplier_email'])) {
            $this->sendEmail($order['supplier_email'], $pdfPath);
        }

    echo json_encode(['success' => true, 'message' => TranslationManager::t('purchase_order.msg.sent_successfully')]);
    }

    // Allow resending email (does not change status or regenerate unless missing PDF)
    public function resend()
    {
        $id = (int)$_POST['id'];
        $order = $this->model->find($id);
        if (!$order) {
            echo json_encode(['success' => false, 'message' => TranslationManager::t('purchase_order.msg.not_found')]);
            return;
        }
        // Regenerate PDF if path missing
        $pdfPath = $order['pdf_path'];
        if (empty($pdfPath)) {
            $pdfPath = $this->generatePDF($order);
            $this->model->updatePDF($id, $pdfPath);
        }
        if (!empty($order['supplier_email'])) {
            $this->sendEmail($order['supplier_email'], $pdfPath, true);
        }
        echo json_encode(['success' => true, 'message' => TranslationManager::t('purchase_order.msg.resent_successfully')]);
    }

    public function receive($order_id)
    {
        $order = $this->model->find((int)$order_id);
        if (!$order) {
            send_notification(TranslationManager::t('purchase_order.msg.not_found'), 'danger');
            redirect(get_setting('site_url', 'http://localhost') . '/admin/suppliers/purchaseorders');
        }

        // Solo se lo stato lo consente (bozza o inviato)
        if (!in_array($order['status'], ['sent', 'draft'])) {
            send_notification(TranslationManager::t('purchase_order.msg.not_receivable'), 'warning');
            redirect(get_setting('site_url', 'http://localhost') . '/admin/suppliers/purchaseorders');
        }

        $items = $this->model->getItems($order_id); // con nome prodotto, SKU, qty ordinate ecc.

    $page_title = TranslationManager::t('purchase_order.receive_title') . ' #' . $order_id;
        include admin_module_path('/views/purchase_orders/receive.php', 'suppliers'); // vedi sotto
    }

    /**
     * Mark order as received via direct AJAX (from detail view button) using only order id.
     */
    public function receiveDirect()
    {
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) { echo json_encode(['success' => false, 'message' => TranslationManager::t('purchase_order.msg.not_found')]); return; }
        $order = $this->model->find($id);
        if (!$order) { echo json_encode(['success' => false, 'message' => TranslationManager::t('purchase_order.msg.not_found')]); return; }
        if (!in_array($order['status'], ['sent','draft'])) {
            echo json_encode(['success' => false, 'message' => TranslationManager::t('purchase_order.msg.not_receivable')]); return;
        }
    // Auto-inventory increment: assume ordered quantity fully received
    $this->incrementInventoryForOrder($id, null, true);
    $this->model->updateStatus($id, 'received');
        echo json_encode(['success' => true, 'message' => TranslationManager::t('purchase_order.msg.received_successfully')]);
    }

    public function receiveSubmit()
    {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            echo json_encode(['success' => false, 'message' => TranslationManager::t('purchase_order.msg.invalid_token')]);
            exit;
        }

        $order_id = (int)$_POST['order_id'];
    $order = $this->model->find($order_id);
    if(!$order){ echo json_encode(['success'=>false,'message'=>TranslationManager::t('purchase_order.msg.not_found')]); return; }
    if (!in_array($order['status'], ['sent','draft'])) { echo json_encode(['success'=>false,'message'=>TranslationManager::t('purchase_order.msg.not_receivable')]); return; }
    // Set current PO id for barcode persistence context
    $this->currentPoId = $order_id;
        $prices   = $_POST['price'] ?? [];     // price[item_id] => 12.34
        $discount = $_POST['discount'] ?? [];  // discount[item_id] => 1.00
    $discountType = $_POST['discount_type'] ?? []; // discount_type[item_id] => val|pct
        $expiry   = $_POST['expiry'] ?? [];    // expiry[item_id] => yyyy-mm-dd
        $barcodes = $_POST['gen_barcode'] ?? []; // gen_barcode[item_id] => "1" se spuntato
        $qtys     = $_POST['received_qty'] ?? []; // quantità ricevuta effettiva per stampa barcode
    $supplier_reference = sanitize_input($_POST['supplier_reference'] ?? '');
    $order_discount     = isset($_POST['order_discount']) ? (float)$_POST['order_discount'] : null;
    $lineTotals         = $_POST['line_total'] ?? [];

        // Preload items to check expiry requirements
        $orderItems = [];
        try {
            $stmtChk = Database::getInstance()->getConnection()->prepare("SELECT poi.id AS id, p.requires_expiry AS requires_expiry FROM purchase_order_items poi JOIN products p ON p.id = poi.product_id WHERE poi.purchase_order_id = :oid");
            $stmtChk->execute([':oid' => $order_id]);
            foreach ($stmtChk->fetchAll(PDO::FETCH_ASSOC) as $row) { $orderItems[$row['id']] = $row; }
        } catch (Exception $e) { /* ignore */ }

        // Expiry validation (required + format)
        foreach ($orderItems as $iid => $row) {
            $raw = trim($expiry[$iid] ?? '');
            if (!empty($row['requires_expiry']) && $raw === '') {
                echo json_encode(['success' => false, 'message' => TranslationManager::t('purchase_order.validation.expiry_required')]);
                return;
            }
            if ($raw !== '') {
                if (!preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $raw)) {
                    echo json_encode(['success' => false, 'message' => TranslationManager::t('purchase_order.validation.expiry_invalid_format')]);
                    return;
                }
                [$yy,$mm,$dd] = explode('-', $raw);
                if (!checkdate((int)$mm,(int)$dd,(int)$yy)) {
                    echo json_encode(['success' => false, 'message' => TranslationManager::t('purchase_order.validation.expiry_invalid_format')]);
                    return;
                }
            }
        }

        // Aggiorna riga per riga
        $subtotal = 0; $lineDiscountTotal = 0;
        foreach ($prices as $item_id => $p) {
            $priceVal = ($p === '' ? null : (float)$p);
            $discVal = (isset($discount[$item_id]) && $discount[$item_id] !== '' ? (float)$discount[$item_id] : null);
            $dataUpdate = [
                'price'       => $priceVal,
                'discount'    => $discVal,
                'expiry_date' => ($expiry[$item_id] ?? null),
            ];
            if (isset($discountType[$item_id])) {
                $dataUpdate['discount_type'] = in_array($discountType[$item_id], ['val','pct']) ? $discountType[$item_id] : 'val';
            }
            $this->model->updateItem((int)$item_id, $dataUpdate);
            $qtyReceived = isset($qtys[$item_id]) ? (float)$qtys[$item_id] : 0; // received qty used for totals
            if ($priceVal !== null) {
                $lineTotalCalc = $priceVal * $qtyReceived;
                $subtotal += $lineTotalCalc;
                if ($discVal !== null) {
                    $lineDiscountTotal += ($dataUpdate['discount_type'] ?? 'val') === 'pct'
                        ? $lineTotalCalc * ($discVal/100)
                        : $discVal;
                }
            }

            // Genera barcode se richiesto (nuova logica: codifica supplier + product + expiry + progressivo)
            if (!empty($barcodes[$item_id])) {
                $row = $this->model->findItem((int)$item_id);
                $qtyToPrint = max(0, (int)($qtys[$item_id] ?? $row['quantity'] ?? 0));
                if ($qtyToPrint > 0) {
                    $expiryVal = trim($expiry[$item_id] ?? '');
                    $supplierId = (int)($row['supplier_id'] ?? 0);
                    $productId  = (int)($row['product_id'] ?? 0);
                    $this->generateBarcodesAdvanced($order_id, $supplierId, $productId, $expiryVal, $qtyToPrint);
                }
            }
        }

        // Aggiorna metadati ordine
        $meta = [];
        if ($supplier_reference !== '') $meta['supplier_reference'] = $supplier_reference;
        if ($order_discount !== null) $meta['discount'] = $order_discount;
        if ($subtotal > 0) {
            $meta['subtotal'] = $subtotal;
            $meta['total_discount_lines'] = $lineDiscountTotal;
            $meta['total_discount_order'] = isset($order_discount) ? ($subtotal - $lineDiscountTotal) * ($order_discount/100) : 0;
            $meta['total_net'] = $subtotal - $lineDiscountTotal - $meta['total_discount_order'];
        }
        // Handle supplier invoice PDF upload (optional)
        if(isset($_FILES['supplier_invoice_pdf']) && is_uploaded_file($_FILES['supplier_invoice_pdf']['tmp_name'])){
            $uplDir = get_setting('base_path').'storage/supplier_invoices/';
            if(!is_dir($uplDir)) mkdir($uplDir,0777,true);
            $ext = strtolower(pathinfo($_FILES['supplier_invoice_pdf']['name'], PATHINFO_EXTENSION));
            if($ext==='pdf'){
                $fname = 'PO-'.$order_id.'-'.date('YmdHis').'.pdf';
                $dest  = $uplDir.$fname;
                if(move_uploaded_file($_FILES['supplier_invoice_pdf']['tmp_name'],$dest)){
                    $meta['supplier_invoice_pdf'] = str_replace(get_setting('base_path'),'',$dest);
                }
            }
        }
        if (!empty($meta)) {
            if (method_exists($this->model, 'updateMeta')) {
                $this->model->updateMeta($order_id, $meta);
            }
        }

        $this->model->updateStatus($order_id, 'received');
        // Inventory increment using received quantities (fallback to ordered if missing)
        $this->incrementInventoryForOrder($order_id, $qtys, false);
    echo json_encode(['success' => true, 'message' => TranslationManager::t('purchase_order.msg.received_successfully')]);
    }

    /**
     * Increment product inventory for a purchase order when received.
     * @param int $order_id
     * @param array|null $receivedQtys keyed by item_id => qty (optional)
     * @param bool $useOrderedIfEmpty when true, use ordered quantity (for direct receive)
     */
    private function incrementInventoryForOrder(int $order_id, ?array $receivedQtys = null, bool $useOrderedIfEmpty = false): void {
        $items = $this->model->getItems($order_id);
        if (empty($items)) return;
        // Load centralized Inventory model (suppliers)
        $inv = null;
        try {
            $invPath = get_setting('base_path') . 'admin/modules/warehouse/models/Inventory.php';
            if (is_file($invPath)) { require_once $invPath; $inv = new Inventory(); }
        } catch (Exception $e) { $inv = null; }
        if(!$inv || !method_exists($inv,'adjust')) return; // inventory system unavailable
    // Load centralized reasons
    $reasons = [];
    $reasonsPath = get_setting('base_path') . 'admin/modules/suppliers/config/inventory_reasons.php';
    if(is_file($reasonsPath)) { $reasons = require $reasonsPath; }
    $reasonReceive = $reasons['PO_RECEIVE'] ?? 'po_receive';
        // Determine branch_id from order (assumes model getById or similar accessor exists)
        $branch_id = null;
        if(method_exists($this->model,'find')){
            try { $order = $this->model->find($order_id); if($order && isset($order['branch_id'])) $branch_id = (int)$order['branch_id']; } catch(Exception $e){ $branch_id=null; }
        }
        foreach ($items as $it) {
            $qty = null;
            if ($receivedQtys !== null && isset($receivedQtys[$it['id']]) && $receivedQtys[$it['id']] !== '') {
                $qty = (float)$receivedQtys[$it['id']];
            } elseif ($useOrderedIfEmpty) {
                $qty = (float)$it['quantity'];
            }
            if ($qty === null || $qty <= 0) continue;
            try {
                $inv->adjust('product', (int)$it['product_id'], (float)$qty, $it['unit_id'] ? (int)$it['unit_id'] : null, $reasonReceive, 'PO-' . $order_id, $branch_id);
            } catch (Exception $e) { /* ignore inventory failures */ }
        }
    }

    private function generatePDF($order)
    {
        require_once get_setting('base_path') . 'vendor/autoload.php';
        $options = new Dompdf\Options(); $options->set('isRemoteEnabled', true); 
        $dompdf = new Dompdf\Dompdf($options);
        $logoPath = get_setting('logo_path', 'default.png');
        $logoFs = get_setting('base_path') . 'public/assets/images/logo/' . $logoPath;
        if (!is_file($logoFs)) {
            $logoFs = get_setting('base_path') . 'public/assets/images/logo/default.png';
        }
        $logoDataUri = '';
        if (is_file($logoFs)) {
            $mime = 'image/' . strtolower(pathinfo($logoFs, PATHINFO_EXTENSION));
            $data = @file_get_contents($logoFs);
            if ($data !== false) {
                $logoDataUri = 'data:' . $mime . ';base64,' . base64_encode($data);
            }
        }
        $date = date('d/m/Y');
        // Localized labels
        $title = TranslationManager::t('purchase_order.pdf.title');
    $lblSupplier = TranslationManager::t('purchase_order.field.supplier');
    $lblBranch = TranslationManager::t('branch.branch_name');
        $lblDate = TranslationManager::t('purchase_order.field.date');
        $lblStatus = TranslationManager::t('purchase_order.field.status');
        $lblProduct = TranslationManager::t('purchase_order.field.product');
        $lblQty = TranslationManager::t('purchase_order.field.quantity');
        $lblUnit = TranslationManager::t('purchase_order.field.unit');
        $footerText = TranslationManager::t('purchase_order.pdf.footer');

        $html = "
        <style>
            body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
            .header { text-align: center; margin-bottom: 20px; }
            .header img { max-height: 50px; }
            h2 { color: #444; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
            th { background: #f4f4f4; }
            .footer { text-align: center; margin-top: 40px; font-size: 11px; color: #777; }
        </style>

        <div class='header'>
            <img src='{$logoDataUri}' alt='Logo'>
            <h2>{$title} #{$order['id']}</h2>
        </div>

    <p><strong>{$lblSupplier}:</strong> {$order['supplier_name']}<br>
       <strong>{$lblBranch}:</strong> " . htmlspecialchars($order['branch_name'] ?? '') . "<br>
           <strong>{$lblDate}:</strong> {$date}<br>
           <strong>{$lblStatus}:</strong> " . strtoupper(TranslationManager::t('purchase_order.status.' . $order['status'])) . "</p>

        <table>
            <thead>
                <tr>
                    <th>{$lblProduct}</th>
                    <th>{$lblQty}</th>
                    <th>{$lblUnit}</th>
                </tr>
            </thead>
            <tbody>";

        foreach ($order['items'] as $item) {
            $html .= "<tr>
                    <td>{$item['product_name']}</td>
                    <td>{$item['quantity']}</td>
                    <td>{$item['unit_name']}</td>
                  </tr>";
        }

    $html .= "</tbody></table>
    <div class='footer'>{$footerText}</div>";

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdfDir = get_setting('base_path') . "storage/purchase_orders/";
        if (!is_dir($pdfDir)) {
            mkdir($pdfDir, 0777, true);
        }
        $filePath = $pdfDir . "PO-{$order['id']}.pdf";
        file_put_contents($filePath, $dompdf->output());

        return str_replace(get_setting('base_path'), get_setting('site_url') . '/', $filePath);
    }

    private function sendEmail($email, $pdfPath, $isResend = false)
    {
        require_once get_setting('base_path') . 'vendor/autoload.php';
        $smtp = @include get_setting('base_path') . 'config/smtp.php';
        if (!is_array($smtp)) { $smtp = []; }

        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP();
        $mail->Host = $smtp['host'] ?? 'localhost';
        $mail->SMTPAuth = true;
        $mail->Username = $smtp['username'] ?? '';
        $mail->Password = $smtp['password'] ?? '';
        $mail->SMTPSecure = $smtp['encryption'] ?? 'tls';
        $mail->Port = (int)($smtp['port'] ?? 587);

        $fromEmail = $smtp['from_email'] ?? 'no-reply@example.com';
        $fromName  = $smtp['from_name'] ?? 'Purchasing';
        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($email);

        // Localized subject & body
    $subjectTpl = $isResend ? TranslationManager::t('purchase_order.email.subject_resend') : TranslationManager::t('purchase_order.email.subject');
        $mail->Subject = str_replace('{order}', basename($pdfPath, '.pdf'), $subjectTpl);

        // Embed logo as base64
        $logoPath = get_setting('base_path') . 'public/assets/images/logo/' . get_setting('logo_path', 'default.png');
        if (!is_file($logoPath)) {
            $logoPath = get_setting('base_path') . 'public/assets/images/logo/default.png';
        }
        $logoDataUri = '';
        if (is_file($logoPath)) {
            $mime = 'image/' . strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
            $data = @file_get_contents($logoPath);
            if ($data !== false) {
                $logoDataUri = 'data:' . $mime . ';base64,' . base64_encode($data);
            }
        }
        $downloadLink = $pdfPath;

        $greeting = TranslationManager::t('purchase_order.email.greeting');
        $bodyIntro = TranslationManager::t('purchase_order.email.intro');
        $btnText = TranslationManager::t('purchase_order.email.download_button');
        $thanks = TranslationManager::t('purchase_order.email.thanks');
        $signature = TranslationManager::t('purchase_order.email.signature');

        $mail->isHTML(true);
        $mail->Body = "<div style='font-family: Arial, sans-serif; font-size: 14px; color: #333;'>
            <img src='{$logoDataUri}' alt='Logo' style='max-width:150px; margin-bottom:20px;'>
            <h2 style='color:#444;'>{$greeting}</h2>
            <p>{$bodyIntro}</p>
            <p style='text-align:center;'>
                <a href='{$downloadLink}' style='background:#4CAF50;color:#fff;padding:10px 20px;text-decoration:none;border-radius:5px;'>
                    {$btnText}
                </a>
            </p>
            <p>{$thanks}<br>{$signature}</p>
        </div>";

        $mail->addAttachment(str_replace(get_setting('site_url') . '/', get_setting('base_path'), $pdfPath));
        return $mail->send();
    }

    /**
     * Genera e salva barcode pattern: SUP-{po}-{supplier}-{product}-{yyyymmdd|-}-{NN}
     * NN continua per stessa combinazione.
     */
    private function generateBarcodesAdvanced(int $orderId, int $supplierId, int $productId, string $expiry, int $qty)
    {
        require_once get_setting('base_path') . 'vendor/autoload.php';
        $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
        $barcodeDir = get_setting('base_path') . "storage/barcodes/";
        if (!is_dir($barcodeDir)) { mkdir($barcodeDir, 0777, true); }
        // Ensure persistence table exists
        try {
            $db = Database::getInstance()->getConnection();
            $db->exec("CREATE TABLE IF NOT EXISTS purchase_order_barcodes (\n                id INT AUTO_INCREMENT PRIMARY KEY,\n                purchase_order_id INT NOT NULL,\n                supplier_id INT NOT NULL,\n                product_id INT NOT NULL,\n                expiry_date DATE NULL,\n                code VARCHAR(190) NOT NULL,\n                file_path VARCHAR(255) NULL,\n                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,\n                INDEX (purchase_order_id), INDEX (product_id), INDEX (supplier_id), INDEX (expiry_date), UNIQUE KEY uniq_code (code)\n            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        } catch (Exception $e) { /* ignore */ }
    $expPart = '';
    if ($expiry) { $ts = strtotime($expiry); if ($ts) { $expPart = date('Ymd', $ts); } }
    if (!$expPart) { $expPart = '-'; }
    $db = Database::getInstance()->getConnection();
    // Ensure quantity column exists
    try { $db->exec("ALTER TABLE purchase_order_barcodes ADD COLUMN quantity INT NOT NULL DEFAULT 1"); } catch(Exception $e) { /* ignore */ }
    // Unique key per combination; code deterministic without sequence
    $baseCode = sprintf('SUP-%d-%d-%d-%s', $orderId, $supplierId, $productId, $expPart ?: '-');
    // Insert or update quantity
    $stmtSel = $db->prepare("SELECT id, quantity, file_path FROM purchase_order_barcodes WHERE code=:code LIMIT 1");
    $stmtSel->execute([':code'=>$baseCode]);
    $existing = $stmtSel->fetch(PDO::FETCH_ASSOC);
    // Fetch product & supplier names once for label overlay
    $pname=''; $sname='';
    try { $pname = $db->query("SELECT name FROM products WHERE id=".(int)$productId)->fetchColumn() ?: ''; } catch(Exception $e) {}
    try { $sname = $db->query("SELECT name FROM suppliers WHERE id=".(int)$supplierId)->fetchColumn() ?: ''; } catch(Exception $e) {}
    if($existing){
        // Update quantity sum
        $newQty = (int)$existing['quantity'] + $qty;
        $stmtUpd = $db->prepare("UPDATE purchase_order_barcodes SET quantity=:q WHERE id=:id");
        $stmtUpd->execute([':q'=>$newQty, ':id'=>$existing['id']]);
        return; // image already exists
    }
    // Create new image once
    $pngRaw = $generator->getBarcode($baseCode, $generator::TYPE_CODE_128, 2, 60);
    $img = imagecreatefromstring($pngRaw);
    if($img){
        $w = imagesx($img); $h = imagesy($img); $extraH = 55;
        $canvas = imagecreatetruecolor($w, $h+$extraH);
        $white = imagecolorallocate($canvas,255,255,255); $black=imagecolorallocate($canvas,0,0,0);
        imagefilledrectangle($canvas,0,0,$w,$h+$extraH,$white);
        $line1 = mb_substr($pname,0,40);
        $line2 = 'EXP: '.($expiry?:'-').'  PO#'.$orderId;
        $line3 = 'SUP: '.mb_substr($sname,0,25);
        $line4 = $baseCode;
        $y=12; imagestring($canvas,2,2,$y,$line1,$black);
        $y+=12; imagestring($canvas,2,2,$y,$line2,$black);
        $y+=12; imagestring($canvas,2,2,$y,$line3,$black);
        $y+=12; imagestring($canvas,2,2,$y,$line4,$black);
        imagecopy($canvas,$img,0,$extraH,0,0,$w,$h);
        $filePath = $barcodeDir . $baseCode . '.png';
        imagepng($canvas,$filePath,9);
        imagedestroy($img); imagedestroy($canvas);
    } else {
        $filePath = $barcodeDir . $baseCode . '.png';
        file_put_contents($filePath,$pngRaw);
    }
    try {
        $stmt = $db->prepare("INSERT INTO purchase_order_barcodes (purchase_order_id,supplier_id,product_id,expiry_date,code,file_path,quantity) VALUES (:po,:sid,:pid,:exp,:code,:file,:q)");
        $stmt->execute([
            ':po'=>$orderId,
            ':sid'=>$supplierId,
            ':pid'=>$productId,
            ':exp'=> ($expiry && $expiry!=='' ? (date('Y-m-d', strtotime($expiry)) ?: null) : null),
            ':code'=>$baseCode,
            ':file'=> str_replace(get_setting('base_path'),'',$filePath),
            ':q'=>$qty
        ]);
    } catch(Exception $e){ /* ignore */ }
    }

    /** Stampa semplice dei barcode (griglia) */
    public function barcodes($order_id)
    {
        $order = $this->model->find((int)$order_id);
        if (!$order) {
            send_notification(TranslationManager::t('purchase_order.msg.not_found'), 'danger');
            redirect(get_setting('site_url') . '/admin/suppliers/purchaseorders');
        }
        $barcodes = $this->model->getBarcodes((int)$order_id);
        include admin_module_path('/views/purchase_orders/barcodes.php', 'suppliers');
    }

    public function barcodesJson($order_id)
    {
        $order = $this->model->find((int)$order_id);
        if (!$order) { echo json_encode(['success'=>false,'message'=>TranslationManager::t('purchase_order.msg.not_found')]); return; }
        $list = $this->model->getBarcodes((int)$order_id);
        $base = rtrim(get_setting('site_url'),'/');
        foreach ($list as &$b) { $b['url'] = $base . '/' . ltrim($b['file_path'],'/'); }
        echo json_encode(['success'=>true,'data'=>$list]);
    }

    public function barcodeRegenerate()
    {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) { echo json_encode(['success'=>false,'message'=>TranslationManager::t('purchase_order.msg.invalid_token')]); return; }
        $orderId = (int)($_POST['order_id'] ?? 0);
        $productId = (int)($_POST['product_id'] ?? 0);
        $qty = (int)($_POST['qty'] ?? 0);
        $expiry = trim($_POST['expiry'] ?? '');
        if (!$orderId || !$productId || $qty <= 0) { echo json_encode(['success'=>false,'message'=>TranslationManager::t('purchase_order.barcode.invalid_params')]); return; }
        if ($expiry !== '') {
            if (!preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $expiry)) { echo json_encode(['success'=>false,'message'=>TranslationManager::t('purchase_order.validation.expiry_invalid_format')]); return; }
            [$yy,$mm,$dd] = explode('-', $expiry);
            if (!checkdate((int)$mm,(int)$dd,(int)$yy)) { echo json_encode(['success'=>false,'message'=>TranslationManager::t('purchase_order.validation.expiry_invalid_format')]); return; }
        }
        $order = $this->model->find($orderId);
        if (!$order) { echo json_encode(['success'=>false,'message'=>TranslationManager::t('purchase_order.msg.not_found')]); return; }
        $supplierId = (int)$order['supplier_id'];
        $this->generateBarcodesAdvanced($orderId, $supplierId, $productId, $expiry, $qty);
        $count = $this->model->countBarcodes($orderId);
        echo json_encode(['success'=>true,'message'=>TranslationManager::t('purchase_order.barcode.regenerated'),'total'=>$count]);
    }
}

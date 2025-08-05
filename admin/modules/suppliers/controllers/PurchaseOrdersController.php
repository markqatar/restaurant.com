<?php
require_once admin_module_path('/models/PurchaseOrder.php', 'suppliers');

class PurchaseOrdersController
{
    private $model;

    public function __construct()
    {
        $this->model = new PurchaseOrder();
    }

    public function index()
    {
        $page_title = "Purchase Orders";
        include admin_module_path('/views/purchase_orders/index.php', 'suppliers');
    }

    public function datatable()
    {
        $draw = intval($_POST['draw'] ?? 1);
        $start = intval($_POST['start'] ?? 0);
        $length = intval($_POST['length'] ?? 10);
        $search = sanitize_input($_POST['search']['value'] ?? '');

        $data = $this->model->datatable($start, $length, $search);

        echo json_encode([
            'draw' => $draw,
            'recordsTotal' => $data['total'],
            'recordsFiltered' => $data['filtered'],
            'data' => $data['data']
        ]);
    }

    public function create()
    {
        $suppliers = $this->model->getSuppliers(); // Per Select2
        include admin_module_path('/views/purchase_orders/create.php', 'suppliers');
    }

    public function store()
    {
        $supplier_id = (int)$_POST['supplier_id'];
        $discount = (float)($_POST['discount'] ?? 0);
        $notes = sanitize_input($_POST['notes']);

        $products = $_POST['products'] ?? [];
        $quantities = $_POST['quantities'] ?? [];
        $units = $_POST['units'] ?? [];

        if (empty($products)) {
            echo json_encode(['success' => false, 'message' => 'Aggiungi almeno un prodotto']);
            return;
        }

        $po_id = $this->model->create([
            'supplier_id' => $supplier_id,
            'discount' => $discount,
            'notes' => $notes
        ]);

        // Inserisci righe ordine
        $this->model->addItems($po_id, $products, $quantities, $units);

        echo json_encode(['success' => true, 'id' => $po_id]);
    }

    public function send()
    {
        $id = (int)$_POST['id'];
        $order = $this->model->find($id);

        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Ordine non trovato']);
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

        echo json_encode(['success' => true]);
    }

    public function receive()
    {
        $id = (int)$_POST['id'];
        $this->model->updateStatus($id, 'received');

        // Qui generiamo i barcode per i prodotti con checkbox barcode = 1
        $this->generateBarcodes($id);

        echo json_encode(['success' => true]);
    }

    private function generatePDF($order)
    {
        require_once get_setting('base_path') . 'vendor/autoload.php';
        $dompdf = new Dompdf\Dompdf();

        $logoUrl = get_setting('site_url') . "/assets/logo.png"; // Cambia con il percorso reale
        $date = date('d/m/Y');

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
        <img src='{$logoUrl}' alt='Logo'>
        <h2>Ordine d'Acquisto #{$order['id']}</h2>
    </div>

    <p><strong>Fornitore:</strong> {$order['supplier_name']}<br>
       <strong>Data:</strong> {$date}<br>
       <strong>Stato:</strong> " . strtoupper($order['status']) . "</p>

    <table>
        <thead>
            <tr>
                <th>Prodotto</th>
                <th>Quantità</th>
                <th>Unità</th>
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
    <div class='footer'>Documento generato automaticamente - Non rispondere a questa email</div>";

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

    private function sendEmail($email, $pdfPath)
    {
        require_once get_setting('base_path') . 'vendor/autoload.php';
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.example.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@example.com';
        $mail->Password = 'password';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('your-email@example.com', 'Ufficio Acquisti');
        $mail->addAddress($email);
        $mail->Subject = 'Ordine d\'Acquisto #' . basename($pdfPath, '.pdf');

        $logoUrl = get_setting('site_url') . "/assets/logo.png";
        $downloadLink = $pdfPath;

        $mail->isHTML(true);
        $mail->Body = "
        <div style='font-family: Arial, sans-serif; font-size: 14px; color: #333;'>
            <img src='{$logoUrl}' alt='Logo' style='max-width:150px; margin-bottom:20px;'>
            <h2 style='color:#444;'>Gentile Fornitore,</h2>
            <p>in allegato trova il nostro ordine d'acquisto. È possibile scaricarlo cliccando sul pulsante qui sotto:</p>
            <p style='text-align:center;'>
                <a href='{$downloadLink}' style='background:#4CAF50;color:#fff;padding:10px 20px;text-decoration:none;border-radius:5px;'>
                    Scarica Ordine
                </a>
            </p>
            <p>Grazie per la collaborazione,<br>Ufficio Acquisti</p>
        </div>
    ";

        $mail->addAttachment(str_replace(get_setting('site_url') . '/', get_setting('base_path'), $pdfPath));
        return $mail->send();
    }

    private function generateBarcodes($orderId)
    {
        $order = $this->model->find($orderId);
        if (!$order) return;

        require_once get_setting('base_path') . 'vendor/autoload.php';
        $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
        $barcodeDir = get_setting('base_path') . "storage/barcodes/";

        if (!is_dir($barcodeDir)) {
            mkdir($barcodeDir, 0777, true);
        }

        foreach ($order['items'] as $item) {
            if ($item['requires_barcode']) {
                for ($i = 0; $i < $item['quantity']; $i++) {
                    $barcode = $generator->getBarcode($item['product_id'], $generator::TYPE_CODE_128);
                    $filePath = $barcodeDir . "barcode_{$item['product_id']}_$i.png";
                    file_put_contents($filePath, $barcode);

                    // Invio alla stampante termica (esempio via CUPS o comando di rete)
                    exec("lp -d Thermal_Printer " . escapeshellarg($filePath));
                }
            }
        }
    }
}

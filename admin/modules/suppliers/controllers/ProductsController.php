<?php
require_once admin_module_path('/models/Product.php');

class ProductsController
{
    private $product_model;

    public function __construct()
    {
        $this->product_model = new Product();
    }

    public function index()
    {
        if (!has_permission($_SESSION['user_id'], 'suppliers', 'view')) {
            redirect('admin/unauthorized.php');
        }
        $page_title = "Products";
        include admin_module_path('/views/products/index.php', 'suppliers');
    }

    public function datatable()
    {
        $draw = intval($_POST['draw'] ?? 1);
        $start = intval($_POST['start'] ?? 0);
        $length = intval($_POST['length'] ?? 10);
        $search = $_POST['search']['value'] ?? '';

        $data = $this->product_model->datatable($start, $length, $search);
        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => $data['total'],
            "recordsFiltered" => $data['filtered'],
            "data" => $data['data']
        ]);
    }

    public function store()
    {
        $id = $_POST['id'] ?? null;
        $data = [
            'name' => sanitize_input($_POST['name']),
            'sku' => sanitize_input($_POST['sku']),
            'description' => sanitize_input($_POST['description'])
        ];

        if ($id) {
            $this->product_model->update((int)$id, $data);
        } else {
            $this->product_model->create($data);
        }

        echo json_encode(['success' => true]);
    }

    public function get($id)
    {
        $product = $this->product_model->find((int)$id);
        echo json_encode(['success' => true, 'data' => $product]);
    }

    public function delete()
    {
        $id = (int)$_POST['id'];
        $this->product_model->delete($id);
        echo json_encode(['success' => true]);
    }

    public function select2()
    {
        $search = $_GET['search'] ?? '';
        $products = $this->product_model->searchByName($search);
        $results = [];
        foreach ($products as $p) {
            $results[] = [
                'id' => $p['id'],
                'text' => $p['name']
            ];
        }
        echo json_encode($results);
        exit;
    }

    public function associate($product_id)
    {
        if (!has_permission($_SESSION['user_id'], 'suppliers', 'view')) {
            redirect('admin/unauthorized.php');
        }

        $product_id = (int)$product_id;
        $product = $this->product_model->find($product_id);

        if (!$product) {
            redirect('admin/products');
        }

        $page_title = "Associa Fornitori - " . htmlspecialchars($product['name']);
        include admin_module_path('/views/products/associate.php', 'suppliers');
    }
}

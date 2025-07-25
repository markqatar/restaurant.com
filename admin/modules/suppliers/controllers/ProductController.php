<?php
require_once '../models/Product.php';
require_once '../includes/functions.php';

class ProductController {
    private $product_model;
    
    public function __construct() {
        $this->product_model = new Product();
    }
    
    // Display products list
    public function index() {
        if (!has_permission($_SESSION['user_id'], 'products', 'view')) {
            redirect('../admin/unauthorized.php');
        }
        
        $category_id = $_GET['category'] ?? null;
        $products = $this->product_model->read($category_id);
        $total_products = $this->product_model->count($category_id);
        
        $page_title = "Gestione Prodotti";
        include '../views/products/index.php';
    }
    
    // Show create product form
    public function create() {
        if (!has_permission($_SESSION['user_id'], 'products', 'create')) {
            redirect('../admin/unauthorized.php');
        }
        
        // Get categories for dropdown
        $categories = $this->getCategories();
        $recipes = $this->getRecipes();
        
        $page_title = "Nuovo Prodotto";
        include '../views/products/create.php';
    }
    
    // Store new product
    public function store() {
        if (!has_permission($_SESSION['user_id'], 'products', 'create')) {
            redirect('../admin/unauthorized.php');
        }
        
        if ($_POST) {
            // Verify CSRF token
            if (!verify_csrf_token($_POST['csrf_token'])) {
                send_notification('Token di sicurezza non valido', 'danger');
                redirect('products.php?action=create');
            }
            
            $data = [
                'name' => sanitize_input($_POST['name']),
                'name_ar' => sanitize_input($_POST['name_ar']),
                'description' => sanitize_input($_POST['description']),
                'description_ar' => sanitize_input($_POST['description_ar']),
                'category_id' => (int)$_POST['category_id'],
                'recipe_id' => !empty($_POST['recipe_id']) ? (int)$_POST['recipe_id'] : null,
                'price' => (float)$_POST['price'],
                'cost' => (float)($_POST['cost'] ?? 0),
                'barcode' => sanitize_input($_POST['barcode']),
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'show_on_website' => isset($_POST['show_on_website']) ? 1 : 0,
                'preparation_time' => (int)($_POST['preparation_time'] ?? 0),
                'sort_order' => (int)($_POST['sort_order'] ?? 0)
            ];
            
            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $image_name = upload_file($_FILES['image'], '../uploads/products/');
                if ($image_name) {
                    $data['image'] = $image_name;
                }
            }
            
            try {
                if ($this->product_model->create($data)) {
                    log_action($_SESSION['user_id'], 'create_product', 'Created product: ' . $data['name']);
                    send_notification('Prodotto creato con successo', 'success');
                    redirect('products.php');
                } else {
                    send_notification('Errore nella creazione del prodotto', 'danger');
                }
            } catch (Exception $e) {
                send_notification('Errore database: ' . $e->getMessage(), 'danger');
            }
            
            redirect('products.php?action=create');
        }
    }
    
    // Show edit product form
    public function edit($id) {
        if (!has_permission($_SESSION['user_id'], 'products', 'update')) {
            redirect('../admin/unauthorized.php');
        }
        
        $product = $this->product_model->readOne($id);
        
        if (!$product) {
            send_notification('Prodotto non trovato', 'danger');
            redirect('products.php');
        }
        
        $categories = $this->getCategories();
        $recipes = $this->getRecipes();
        
        $page_title = "Modifica Prodotto";
        include '../views/products/edit.php';
    }
    
    // Update product
    public function update($id) {
        if (!has_permission($_SESSION['user_id'], 'products', 'update')) {
            redirect('../admin/unauthorized.php');
        }
        
        if ($_POST) {
            // Verify CSRF token
            if (!verify_csrf_token($_POST['csrf_token'])) {
                send_notification('Token di sicurezza non valido', 'danger');
                redirect('products.php?action=edit&id=' . $id);
            }
            
            $data = [
                'name' => sanitize_input($_POST['name']),
                'name_ar' => sanitize_input($_POST['name_ar']),
                'description' => sanitize_input($_POST['description']),
                'description_ar' => sanitize_input($_POST['description_ar']),
                'category_id' => (int)$_POST['category_id'],
                'recipe_id' => !empty($_POST['recipe_id']) ? (int)$_POST['recipe_id'] : null,
                'price' => (float)$_POST['price'],
                'cost' => (float)($_POST['cost'] ?? 0),
                'barcode' => sanitize_input($_POST['barcode']),
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'show_on_website' => isset($_POST['show_on_website']) ? 1 : 0,
                'preparation_time' => (int)($_POST['preparation_time'] ?? 0),
                'sort_order' => (int)($_POST['sort_order'] ?? 0)
            ];
            
            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $image_name = upload_file($_FILES['image'], '../uploads/products/');
                if ($image_name) {
                    $data['image'] = $image_name;
                }
            }
            
            try {
                if ($this->product_model->update($id, $data)) {
                    log_action($_SESSION['user_id'], 'update_product', 'Updated product ID: ' . $id);
                    send_notification('Prodotto aggiornato con successo', 'success');
                    redirect('products.php');
                } else {
                    send_notification('Errore nell\'aggiornamento', 'danger');
                }
            } catch (Exception $e) {
                send_notification('Errore database: ' . $e->getMessage(), 'danger');
            }
        }
        
        redirect('products.php?action=edit&id=' . $id);
    }
    
    // Delete product
    public function delete($id) {
        if (!has_permission($_SESSION['user_id'], 'products', 'delete')) {
            redirect('../admin/unauthorized.php');
        }
        
        try {
            if ($this->product_model->delete($id)) {
                log_action($_SESSION['user_id'], 'delete_product', 'Deleted product ID: ' . $id);
                send_notification('Prodotto eliminato con successo', 'success');
            } else {
                send_notification('Errore nell\'eliminazione', 'danger');
            }
        } catch (Exception $e) {
            send_notification('Errore database: ' . $e->getMessage(), 'danger');
        }
        
        redirect('products.php');
    }
    
    // Toggle product status
    public function toggleStatus($id) {
        if (!has_permission($_SESSION['user_id'], 'products', 'update')) {
            redirect('../admin/unauthorized.php');
        }
        
        $product = $this->product_model->readOne($id);
        if ($product) {
            $new_status = $product['is_active'] ? 0 : 1;
            
            if ($this->product_model->updateStatus($id, $new_status)) {
                $status_text = $new_status ? 'attivato' : 'disattivato';
                send_notification("Prodotto $status_text con successo", 'success');
            }
        }
        
        redirect('products.php');
    }
    
    // Get categories for dropdown
    private function getCategories() {
        global $db;
        $stmt = $db->prepare("SELECT id, name FROM product_categories WHERE is_active = 1 ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get recipes for dropdown
    private function getRecipes() {
        global $db;
        $stmt = $db->prepare("SELECT id, name FROM recipes ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // AJAX search products
    public function search() {
        $keyword = $_GET['q'] ?? '';
        $category_id = $_GET['category'] ?? null;
        
        $products = $this->product_model->search($keyword, $category_id);
        
        header('Content-Type: application/json');
        echo json_encode($products);
        exit;
    }
}
?>
<?php
require_once '../models/Supplier.php';
require_once '../includes/functions.php';

class SupplierController {
    private $supplier_model;
    
    public function __construct() {
        $this->supplier_model = new Supplier();
    }
    
    // Display suppliers list
    public function index() {
        if (!has_permission($_SESSION['user_id'], 'suppliers', 'view')) {
            redirect('../admin/unauthorized.php');
        }
        
        $suppliers = $this->supplier_model->read();
        $total_suppliers = $this->supplier_model->count();
        
        $page_title = "Gestione Fornitori";
        include '../views/suppliers/index.php';
    }
    
    // Show create supplier form
    public function create() {
        if (!has_permission($_SESSION['user_id'], 'suppliers', 'create')) {
            redirect('../admin/unauthorized.php');
        }
        
        $countries = $this->supplier_model->getCountries();
        
        $page_title = "Nuovo Fornitore";
        include '../views/suppliers/create.php';
    }
    
    // Store new supplier
    public function store() {
        if (!has_permission($_SESSION['user_id'], 'suppliers', 'create')) {
            redirect('../admin/unauthorized.php');
        }
        
        if ($_POST) {
            // Verify CSRF token
            if (!verify_csrf_token($_POST['csrf_token'])) {
                send_notification('Token di sicurezza non valido', 'danger');
                redirect('suppliers.php?action=create');
            }
            
            // Validate input
            $errors = $this->validateSupplierData($_POST);
            
            if (empty($errors)) {
                $data = [
                    'name' => sanitize_input($_POST['name']),
                    'address_line1' => sanitize_input($_POST['address_line1']),
                    'address_line2' => sanitize_input($_POST['address_line2']),
                    'zip_code' => sanitize_input($_POST['zip_code']),
                    'country_id' => !empty($_POST['country_id']) ? (int)$_POST['country_id'] : null,
                    'city_id' => !empty($_POST['city_id']) ? (int)$_POST['city_id'] : null,
                    'tel1' => sanitize_input($_POST['tel1']),
                    'tel2' => sanitize_input($_POST['tel2']),
                    'email1' => sanitize_input($_POST['email1']),
                    'email2' => sanitize_input($_POST['email2']),
                    'notes' => sanitize_input($_POST['notes']),
                    'is_active' => isset($_POST['is_active']) ? 1 : 0
                ];
                
                try {
                    if ($this->supplier_model->create($data)) {
                        $supplier_id = $this->supplier_model->db->lastInsertId();
                        
                        // Add contacts if provided
                        if (isset($_POST['contacts']) && !empty($_POST['contacts'])) {
                            $this->processContacts($supplier_id, $_POST['contacts']);
                        }
                        
                        log_activity($_SESSION['user_id'], 'create_supplier', 'Created supplier: ' . $data['name']);
                        send_notification('Fornitore creato con successo', 'success');
                        redirect('suppliers.php');
                    } else {
                        send_notification('Errore nella creazione del fornitore', 'danger');
                    }
                } catch (Exception $e) {
                    send_notification('Errore database: ' . $e->getMessage(), 'danger');
                }
            } else {
                foreach ($errors as $error) {
                    send_notification($error, 'danger');
                }
            }
            
            redirect('suppliers.php?action=create');
        }
    }
    
    // Show edit supplier form
    public function edit($id) {
        if (!has_permission($_SESSION['user_id'], 'suppliers', 'update')) {
            redirect('../admin/unauthorized.php');
        }
        
        $supplier = $this->supplier_model->readOne($id);
        
        if (!$supplier) {
            send_notification('Fornitore non trovato', 'danger');
            redirect('suppliers.php');
        }
        
        $countries = $this->supplier_model->getCountries();
        $states = [];
        $cities = [];
        
        if ($supplier['country_id']) {
            $states = $this->supplier_model->getStatesByCountry($supplier['country_id']);
        }
        
        if ($supplier['state_id']) {
            $cities = $this->supplier_model->getCitiesByState($supplier['state_id']);
        }
        
        $page_title = "Modifica Fornitore";
        include '../views/suppliers/edit.php';
    }
    
    // Update supplier
    public function update($id) {
        if (!has_permission($_SESSION['user_id'], 'suppliers', 'update')) {
            redirect('../admin/unauthorized.php');
        }
        
        if ($_POST) {
            // Verify CSRF token
            if (!verify_csrf_token($_POST['csrf_token'])) {
                send_notification('Token di sicurezza non valido', 'danger');
                redirect('suppliers.php?action=edit&id=' . $id);
            }
            
            $errors = $this->validateSupplierData($_POST);
            
            if (empty($errors)) {
                $data = [
                    'name' => sanitize_input($_POST['name']),
                    'address_line1' => sanitize_input($_POST['address_line1']),
                    'address_line2' => sanitize_input($_POST['address_line2']),
                    'zip_code' => sanitize_input($_POST['zip_code']),
                    'country_id' => !empty($_POST['country_id']) ? (int)$_POST['country_id'] : null,
                    'city_id' => !empty($_POST['city_id']) ? (int)$_POST['city_id'] : null,
                    'tel1' => sanitize_input($_POST['tel1']),
                    'tel2' => sanitize_input($_POST['tel2']),
                    'email1' => sanitize_input($_POST['email1']),
                    'email2' => sanitize_input($_POST['email2']),
                    'notes' => sanitize_input($_POST['notes']),
                    'is_active' => isset($_POST['is_active']) ? 1 : 0
                ];
                
                try {
                    if ($this->supplier_model->update($id, $data)) {
                        // Update contacts if provided
                        if (isset($_POST['contacts'])) {
                            $this->processContacts($id, $_POST['contacts']);
                        }
                        
                        log_activity($_SESSION['user_id'], 'update_supplier', 'Updated supplier ID: ' . $id);
                        send_notification('Fornitore aggiornato con successo', 'success');
                        redirect('suppliers.php');
                    } else {
                        send_notification('Errore nell\'aggiornamento', 'danger');
                    }
                } catch (Exception $e) {
                    send_notification('Errore database: ' . $e->getMessage(), 'danger');
                }
            } else {
                foreach ($errors as $error) {
                    send_notification($error, 'danger');
                }
            }
        }
        
        redirect('suppliers.php?action=edit&id=' . $id);
    }
    
    // Delete supplier
    public function delete($id) {
        if (!has_permission($_SESSION['user_id'], 'suppliers', 'delete')) {
            redirect('../admin/unauthorized.php');
        }
        
        try {
            if ($this->supplier_model->delete($id)) {
                log_activity($_SESSION['user_id'], 'delete_supplier', 'Deleted supplier ID: ' . $id);
                send_notification('Fornitore eliminato con successo', 'success');
            } else {
                send_notification('Errore nell\'eliminazione', 'danger');
            }
        } catch (Exception $e) {
            send_notification('Errore database: ' . $e->getMessage(), 'danger');
        }
        
        redirect('suppliers.php');
    }
    
    // AJAX: Get cities by country
    public function getCities() {
        $country_id = $_GET['country_id'] ?? null;
        $keyword = $_GET['q'] ?? '';
        
        if ($country_id) {
            $cities = $this->supplier_model->searchCities($country_id, $keyword);
            header('Content-Type: application/json');
            echo json_encode($cities);
        } else {
            echo json_encode([]);
        }
        exit;
    }
    
    // AJAX: Add contact
    public function addContact() {
        if (!has_permission($_SESSION['user_id'], 'suppliers', 'update')) {
            http_response_code(403);
            echo json_encode(['error' => 'Permission denied']);
            exit;
        }
        
        if ($_POST) {
            $supplier_id = (int)$_POST['supplier_id'];
            $contact_data = [
                'first_name' => sanitize_input($_POST['first_name']),
                'last_name' => sanitize_input($_POST['last_name']),
                'tel1' => sanitize_input($_POST['tel1']),
                'tel2' => sanitize_input($_POST['tel2']),
                'email1' => sanitize_input($_POST['email1']),
                'email2' => sanitize_input($_POST['email2']),
                'notes' => sanitize_input($_POST['notes']),
                'is_primary' => isset($_POST['is_primary']) ? 1 : 0
            ];
            
            try {
                if ($this->supplier_model->addContact($supplier_id, $contact_data)) {
                    echo json_encode(['success' => true, 'message' => 'Contatto aggiunto con successo']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Errore nell\'aggiunta del contatto']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }
        exit;
    }
    
    // AJAX: Delete contact
    public function deleteContact() {
        if (!has_permission($_SESSION['user_id'], 'suppliers', 'delete')) {
            http_response_code(403);
            echo json_encode(['error' => 'Permission denied']);
            exit;
        }
        
        $contact_id = $_POST['contact_id'] ?? null;
        
        if ($contact_id) {
            try {
                if ($this->supplier_model->deleteContact($contact_id)) {
                    echo json_encode(['success' => true, 'message' => 'Contatto eliminato con successo']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Errore nell\'eliminazione del contatto']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'ID contatto mancante']);
        }
        exit;
    }
    
    // Process contacts data
    private function processContacts($supplier_id, $contacts_data) {
        foreach ($contacts_data as $contact) {
            if (!empty($contact['first_name']) && !empty($contact['last_name'])) {
                if (isset($contact['id']) && $contact['id']) {
                    // Update existing contact
                    $this->supplier_model->updateContact($contact['id'], $contact);
                } else {
                    // Add new contact
                    $this->supplier_model->addContact($supplier_id, $contact);
                }
            }
        }
    }
    
    // Validate supplier data
    private function validateSupplierData($data) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = 'Il nome del fornitore è obbligatorio';
        }
        
        if (!empty($data['email1']) && !filter_var($data['email1'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Formato email 1 non valido';
        }
        
        if (!empty($data['email2']) && !filter_var($data['email2'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Formato email 2 non valido';
        }
        
        return $errors;
    }
}
?>
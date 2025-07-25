<?php
require_once admin_module_path('/models/User.php');
require_once get_setting('base_path', '/var/www/html') . 'admin/includes/functions.php';

class UsersController {
    private $user_model;
    private $db;
    
    public function __construct() {
        $this->user_model = new User();
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }
    
    // Display users list
    public function index() {
        // Check permission
        if (!has_permission($_SESSION['user_id'], 'users', 'view')) {
            redirect(__DIR__ . '/../admin/unauthorized.php');
        }
        
        $users = $this->user_model->read();
        $total_users = $this->user_model->count();
        
        $page_title = "Gestione Utenti";
        include admin_module_path('/views/users/index.php');
    }
    
    // Show create user form
    public function create() {
        if (!has_permission($_SESSION['user_id'], 'users', 'create')) {
            redirect(__DIR__ . '/../admin/unauthorized.php');
        }
        
        $page_title = "Nuovo Utente";
        include __DIR__ . '/../views/users/create.php';
    }
    
    // Show edit user form
    public function edit($id) {
        if (!has_permission($_SESSION['user_id'], 'users', 'update')) {
            redirect(__DIR__ . '/../admin/unauthorized.php');
        }
        
        $user = $this->user_model->readOne($id);
        if (!$user) {
            send_notification('Utente non trovato', 'danger');
            redirect('users.php');
        }
        
        $page_title = "Modifica Utente";
        include __DIR__ . '/../views/users/edit.php';
    }
    
    // Store new user
    public function store() {
        if (!has_permission($_SESSION['user_id'], 'users', 'create')) {
            redirect(__DIR__ . '/../admin/unauthorized.php');
        }
        
        if ($_POST) {
            // Verify CSRF token
            if (!verify_csrf_token($_POST['csrf_token'])) {
                send_notification('Token di sicurezza non valido', 'danger');
                redirect(get_setting('site_url', 'http://localhost') . '/admin/users/create');
            }
            
            // Validate input
            $errors = $this->validateUserData($_POST);
            
            if (empty($errors)) {
                $data = [
                    'username' => sanitize_input($_POST['username']),
                    'email' => sanitize_input($_POST['email']),
                    'password' => $_POST['password'],
                    'first_name' => sanitize_input($_POST['first_name']),
                    'last_name' => sanitize_input($_POST['last_name']),
                    'phone' => sanitize_input($_POST['phone']),
                    'is_active' => isset($_POST['is_active']) ? 1 : 0
                ];
                
                try {
                    $newUserId = $this->user_model->create($data);
                    if ($newUserId) {
                        log_action('access-management', 'users', 'create', $newUserId, null, $data);
                        run_logic_hook('user.after_create', $newUserId, $_POST);
                        send_notification(TranslationManager::t('user.created_successfully'), 'success');
                        redirect(get_setting('site_url', 'http://localhost') . '/admin/users');
                    } else {
                        send_notification('Errore nella creazione dell\'utente', 'danger');
                    }
                } catch (Exception $e) {
                    send_notification('Errore database: ' . $e->getMessage(), 'danger');
                }
            } else {
                foreach ($errors as $error) {
                    send_notification($error, 'danger');
                }
            }

            redirect(get_setting('site_url', 'http://localhost') . '/admin/users/create');
        }
    }
    
    // Update user
    public function update($id) {
        if (!has_permission($_SESSION['user_id'], 'users', 'update')) {
            redirect('../admin/unauthorized.php');
        }
        
        if ($_POST) {
            // Verify CSRF token
            if (!verify_csrf_token($_POST['csrf_token'])) {
                send_notification('Token di sicurezza non valido', 'danger');
                redirect('users.php?action=edit&id=' . $id);
            }
            
            $data = [
                'username' => sanitize_input($_POST['username']),
                'email' => sanitize_input($_POST['email']),
                'first_name' => sanitize_input($_POST['first_name']),
                'last_name' => sanitize_input($_POST['last_name']),
                'phone' => sanitize_input($_POST['phone']),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];
            
            try {
                $this->db->beginTransaction();
                $old_data = $this->user_model->readOne($id);

                if ($this->user_model->update($id, $data)) {
                    log_action('access-management', 'users', 'update', $id, $old_data, $data);
                    run_logic_hook('user.after_update', $id, $_POST);
                    $this->db->commit();
                    send_notification(TranslationManager::t('user.updated_successfully'), 'success');
                    redirect('../admin/access-management/users');
                } else {
                    $this->db->rollBack();
                    send_notification(TranslationManager::t('error.updating_user'), 'danger');
                }
            } catch (Exception $e) {
                $this->db->rollBack();
                send_notification(TranslationManager::t('error.database') . ': ' . $e->getMessage(), 'danger');
            }
        }
        
        redirect('../admin/users.php?action=edit&id=' . $id);
    }
    
    // Delete user
    public function delete($id) {
        if (!has_permission($_SESSION['user_id'], 'users', 'delete')) {
            redirect('../admin/unauthorized.php');
        }
        
        try {
            $old_data = $this->user_model->readOne($id);

            if ($this->user_model->delete($id)) {
                log_action('access-management', 'users', 'delete', $id, $old_data, null);
                send_notification('Utente eliminato con successo', 'success');
            } else {
                send_notification('Errore nell\'eliminazione', 'danger');
            }
        } catch (Exception $e) {
            send_notification('Errore database: ' . $e->getMessage(), 'danger');
        }
        
        redirect('../admin/users.php');
    }
    
    // Validate user data
    private function validateUserData($data, $user_id = null) {
        $errors = [];
        
        if (empty($data['username'])) {
            $errors[] = 'Il nome utente è obbligatorio';
        }
        
        if (empty($data['email'])) {
            $errors[] = 'L\'email è obbligatoria';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Formato email non valido';
        }
        
        if (empty($data['first_name'])) {
            $errors[] = 'Il nome è obbligatorio';
        }
        
        if (empty($data['last_name'])) {
            $errors[] = 'Il cognome è obbligatorio';
        }
        
        if (!$user_id && empty($data['password'])) {
            $errors[] = 'La password è obbligatoria';
        } elseif (!empty($data['password']) && strlen($data['password']) < 6) {
            $errors[] = 'La password deve essere di almeno 6 caratteri';
        }
        
        return $errors;
    }
}
?>

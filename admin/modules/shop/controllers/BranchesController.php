<?php
require_once __DIR__ . '/../models/Branch.php';

class BranchesController
{
    private $branch_model;

    public function __construct()
    {
        $this->branch_model = new Branch();
    }

    // Display branches list
    public function index()
    {
        if (!has_permission($_SESSION['user_id'], 'branches', 'view')) {
            redirect(__DIR__ . '/../admin/unauthorized.php');
        }

        $branches = $this->branch_model->read();
        $total_branches = $this->branch_model->count();

        $page_title = "Gestione Filiali";
        include __DIR__ . '/../views/branches/index.php';
    }

    // Show create branch form
    public function create()
    {
        if (!has_permission($_SESSION['user_id'], 'branches', 'create')) {
            redirect(__DIR__ . '/../admin/unauthorized.php');
        }

        $page_title = "Nuova Filiale";
        include __DIR__ . '/../views/branches/create.php';
    }

    // Store new branch
    public function store()
    {
        if (!has_permission($_SESSION['user_id'], 'branches', 'create')) {
            redirect(__DIR__ . '/../admin/unauthorized.php');
        }

        if ($_POST) {
            // Verify CSRF token
            if (!verify_csrf_token($_POST['csrf_token'])) {
                send_notification('Token di sicurezza non valido', 'danger');
                redirect(get_setting('site_url', 'http://localhost') . '/admin/branches/create');
            }

            // Validate input
            $errors = $this->validateBranchData($_POST);

            if (empty($errors)) {
                $data = [
                    'name' => sanitize_input($_POST['name']),
                    'email1' => sanitize_input($_POST['email1']),
                    'email2' => sanitize_input($_POST['email2']),
                    'tel1' => sanitize_input($_POST['tel1']),
                    'tel2' => sanitize_input($_POST['tel2']),
                    'address' => sanitize_input($_POST['address']),
                    'city_id' => !empty($_POST['city_id']) ? (int)$_POST['city_id'] : null,
                    'state_id' => !empty($_POST['state_id']) ? (int)$_POST['state_id'] : null,
                    'referente' => sanitize_input($_POST['referente']),
                    'is_active' => isset($_POST['is_active']) ? 1 : 0
                ];

                try {
                    if ($this->branch_model->create($data)) {
                        log_action('shop', 'branches', 'create_branch', 0);
                        send_notification('Filiale creata con successo', 'success');
                        redirect(get_setting('site_url', 'http://localhost') . '/admin/branches');
                    } else {
                        send_notification('Errore nella creazione della filiale', 'danger');
                    }
                } catch (Exception $e) {
                    send_notification('Errore database: ' . $e->getMessage(), 'danger');
                }
            } else {
                foreach ($errors as $error) {
                    send_notification($error, 'danger');
                }
            }

            redirect(get_setting('site_url', 'http://localhost') . '/admin/branches/create');
        }
    }

    // Show edit branch form
    public function edit($id)
    {
        if (!has_permission($_SESSION['user_id'], 'branches', 'update')) {
            redirect(__DIR__ . '/../admin/unauthorized.php');
        }

        $branch = $this->branch_model->readOne($id);

        if (!$branch) {
            send_notification('Filiale non trovata', 'danger');
            redirect(get_setting('site_url', 'http://localhost') . '/admin/branches');
        }

        $page_title = "Modifica Filiale";
        include __DIR__ . '/../views/branches/edit.php';
    }

    // Update branch
    public function update($id)
    {
        if (!has_permission($_SESSION['user_id'], 'branches', 'update')) {
            redirect(__DIR__ . '/../admin/unauthorized.php');
        }

        if ($_POST) {
            // Verify CSRF token
            if (!verify_csrf_token($_POST['csrf_token'])) {
                send_notification('Token di sicurezza non valido', 'danger');
                redirect('branches.php?action=edit&id=' . $id);
            }

            $errors = $this->validateBranchData($_POST);

            if (empty($errors)) {
                $data = [
                    'name' => sanitize_input($_POST['name']),
                    'email1' => sanitize_input($_POST['email1']),
                    'email2' => sanitize_input($_POST['email2']),
                    'tel1' => sanitize_input($_POST['tel1']),
                    'tel2' => sanitize_input($_POST['tel2']),
                    'address' => sanitize_input($_POST['address']),
                    'city_id' => !empty($_POST['city_id']) ? (int)$_POST['city_id'] : null,
                    'state_id' => !empty($_POST['state_id']) ? (int)$_POST['state_id'] : null,
                    'referente' => sanitize_input($_POST['referente']),
                    'is_active' => isset($_POST['is_active']) ? 1 : 0
                ];

                try {
                    if ($this->branch_model->update($id, $data)) {
                        //log_action($_SESSION['user_id'], 'update_branch', 'Updated branch ID: ' . $id);
                        send_notification('Filiale aggiornata con successo', 'success');
                        redirect(get_setting('site_url', 'http://localhost') . '/admin/branches');
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

        redirect('branches.php?action=edit&id=' . $id);
    }

    // Delete branch
    public function delete($id)
    {
        if (!has_permission($_SESSION['user_id'], 'branches', 'delete')) {
            redirect(__DIR__ . '/../admin/unauthorized.php');
        }

        try {
            if ($this->branch_model->delete($id)) {
                //log_action($_SESSION['user_id'], 'delete_branch', 'Deleted branch ID: ' . $id);
                send_notification('Filiale eliminata con successo', 'success');
            } else {
                send_notification('Errore nell\'eliminazione', 'danger');
            }
        } catch (Exception $e) {
            send_notification('Errore database: ' . $e->getMessage(), 'danger');
        }

        redirect(get_setting('site_url', 'http://localhost') . '/admin/branches');
    }

    // Manage users for branch
    public function manageUsers($id)
    {
        if (!has_permission($_SESSION['user_id'], 'branches', 'update')) {
            redirect(__DIR__ . '/../admin/unauthorized.php');
        }

        $branch = $this->branch_model->readOne($id);
        $branch_users = $this->branch_model->getBranchUsers($id);
        $all_users = $this->getAllUsers();

        $page_title = "Gestione Utenti - " . $branch['name'];
        include __DIR__ . '/../views/branches/manage_users.php';
    }

    // Assign user to branch
    public function assignUser()
    {
        if (!has_permission($_SESSION['user_id'], 'branches', 'update')) {
            http_response_code(403);
            echo json_encode(['error' => 'Permission denied']);
            exit;
        }

        if ($_POST) {
            $user_id = (int)$_POST['user_id'];
            $branch_id = (int)$_POST['branch_id'];
            $is_primary = isset($_POST['is_primary']) ? 1 : 0;

            try {
                if ($this->branch_model->assignUser($user_id, $branch_id, $is_primary)) {
                    echo json_encode(['success' => true, 'message' => 'Utente assegnato con successo']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Errore nell\'assegnazione']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }
        exit;
    }

    // Remove user from branch
    public function removeUser()
    {
        if (!has_permission($_SESSION['user_id'], 'branches', 'update')) {
            http_response_code(403);
            echo json_encode(['error' => 'Permission denied']);
            exit;
        }

        if ($_POST) {
            $user_id = (int)$_POST['user_id'];
            $branch_id = (int)$_POST['branch_id'];

            try {
                if ($this->branch_model->removeUser($user_id, $branch_id)) {
                    echo json_encode(['success' => true, 'message' => 'Utente rimosso con successo']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Errore nella rimozione']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }
        exit;
    }

    // Get all users for assignment
    private function getAllUsers()
    {
        global $db;
        $stmt = $db->prepare("
            SELECT id, username, first_name, last_name, email,
                   CONCAT(first_name, ' ', last_name, ' (', username, ')') as display_name
            FROM users 
            WHERE is_active = 1 
            ORDER BY first_name, last_name
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Validate branch data
    private function validateBranchData($data)
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors[] = 'Il nome della filiale è obbligatorio';
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

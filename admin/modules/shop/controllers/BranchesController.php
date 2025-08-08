<?php
require_once __DIR__ . '/../models/Branch.php';

class BranchesController
{
    private $branch_model;

    public function __construct()
    {
        $this->branch_model = new Branch();
    TranslationManager::loadModuleTranslations('shop');
    }

    // Display branches list
    public function index()
    {
        if (!has_permission($_SESSION['user_id'], 'branches', 'view')) {
            redirect(__DIR__ . '/../admin/unauthorized.php');
        }

        $branches = $this->branch_model->read();
        $total_branches = $this->branch_model->count();

    $page_title = TranslationManager::t('branch.management');
        include __DIR__ . '/../views/branches/index.php';
    }

    // Show create branch form
    public function create()
    {
        if (!has_permission($_SESSION['user_id'], 'branches', 'create')) {
            redirect(__DIR__ . '/../admin/unauthorized.php');
        }

    $page_title = TranslationManager::t('branch.new_branch');
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
                send_notification('msg.invalid_token', 'danger');
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
                        send_notification('branch.msg.created_successfully', 'success');
                        redirect(get_setting('site_url', 'http://localhost') . '/admin/branches');
                    } else {
                        send_notification('branch.msg.create_error', 'danger');
                    }
                } catch (Exception $e) {
                    send_notification('branch.msg.db_error', 'danger');
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
            send_notification('branch.msg.not_found', 'danger');
            redirect(get_setting('site_url', 'http://localhost') . '/admin/branches');
        }
        $page_title = TranslationManager::t('branch.edit_branch');
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
                send_notification('msg.invalid_token', 'danger');
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
                        send_notification('branch.msg.updated_successfully', 'success');
                        redirect(get_setting('site_url', 'http://localhost') . '/admin/branches');
                    } else {
                        send_notification('branch.msg.update_error', 'danger');
                    }
                } catch (Exception $e) {
                    send_notification('branch.msg.db_error', 'danger');
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
                send_notification('branch.msg.deleted_successfully', 'success');
            } else {
                send_notification('branch.msg.delete_error', 'danger');
            }
        } catch (Exception $e) {
            send_notification('branch.msg.db_error', 'danger');
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

    $page_title = TranslationManager::t('branch.manage_users') . ' - ' . $branch['name'];
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
                    echo json_encode(['success' => true, 'message' => TranslationManager::t('branch.msg.user_assigned')]);
                } else {
                    echo json_encode(['success' => false, 'message' => TranslationManager::t('branch.msg.assign_error')]);
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
                    echo json_encode(['success' => true, 'message' => TranslationManager::t('branch.msg.user_removed')]);
                } else {
                    echo json_encode(['success' => false, 'message' => TranslationManager::t('branch.msg.remove_error')]);
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
            $errors[] = 'branch.validation.name_required';
        }

        if (!empty($data['email1']) && !filter_var($data['email1'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'branch.validation.email1_invalid';
        }

        if (!empty($data['email2']) && !filter_var($data['email2'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'branch.validation.email2_invalid';
        }

        return $errors;
    }
}

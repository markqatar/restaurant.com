<?php
require_once admin_module_path('/models/User.php');
require_once admin_module_path('/models/UserGroup.php');

class UsersController
{
    private $user_model;
    private $db;

    public function __construct()
    {
        $this->user_model = new User();
        $database = Database::getInstance();
        $this->db = $database->getConnection();
        TranslationManager::loadModuleTranslations('access-management');
    }

    /**
     * Display list of users
     */
    public function index()
    {
        if (!has_permission($_SESSION['user_id'], 'users', 'view')) {
            redirect(__DIR__ . '/../admin/unauthorized.php');
        }

        $users = $this->user_model->read();
        $total_users = $this->user_model->count();

        $page_title = TranslationManager::t('users.page_title');
        include admin_module_path('/views/users/index.php');
    }

    /**
     * Show create user form
     */
    public function create()
    {
        if (!has_permission($_SESSION['user_id'], 'users', 'create')) {
            redirect(__DIR__ . '/../admin/unauthorized.php');
        }
        $userGroupModel = new UserGroup();
        $userGroups = $userGroupModel->getActive();

        $page_title = TranslationManager::t('users.create_title');
        include __DIR__ . '/../views/users/create.php';
    }

    /**
     * Show edit user form
     */
    public function edit($id)
    {
        if (!has_permission($_SESSION['user_id'], 'users', 'update')) {
            redirect(__DIR__ . '/../admin/unauthorized.php');
        }

        $user = $this->user_model->readOne($id);
        if (!$user) {
            send_notification(TranslationManager::t('msg.not_found'), 'danger');
            redirect('users.php');
        }
        $userGroupModel = new UserGroup();
        $userGroups = $userGroupModel->getActive();
        $assignedGroups = $this->user_model->getUserGroups($id); // Aggiungeremo questa funzione in User.php


        $page_title = TranslationManager::t('users.edit_title');
        include __DIR__ . '/../views/users/edit.php';
    }

    /**
     * Store new user
     */
    public function store()
    {
        if (!has_permission($_SESSION['user_id'], 'users', 'create')) {
            redirect(__DIR__ . '/../admin/unauthorized.php');
        }

        if ($_POST) {
            if (!verify_csrf_token($_POST['csrf_token'])) {
                send_notification(TranslationManager::t('msg.invalid_token'), 'danger');
                redirect(get_setting('site_url') . '/admin/users/create');
            }

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
                        if (!empty($_POST['group_id'])) {
                            $this->user_model->assignToGroup($newUserId, (int) $_POST['group_id']);
                        }
                        log_action('access-management', 'users', 'create', $newUserId, null, $data);
                        run_logic_hook('user.after_create', $newUserId, $_POST);
                        send_notification(TranslationManager::t('msg.created_successfully'), 'success');
                        redirect(get_setting('site_url') . '/admin/users');
                    } else {
                        send_notification(TranslationManager::t('msg.error_occurred'), 'danger');
                    }
                } catch (Exception $e) {
                    send_notification(TranslationManager::t('msg.db_error') . ': ' . $e->getMessage(), 'danger');
                }
            } else {
                foreach ($errors as $error) {
                    send_notification(TranslationManager::t($error), 'danger');
                }
            }

            redirect(get_setting('site_url') . '/admin/users/create');
        }
    }

    /**
     * Update user
     */
    public function update($id)
    {
        if (!has_permission($_SESSION['user_id'], 'users', 'update')) {
            redirect('../admin/unauthorized.php');
        }

        if ($_POST) {
            if (!verify_csrf_token($_POST['csrf_token'])) {
                send_notification(TranslationManager::t('msg.invalid_token'), 'danger');
                redirect(get_setting('site_url') . '/admin/access-management/users/edit/' . $id);
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
                    $this->user_model->removeAllGroups($id);
                    if (!empty($_POST['group_id'])) {
                        $this->user_model->assignToGroup($id, (int) $_POST['group_id']);
                    }

                    log_action('access-management', 'users', 'update', $id, $old_data, $data);
                    run_logic_hook('user.after_update', $id, $_POST);
                    $this->db->commit();
                    send_notification(TranslationManager::t('msg.updated_successfully'), 'success');
                    redirect('../admin/access-management/users');
                } else {
                    $this->db->rollBack();
                    send_notification(TranslationManager::t('msg.error_occurred'), 'danger');
                }
            } catch (Exception $e) {
                $this->db->rollBack();
                send_notification(TranslationManager::t('msg.db_error') . ': ' . $e->getMessage(), 'danger');
            }
        }

                redirect(get_setting('site_url') . '/admin/access-management/users/edit/' . $id);
    }

    /**
     * Delete user
     */
    public function delete($id)
    {
        if (!has_permission($_SESSION['user_id'], 'users', 'delete')) {
            redirect('../admin/unauthorized.php');
        }

        try {
            $old_data = $this->user_model->readOne($id);

            if ($this->user_model->delete($id)) {
                log_action('access-management', 'users', 'delete', $id, $old_data, null);
                send_notification(TranslationManager::t('msg.deleted_successfully'), 'success');
            } else {
                send_notification(TranslationManager::t('msg.error_occurred'), 'danger');
            }
        } catch (Exception $e) {
            send_notification(TranslationManager::t('msg.db_error') . ': ' . $e->getMessage(), 'danger');
        }

        redirect('../admin/users.php');
    }

    /**
     * Validate user input
     */
    private function validateUserData($data, $user_id = null)
    {
        $errors = [];

        if (empty($data['username'])) {
            $errors[] = 'validation.username_required';
        }

        if (empty($data['email'])) {
            $errors[] = 'validation.email_required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'validation.email_invalid';
        }

        if (empty($data['first_name'])) {
            $errors[] = 'validation.first_name_required';
        }

        if (empty($data['last_name'])) {
            $errors[] = 'validation.last_name_required';
        }

        if (!$user_id && empty($data['password'])) {
            $errors[] = 'validation.password_required';
        } elseif (!empty($data['password']) && strlen($data['password']) < 6) {
            $errors[] = 'validation.password_min';
        }

        return $errors;
    }
}

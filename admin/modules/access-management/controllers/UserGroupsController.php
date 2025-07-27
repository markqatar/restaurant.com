<?php
require_once admin_module_path('/models/UserGroup.php');

class UserGroupsController
{
    private $userGroupModel;

    public function __construct()
    {
        $this->userGroupModel = new UserGroup();
        TranslationManager::loadModuleTranslations('access-management');
    }

    public function index()
    {
        $user_groups = $this->userGroupModel->getAll();
        $total_groups = count($user_groups);

        $page_title = TranslationManager::t('user_groups.page_title');
        include __DIR__ . '/../views/user-groups/index.php';
    }

    public function create()
    {
        require_once __DIR__ . '/../models/Permission.php';
        $permissionModel = new Permission();
        $resources = $permissionModel->getResources();

        $page_title = TranslationManager::t('user_groups.create_title');
        include __DIR__ . '/../views/user-groups/create.php';
    }

    public function store()
    {
        if ($_POST) {
            if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
                set_notification(TranslationManager::t('msg.invalid_token'), 'error');
                redirect(get_setting('site_url') . '/admin/access-management/userGroups/create');
                return;
            }

            if (empty($_POST['name'])) {
                set_notification(TranslationManager::t('msg.required_field') . ': ' . TranslationManager::t('fields.name'), 'error');
                redirect(get_setting('site_url') . '/admin/access-management/userGroups/create');
                return;
            }

            $data = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description'] ?? '')
            ];

            $groupId = $this->userGroupModel->create($data);
            if ($groupId) {
                if (!empty($_POST['permissions'])) {
                    $this->userGroupModel->assignPermissions($groupId, $_POST['permissions']);
                }

                set_notification(TranslationManager::t('msg.created_successfully'), 'success');
                redirect(get_setting('site_url') . '/admin/access-management/userGroups');
            } else {
                set_notification(TranslationManager::t('msg.error_occurred'), 'error');
                redirect(get_setting('site_url') . '/admin/access-management/userGroups/create');
            }
        }
    }

    public function edit($id)
    {
        $user_group = $this->userGroupModel->getById($id);
        if (!$user_group) {
            set_notification(TranslationManager::t('msg.not_found'), 'error');
            redirect(get_setting('site_url') . '/admin/access-management/userGroups');
            return;
        }

        require_once __DIR__ . '/../models/Permission.php';
        $permissionModel = new Permission();
        $resources = $permissionModel->getResources();
        $user_group['permissions'] = $this->userGroupModel->getPermissions($id);

        $page_title = TranslationManager::t('user_groups.edit_title');
        include __DIR__ . '/../views/user-groups/edit.php';
    }

    public function update($id)
    {
        if ($_POST) {
            if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
                set_notification(TranslationManager::t('msg.invalid_token'), 'error');
                redirect(get_setting('site_url') . '/admin/access-management/user-groups/edit/' . $id);
                return;
            }

            if (empty($_POST['name'])) {
                set_notification(TranslationManager::t('msg.required_field') . ': ' . TranslationManager::t('fields.name'), 'error');
                redirect(get_setting('site_url') . '/admin/access-management/user-groups/edit/' . $id);
                return;
            }

            $data = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description'] ?? '')
            ];

            if ($this->userGroupModel->update($id, $data)) {
                $permissions = $_POST['permissions'] ?? [];
                $this->userGroupModel->assignPermissions($id, $permissions);

                set_notification(TranslationManager::t('msg.updated_successfully'), 'success');
                redirect(get_setting('site_url') . '/admin/access-management/userGroups');
            } else {
                set_notification(TranslationManager::t('msg.error_occurred'), 'error');
                redirect(get_setting('site_url') . '/admin/access-management/user-groups/edit/' . $id);
            }
        }
    }

    public function delete($id)
    {
        $result = $this->userGroupModel->delete($id);

        if (is_array($result) && isset($result['error'])) {
            set_notification(TranslationManager::t('user_groups.delete_error_in_use'), 'error');
        } else if ($result) {
            set_notification(TranslationManager::t('msg.deleted_successfully'), 'success');
        } else {
            set_notification(TranslationManager::t('msg.error_occurred'), 'error');
        }

        redirect(get_setting('site_url') . '/admin/access-management/userGroups');
    }
}
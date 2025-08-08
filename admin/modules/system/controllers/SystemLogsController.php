<?php
require_once admin_module_path('/models/ActivityLog.php', 'system');
class SystemLogsController
{
    public function index()
    {
        if (!has_permission($_SESSION['user_id'], 'system_logs', 'view')) {
            redirect(admin_url('unauthorized'));
        }
    TranslationManager::loadModuleTranslations('system');
        include admin_module_path('/views/activity_logs/activity_logs.php', 'system');
    }

    public function datatable()
    {
        $start = $_POST['start'] ?? 0;
        $length = $_POST['length'] ?? 10;
        $search = $_POST['search']['value'] ?? '';
        // Parametri ordinamento
        $orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
        $orderDir = $_POST['order'][0]['dir'] ?? 'asc';

        // Colonne disponibili
        $columns = [
            'id',
            'username',
            'module',
            'table_name',
            'action',
            'record_id',
            'created_at'
        ];

        // Assicurati che l'indice sia valido
        $orderColumn = $columns[$orderColumnIndex] ?? 'created_at';

        $model = new ActivityLog();
        $logs = $model->getLogs($start, $length, $search, $orderColumn, $orderDir);
        $total = $model->countLogs($search);

        echo json_encode([
            'draw' => (int)$_POST['draw'],
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => array_map(function ($log) {
                return [
                    $log['id'],
                    htmlspecialchars($log['username']),
                    htmlspecialchars($log['module']),
                    htmlspecialchars($log['table_name']),
                    htmlspecialchars($log['action']),
                    $log['record_id'],
                    format_date($log['created_at']),
                    $this->actionButtons($log)
                ];
            }, $logs)
        ]);
    }

    private function actionButtons($log)
    {
        $detailsBtn = "<button class='btn btn-info btn-sm view-details' data-id='{$log['id']}'>
                        <i class='fas fa-eye'></i> " . TranslationManager::t('details') . "
                   </button>";

        $restoreBtn = '';
        if ($log['action'] === 'update') {
            $restoreBtn = " <button class='btn btn-warning btn-sm restore-log' data-id='{$log['id']}'>
                            <i class='fas fa-undo'></i> " . TranslationManager::t('restore') . "
                        </button>";
        }

        return $detailsBtn . $restoreBtn;
    }
    public function restore()
    {
        if (!has_permission($_SESSION['user_id'], 'system_logs', 'restore')) {
            echo json_encode(['success' => false, 'message' => TranslationManager::t('unauthorized')]);
            exit;
        }

        $result = restore_action($_POST['log_id']);
        echo json_encode($result);
    }

    public function getLogDetails()
    {
        $model = new ActivityLog();
        $log = $model->getLogById($_POST['log_id']);

        if (!$log) {
            echo json_encode(['success' => false, 'message' => TranslationManager::t('log.not_found')]);
            return;
        }

        echo json_encode([
            'success' => true,
            'old_data' => !empty($log['old_data']) ? json_decode($log['old_data'], true) : [],
            'new_data' => !empty($log['new_data']) ? json_decode($log['new_data'], true) : []
        ]);
    }
}

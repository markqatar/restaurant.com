<?php
class ActivityLog
{

    /**
     * Recupera i log per DataTables (paginazione + ricerca)
     */
    public function getLogs($start, $length, $search = null, $orderColumn = 'created_at', $orderDir = 'desc')
    {
        $db = Database::getInstance()->getConnection();
        $allowedColumns = ['id', 'username', 'module', 'table_name', 'action', 'record_id', 'created_at'];
        if (!in_array($orderColumn, $allowedColumns)) {
            $orderColumn = 'created_at';
        }
        $orderDir = strtolower($orderDir) === 'asc' ? 'ASC' : 'DESC';

        $query = "
        SELECT l.id, l.module, l.table_name, l.action, l.record_id, l.created_at, u.username
        FROM activity_logs l
        LEFT JOIN users u ON u.id = l.user_id
        WHERE 1
    ";
        if ($search) {
            $query .= " AND (l.module LIKE :search OR u.username LIKE :search)";
        }
        $query .= " ORDER BY $orderColumn $orderDir LIMIT :start, :length";

        $stmt = $db->prepare($query);
        if ($search) $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
        $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Conta i log totali per DataTables
     */
    public function countLogs($search = null)
    {
        $db = Database::getInstance()->getConnection();
        $query = "
            SELECT COUNT(*) as total
            FROM activity_logs l
            LEFT JOIN users u ON u.id = l.user_id
            WHERE 1
        ";
        if ($search) {
            $query .= " AND (l.module LIKE :search OR u.username LIKE :search)";
        }
        $stmt = $db->prepare($query);
        if ($search) $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Recupera un log specifico per ID
     */
    public function getLogById($id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM activity_logs WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Elimina i log più vecchi di X giorni
     */
    public function deleteOldLogs($days = 30)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM activity_logs WHERE created_at < NOW() - INTERVAL :days DAY");
        $stmt->bindValue(':days', (int)$days, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

<?php
function get_user_branches($user_id) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT b.name FROM branches b 
                          INNER JOIN user_branch_assignments uba ON uba.branch_id = b.id
                          WHERE uba.user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}
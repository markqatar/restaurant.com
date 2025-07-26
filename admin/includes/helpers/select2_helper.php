<?php
/**
 * Restituisce dati per Select2 con ricerca e paginazione
 *
 * @param string $table Nome della tabella
 * @param array $columns Colonne da selezionare (es. ['id', 'name'])
 * @param string $searchColumn Colonna su cui applicare il LIKE
 * @param string|null $orderBy Colonna per l'ordinamento
 * @param int $limit Numero di record per pagina
 */
function select2_response($table, $columns = ['id', 'name'], $searchColumn = 'name', $orderBy = 'name', $limit = 20)
{
    $db = Database::getInstance()->getConnection();

    $search = $_GET['q'] ?? '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    $selectCols = implode(', ', $columns);
    $sql = "SELECT SQL_CALC_FOUND_ROWS {$selectCols} FROM {$table} WHERE 1";

    if (!empty($search)) {
        $sql .= " AND {$searchColumn} LIKE :search";
    }

    $sql .= " ORDER BY {$orderBy} ASC LIMIT :offset, :limit";

    $stmt = $db->prepare($sql);

    if (!empty($search)) {
        $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    }

    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Conta i risultati totali
    $totalStmt = $db->query("SELECT FOUND_ROWS()");
    $totalCount = $totalStmt->fetchColumn();

    // Format per Select2 (id e text)
    $results = array_map(function ($row) use ($columns) {
        return [
            'id' => $row[$columns[0]], // Prima colonna = id
            'text' => $row[$columns[1]] // Seconda colonna = nome
        ];
    }, $rows);

    header('Content-Type: application/json');
    echo json_encode([
        'results' => $results,
        'pagination' => [
            'more' => ($offset + $limit) < $totalCount
        ]
    ]);
}
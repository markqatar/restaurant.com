<?php
require_once get_setting('base_path') . 'admin/includes/helpers/select2_helper.php';

class CitiesController
{
    /**
     * Restituisce l'elenco delle città per Select2, filtrate per country_id.
     * Parametri:
     *   q -> ricerca per nome
     *   page -> paginazione
     *   country_id -> filtro obbligatorio
     */
    public function select2()
    {
        $country_id = isset($_GET['country_id']) ? (int)$_GET['country_id'] : null;

        if (!$country_id) {
            header('Content-Type: application/json');
            echo json_encode([
                'results' => [],
                'pagination' => ['more' => false]
            ]);
            exit;
        }

        $db = Database::getInstance()->getConnection();

        $search = $_GET['q'] ?? '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $sql = "SELECT SQL_CALC_FOUND_ROWS id, name FROM cities WHERE country_id = :country_id";

        if (!empty($search)) {
            $sql .= " AND name LIKE :search";
        }

        $sql .= " ORDER BY name ASC LIMIT :offset, :limit";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':country_id', $country_id, PDO::PARAM_INT);
        if (!empty($search)) {
            $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
        }
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Conta totale per paginazione
        $totalStmt = $db->query("SELECT FOUND_ROWS()");
        $totalCount = $totalStmt->fetchColumn();

        // Output JSON compatibile con Select2
        $results = array_map(function ($row) {
            return [
                'id' => $row['id'],
                'text' => $row['name']
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
}
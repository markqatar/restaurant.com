<?php

class Unit
{
    private $db;
    private $table = 'units';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function datatable($start, $length, $search, $orderColumn = 'u.id', $orderDir = 'asc')
    {
        $where = "";
        $params = [];
        if (!empty($search)) {
            $where = " WHERE u.name LIKE :search OR u.short_name LIKE :search";
            $params[':search'] = "%$search%";
        }

        // Conta totale
        $total = $this->db->query("SELECT COUNT(*) as total FROM {$this->table}")->fetch(PDO::FETCH_ASSOC)['total'];

        // Query principale con ORDER dinamico
        $sql = "SELECT u.*, t.value as translation_it
            FROM {$this->table} u
            LEFT JOIN translations t ON t.entity='units' AND t.entity_id=u.id AND t.language='it'
            $where
            ORDER BY $orderColumn $orderDir
            LIMIT :start, :length";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
        $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Conta filtrati
        $filtered = $total;
        if (!empty($search)) {
            $filteredStmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table} u $where");
            $filteredStmt->execute($params);
            $filtered = $filteredStmt->fetch(PDO::FETCH_ASSOC)['total'];
        }

        return ['data' => $data, 'recordsTotal' => $total, 'recordsFiltered' => $filtered];
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (name, short_name, factor, is_active) 
                                    VALUES (:name, :short_name, :factor, :is_active)");
        $stmt->execute($data);
        return $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $data['id'] = $id;
        $stmt = $this->db->prepare("UPDATE {$this->table} 
                                    SET name=:name, short_name=:short_name, factor=:factor, is_active=:is_active 
                                    WHERE id=:id");
        return $stmt->execute($data);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id=:id");
        return $stmt->execute([':id' => $id]);
    }

    public function get($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTranslations($id)
    {
        $stmt = $this->db->prepare("SELECT language, value FROM translations WHERE entity='units' AND entity_id=:id AND field='name'");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $translations = [];
        foreach ($result as $row) {
            $translations[$row['language']] = $row['value'];
        }
        return $translations;
    }

    public function saveTranslations($id, $translations)
    {
        $stmt = $this->db->prepare("DELETE FROM translations WHERE entity='units' AND entity_id=:id AND field='name'");
        $stmt->execute([':id' => $id]);
        $insert = $this->db->prepare("INSERT INTO translations (entity, entity_id, language, field, value) VALUES ('units', :id, :lang, 'name', :value)");
        foreach ($translations as $lang => $value) {
            if (!empty($value)) {
                $insert->execute([':id' => $id, ':lang' => $lang, ':value' => $value]);
            }
        }
    }

    public function getRelations($unitId)
    {
        $sql = "SELECT ur.id, u.name FROM unit_relations ur
            JOIN units u ON u.id = ur.child_unit_id
            WHERE ur.parent_unit_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $unitId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReverseRelations($unitId)
    {
        $sql = "SELECT ur.id, u.name FROM unit_relations ur
            JOIN units u ON u.id = ur.parent_unit_id
            WHERE ur.child_unit_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $unitId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addRelation($parentId, $childId)
    {
        $stmt = $this->db->prepare("INSERT INTO unit_relations (parent_unit_id, child_unit_id)
                                VALUES (:parent_id, :child_id)");
        return $stmt->execute([':parent_id' => $parentId, ':child_id' => $childId]);
    }

    public function deleteRelation($id)
    {
        $stmt = $this->db->prepare("DELETE FROM unit_relations WHERE id=:id");
        return $stmt->execute([':id' => $id]);
    }

    public function searchByName($search = '')
    {
        $sql = "SELECT id, name FROM units";
        if ($search) {
            $sql .= " WHERE name LIKE :search";
        }
        $sql .= " ORDER BY name ASC LIMIT 50";

        $stmt = $this->db->prepare($sql);
        if ($search) {
            $stmt->bindValue(':search', "%$search%");
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

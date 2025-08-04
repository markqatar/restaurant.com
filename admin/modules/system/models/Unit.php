<?php

class Unit {
    private $db;
    private $table = 'units';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function datatable($start, $length, $search) {
        $where = "";
        $params = [];
        if (!empty($search)) {
            $where = " WHERE u.name LIKE :search OR u.short_name LIKE :search";
            $params[':search'] = "%$search%";
        }

        $total = $this->db->query("SELECT COUNT(*) as total FROM {$this->table}")->fetch(PDO::FETCH_ASSOC)['total'];

        $sql = "SELECT u.*, t.value as translation_it
                FROM {$this->table} u
                LEFT JOIN translations t ON t.entity='units' AND t.entity_id=u.id AND t.language='it'
                $where
                ORDER BY u.id DESC
                LIMIT $start, $length";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $filtered = $total;
        if (!empty($search)) {
            $filtered = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table} $where");
            $filtered->execute($params);
            $filtered = $filtered->fetch(PDO::FETCH_ASSOC)['total'];
        }

        return ['data' => $data, 'recordsTotal' => $total, 'recordsFiltered' => $filtered];
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (name, short_name, factor, type, is_active) 
                                    VALUES (:name, :short_name, :factor, :type, :is_active)");
        $stmt->execute($data);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $data['id'] = $id;
        $stmt = $this->db->prepare("UPDATE {$this->table} 
                                    SET name=:name, short_name=:short_name, factor=:factor, type=:type, is_active=:is_active 
                                    WHERE id=:id");
        return $stmt->execute($data);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id=:id");
        return $stmt->execute([':id'=>$id]);
    }

    public function get($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id=:id");
        $stmt->execute([':id'=>$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTranslations($id) {
        $stmt = $this->db->prepare("SELECT language, value FROM translations WHERE entity='units' AND entity_id=:id AND field='name'");
        $stmt->execute([':id'=>$id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $translations = [];
        foreach ($result as $row) {
            $translations[$row['language']] = $row['value'];
        }
        return $translations;
    }

    public function saveTranslations($id, $translations) {
        $stmt = $this->db->prepare("DELETE FROM translations WHERE entity='units' AND entity_id=:id AND field='name'");
        $stmt->execute([':id'=>$id]);
        $insert = $this->db->prepare("INSERT INTO translations (entity, entity_id, language, field, value) VALUES ('units', :id, :lang, 'name', :value)");
        foreach ($translations as $lang => $value) {
            if (!empty($value)) {
                $insert->execute([':id'=>$id, ':lang'=>$lang, ':value'=>$value]);
            }
        }
    }
}
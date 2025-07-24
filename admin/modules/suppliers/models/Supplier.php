<?php

class Supplier {
    private $db;
    private $table = 'suppliers';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Create supplier
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (name, contact_person, email, phone, address, country_id, city_id, is_active) 
                  VALUES (:name, :contact_person, :email, :phone, :address, :country_id, :city_id, :is_active)";
        
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([
            ':name' => $data['name'],
            ':contact_person' => $data['contact_person'] ?? null,
            ':email' => $data['email'] ?? null,
            ':phone' => $data['phone'] ?? null,
            ':address' => $data['address'] ?? null,
            ':country_id' => $data['country_id'] ?? null,
            ':city_id' => $data['city_id'] ?? null,
            ':is_active' => $data['is_active'] ?? 1
        ]);
    }
    
    // Read all suppliers
    public function read($active_only = false) {
        $query = "SELECT s.*, c.name as country_name, ci.name as city_name
                  FROM " . $this->table . " s
                  LEFT JOIN countries c ON s.country_id = c.id
                  LEFT JOIN cities ci ON s.city_id = ci.id";
        
        if ($active_only) {
            $query .= " WHERE s.is_active = 1";
        }
        
        $query .= " ORDER BY s.name";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Read single supplier with location info
    public function readOne($id) {
        $query = "SELECT s.*, c.name as country_name, ci.name as city_name
                  FROM " . $this->table . " s
                  LEFT JOIN countries c ON s.country_id = c.id
                  LEFT JOIN cities ci ON s.city_id = ci.id
                  WHERE s.id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        $supplier = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $supplier;
    }
    
    // Update supplier
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET name = :name, contact_person = :contact_person, email = :email, phone = :phone,
                      address = :address, country_id = :country_id, city_id = :city_id,
                      is_active = :is_active
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':contact_person' => $data['contact_person'] ?? null,
            ':email' => $data['email'] ?? null,
            ':phone' => $data['phone'] ?? null,
            ':address' => $data['address'] ?? null,
            ':country_id' => $data['country_id'] ?? null,
            ':city_id' => $data['city_id'] ?? null,
            ':is_active' => $data['is_active'] ?? 1
        ]);
    }
    
    // Delete supplier
    public function delete($id) {
        // First delete all contacts
        $contacts_query = "DELETE FROM supplier_contacts WHERE supplier_id = :supplier_id";
        $contacts_stmt = $this->db->prepare($contacts_query);
        $contacts_stmt->execute([':supplier_id' => $id]);
        
        // Then delete supplier
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([':id' => $id]);
    }
    
    // Get countries for dropdown
    public function getCountries() {
        $query = "SELECT id, name, phonecode, currency, currency_symbol, emoji 
                  FROM countries 
                  ORDER BY name";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get cities by country (since we don't have states table)
    public function getCitiesByCountry($country_id) {
        $query = "SELECT id, name, country_code 
                  FROM cities 
                  WHERE country_id = :country_id 
                  ORDER BY name
                  LIMIT 1000";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':country_id' => $country_id]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Search cities by country and keyword
    public function searchCities($country_id, $keyword = '') {
        $query = "SELECT id, name, country_code 
                  FROM cities 
                  WHERE country_id = :country_id";
        
        $params = [':country_id' => $country_id];
        
        if (!empty($keyword)) {
            $query .= " AND name LIKE :keyword";
            $params[':keyword'] = '%' . $keyword . '%';
        }
        
        $query .= " ORDER BY name LIMIT 100";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Add contact to supplier
    public function addContact($supplier_id, $contact_data) {
        $query = "INSERT INTO supplier_contacts 
                  (supplier_id, first_name, last_name, tel1, tel2, email1, email2, notes, is_primary, is_active)
                  VALUES (:supplier_id, :first_name, :last_name, :tel1, :tel2, :email1, :email2, :notes, :is_primary, :is_active)";
        
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([
            ':supplier_id' => $supplier_id,
            ':first_name' => $contact_data['first_name'],
            ':last_name' => $contact_data['last_name'],
            ':tel1' => $contact_data['tel1'] ?? null,
            ':tel2' => $contact_data['tel2'] ?? null,
            ':email1' => $contact_data['email1'] ?? null,
            ':email2' => $contact_data['email2'] ?? null,
            ':notes' => $contact_data['notes'] ?? null,
            ':is_primary' => $contact_data['is_primary'] ?? 0,
            ':is_active' => $contact_data['is_active'] ?? 1
        ]);
    }
    
    // Update contact
    public function updateContact($contact_id, $contact_data) {
        $query = "UPDATE supplier_contacts 
                  SET first_name = :first_name, last_name = :last_name,
                      tel1 = :tel1, tel2 = :tel2, email1 = :email1, email2 = :email2,
                      notes = :notes, is_primary = :is_primary, is_active = :is_active,
                      updated_at = CURRENT_TIMESTAMP
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([
            ':id' => $contact_id,
            ':first_name' => $contact_data['first_name'],
            ':last_name' => $contact_data['last_name'],
            ':tel1' => $contact_data['tel1'] ?? null,
            ':tel2' => $contact_data['tel2'] ?? null,
            ':email1' => $contact_data['email1'] ?? null,
            ':email2' => $contact_data['email2'] ?? null,
            ':notes' => $contact_data['notes'] ?? null,
            ':is_primary' => $contact_data['is_primary'] ?? 0,
            ':is_active' => $contact_data['is_active'] ?? 1
        ]);
    }
    
    // Delete contact
    public function deleteContact($contact_id) {
        $query = "DELETE FROM supplier_contacts WHERE id = :id";
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([':id' => $contact_id]);
    }
    
    // Get supplier contacts
    public function getContacts($supplier_id) {
        $query = "SELECT * FROM supplier_contacts 
                  WHERE supplier_id = :supplier_id 
                  ORDER BY is_primary DESC, first_name";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':supplier_id' => $supplier_id]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Search suppliers
    public function search($keyword) {
        $query = "SELECT s.*
                  FROM " . $this->table . " s
                  WHERE s.is_active = 1 
                  AND (s.name LIKE :keyword OR s.email LIKE :keyword OR s.phone LIKE :keyword)
                  ORDER BY s.name";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':keyword' => '%' . $keyword . '%']);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Count suppliers
    public function count() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
?>
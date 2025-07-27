<?php

class User
{
    private $db;
    private $table = 'users';

    public function __construct()
    {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }

    /**
     * Create a new user
     */
    public function create($data)
    {
        try {
            $query = "INSERT INTO " . $this->table . " 
                      (username, email, password, first_name, last_name, phone, is_active) 
                      VALUES (:username, :email, :password, :first_name, :last_name, :phone, :is_active)";

            $stmt = $this->db->prepare($query);

            // Hash password
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

            $result = $stmt->execute([
                ':username' => $data['username'],
                ':email' => $data['email'],
                ':password' => $data['password'],
                ':first_name' => $data['first_name'],
                ':last_name' => $data['last_name'],
                ':phone' => $data['phone'] ?? null,
                ':is_active' => $data['is_active'] ?? 1
            ]);

            if ($result) {
                $userId = $this->db->lastInsertId();

                // Insert user preferences
                $prefsQuery = "INSERT INTO user_preferences (user_id, theme, language, avatar) 
                              VALUES (:user_id, :theme, :language, :avatar)";
                $prefsStmt = $this->db->prepare($prefsQuery);
                $prefsStmt->execute([
                    ':user_id' => $userId,
                    ':theme' => $data['default_theme'] ?? 'light',
                    ':language' => $data['default_language'] ?? 'en',
                    ':avatar' => $data['avatar'] ?? 'default.png'
                ]);

                // Log action
                log_action('access-management', $this->table, 'create', $userId, null, $data);

                return $userId;
            }

            return false;
        } catch (Exception $e) {
            error_log("Error creating user: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Read all users
     */
    public function read($limit = null, $offset = null)
    {
        $query = "SELECT u.*, GROUP_CONCAT(ug.name SEPARATOR ', ') as user_groups
                  FROM " . $this->table . " u
                  LEFT JOIN user_group_assignments uga ON u.id = uga.user_id
                  LEFT JOIN user_groups ug ON uga.group_id = ug.id
                  GROUP BY u.id
                  ORDER BY u.created_at DESC";

        if ($limit) {
            $query .= " LIMIT " . $limit;
            if ($offset) {
                $query .= " OFFSET " . $offset;
            }
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Read single user by ID
     */
    public function readOne($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update user
     */
    public function update($id, $data)
    {
        try {
            $old_data = $this->readOne($id);

            $query = "UPDATE " . $this->table . " 
                      SET username = :username, email = :email, first_name = :first_name, 
                          last_name = :last_name, phone = :phone, is_active = :is_active,
                          updated_at = CURRENT_TIMESTAMP
                      WHERE id = :id";

            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([
                ':id' => $id,
                ':username' => $data['username'],
                ':email' => $data['email'],
                ':first_name' => $data['first_name'],
                ':last_name' => $data['last_name'],
                ':phone' => $data['phone'] ?? null,
                ':is_active' => $data['is_active'] ?? 1
            ]);

            if ($result) {
                log_action('access-management', $this->table, 'update', $id, $old_data, $data);
            }

            return $result;
        } catch (Exception $e) {
            error_log("Error updating user: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Change user password
     */
    public function changePassword($id, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE " . $this->table . " 
                  SET password = :password, updated_at = CURRENT_TIMESTAMP
                  WHERE id = :id");
        $result = $stmt->execute([':id' => $id, ':password' => $hashedPassword]);

        if ($result) {
            log_action('access-management', $this->table, 'change_password', $id, null, ['password' => '***']);
        }

        return $result;
    }

    /**
     * Verify user password
     */
    public function verifyPassword($userId, $password)
    {
        $user = $this->readOne($userId);
        return ($user && password_verify($password, $user['password']));
    }

    /**
     * Check if username exists (optionally excluding a user ID)
     */
    public function usernameExists($username, $excludeUserId = null)
    {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE username = :username";
        $params = [':username' => $username];

        if ($excludeUserId) {
            $query .= " AND id != :user_id";
            $params[':user_id'] = $excludeUserId;
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    /**
     * Update username
     */
    public function updateUsername($userId, $username)
    {
        $stmt = $this->db->prepare("UPDATE " . $this->table . " SET username = :username, updated_at = CURRENT_TIMESTAMP WHERE id = :id");
        $result = $stmt->execute([':username' => $username, ':id' => $userId]);

        if ($result) {
            log_action('access-management', $this->table, 'update_username', $userId, null, ['username' => $username]);
        }

        return $result;
    }

    /**
     * Delete user
     */
    public function delete($id)
    {
        $old_data = $this->readOne($id);
        $stmt = $this->db->prepare("DELETE FROM " . $this->table . " WHERE id = :id");
        $result = $stmt->execute([':id' => $id]);

        if ($result) {
            log_action('access-management', $this->table, 'delete', $id, $old_data, null);
        }

        return $result;
    }

    /**
     * Authenticate user by username/email and password
     */
    public function authenticate($username, $password)
    {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " 
                  WHERE (username = :username OR email = :username) AND is_active = 1");
        $stmt->execute([':username' => $username]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($user && password_verify($password, $user['password'])) ? $user : false;
    }

    /**
     * Get user permissions
     */
    public function getPermissions($user_id)
    {
        $stmt = $this->db->prepare("SELECT p.module, p.action 
                  FROM permissions p
                  JOIN user_group_assignments uga ON p.group_id = uga.group_id
                  WHERE uga.user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Assign user to a group
     */
    public function assignToGroup($user_id, $group_id)
    {
        $stmt = $this->db->prepare("INSERT INTO user_group_assignments (user_id, group_id) VALUES (:user_id, :group_id)");
        $result = $stmt->execute([':user_id' => $user_id, ':group_id' => $group_id]);

        if ($result) {
            log_action('access-management', 'user_group_assignments', 'assign_group', 0, null, ['user_id' => $user_id, 'group_id' => $group_id]);
        }

        return $result;
    }

    /**
     * Remove user from a group
     */
    public function removeFromGroup($user_id, $group_id)
    {
        $stmt = $this->db->prepare("DELETE FROM user_group_assignments WHERE user_id = :user_id AND group_id = :group_id");
        $result = $stmt->execute([':user_id' => $user_id, ':group_id' => $group_id]);

        if ($result) {
            log_action('access-management', 'user_group_assignments', 'remove_group', null, ['user_id' => $user_id, 'group_id' => $group_id], null);
        }

        return $result;
    }

    /**
     * Count total users
     */
    public function count()
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM " . $this->table);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getUserGroups($user_id)
    {
        $stmt = $this->db->prepare("SELECT g.id, g.name FROM user_groups g
        INNER JOIN user_group_assignments uga ON uga.group_id = g.id
        WHERE uga.user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function removeAllGroups($user_id)
    {
        $stmt = $this->db->prepare("DELETE FROM user_group_assignments WHERE user_id = :user_id");
        return $stmt->execute([':user_id' => $user_id]);
    }
}

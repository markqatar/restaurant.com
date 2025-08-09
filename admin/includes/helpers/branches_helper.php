<?php
// Branch-based filtering helper functions

/**
 * Get user's assigned branches
 */
function getUserBranches($user_id) {
    global $db;
    
    $stmt = $db->prepare("
        SELECT b.*, uba.is_primary
        FROM branches b
        JOIN user_branch_assignments uba ON b.id = uba.branch_id
        WHERE uba.user_id = ? AND b.is_active = 1
        ORDER BY uba.is_primary DESC, b.name
    ");
    $stmt->execute([$user_id]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get user's primary branch
 */
function getUserPrimaryBranch($user_id) {
    global $db;
    
    $stmt = $db->prepare("
        SELECT b.*
        FROM branches b
        JOIN user_branch_assignments uba ON b.id = uba.branch_id
        WHERE uba.user_id = ? AND uba.is_primary = 1 AND b.is_active = 1
        LIMIT 1
    ");
    $stmt->execute([$user_id]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Check if user has access to specific branch
 */
function userHasBranchAccess($user_id, $branch_id) {
    global $db;
    
    $stmt = $db->prepare("
        SELECT COUNT(*) as count
        FROM user_branch_assignments uba
        JOIN branches b ON uba.branch_id = b.id
        WHERE uba.user_id = ? AND uba.branch_id = ? AND b.is_active = 1
    ");
    $stmt->execute([$user_id, $branch_id]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
}

/**
 * Get user's branch IDs as array for SQL IN clauses
 */
function getUserBranchIds($user_id) {
    $branches = getUserBranches($user_id);
    return array_column($branches, 'id');
}

/**
 * Generate branch filter SQL clause
 */
function getBranchFilterSQL($user_id, $table_alias = '', $branch_column = 'branch_id') {
    $branch_ids = getUserBranchIds($user_id);
    
    if (empty($branch_ids)) {
        return "1 = 0"; // No access to any branch
    }
    
    $table_prefix = $table_alias ? $table_alias . '.' : '';
    $placeholders = str_repeat('?,', count($branch_ids) - 1) . '?';
    
    return "{$table_prefix}{$branch_column} IN ($placeholders) OR {$table_prefix}{$branch_column} IS NULL";
}

/**
 * Get branch filter parameters for prepared statements
 */
function getBranchFilterParams($user_id) {
    return getUserBranchIds($user_id);
}

/**
 * Generate branch selector dropdown for forms
 */
function getBranchSelector($user_id, $selected_branch_id = null, $name = 'branch_id', $required = false) {
    $branches = getUserBranches($user_id);
    $primary_branch = getUserPrimaryBranch($user_id);
    
    if (empty($branches)) {
        return '<select name="' . $name . '" class="form-select" disabled><option>Nessuna filiale assegnata</option></select>';
    }
    
    $html = '<select name="' . $name . '" class="form-select"' . ($required ? ' required' : '') . '>';
    
    if (!$required) {
        $html .= '<option value="">Tutte le filiali</option>';
    }
    
    foreach ($branches as $branch) {
        $selected = '';
        
        // Auto-select logic
        if ($selected_branch_id !== null) {
            $selected = ($branch['id'] == $selected_branch_id) ? ' selected' : '';
        } elseif ($branch['is_primary'] && $required) {
            $selected = ' selected';
        }
        
        $primary_label = $branch['is_primary'] ? ' (Principale)' : '';
        $html .= '<option value="' . $branch['id'] . '"' . $selected . '>' . 
                 htmlspecialchars($branch['name']) . $primary_label . '</option>';
    }
    
    $html .= '</select>';
    
    return $html;
}

/**
 * Validate branch access for forms
 */
function validateBranchAccess($user_id, $branch_id) {
    if (empty($branch_id)) {
        return true; // Allow null/empty for "all branches"
    }
    
    return userHasBranchAccess($user_id, $branch_id);
}

/**
 * Get branch context for page display
 */
function getBranchContext($user_id, $current_branch_id = null) {
    $user_branches = getUserBranches($user_id);
    $primary_branch = getUserPrimaryBranch($user_id);
    
    $context = [
        'user_branches' => $user_branches,
        'primary_branch' => $primary_branch,
        'current_branch' => null,
        'has_multiple_branches' => count($user_branches) > 1,
        'branch_count' => count($user_branches)
    ];
    
    if ($current_branch_id) {
        foreach ($user_branches as $branch) {
            if ($branch['id'] == $current_branch_id) {
                $context['current_branch'] = $branch;
                break;
            }
        }
    }
    
    return $context;
}

/**
 * Set default branch for new records
 */
function getDefaultBranchId($user_id) {
    $primary_branch = getUserPrimaryBranch($user_id);
    return $primary_branch ? $primary_branch['id'] : null;
}

/**
 * Check if user is super admin (bypass branch filtering)
 */
function isSuperAdmin($user_id) {
    global $db;
    $stmt = $db->prepare("
        SELECT ug.group_id
        FROM `user_group_assignments` ug
        JOIN `user_groups` g ON ug.group_id = g.id
        WHERE ug.user_id = :uid AND g.name IN ('Super Admin','Administrator')
    ");
    $stmt->execute([':uid' => $user_id]);
    return $stmt->rowCount() > 0;
}

/**
 * Generate branch-aware SQL query with automatic filtering
 */
function addBranchFilter($base_query, $user_id, $params = [], $table_alias = '', $branch_column = 'branch_id') {
    // Skip filtering for super admins
    if (isSuperAdmin($user_id)) {
        return ['query' => $base_query, 'params' => $params];
    }
    
    $branch_filter = getBranchFilterSQL($user_id, $table_alias, $branch_column);
    $branch_params = getBranchFilterParams($user_id);
    
    // Add WHERE clause or AND condition
    if (stripos($base_query, 'WHERE') !== false) {
        $filtered_query = $base_query . " AND ($branch_filter)";
    } else {
        $filtered_query = $base_query . " WHERE ($branch_filter)";
    }
    
    return [
        'query' => $filtered_query,
        'params' => array_merge($params, $branch_params)
    ];
}
?>
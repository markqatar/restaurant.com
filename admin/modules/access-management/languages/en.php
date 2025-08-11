<?php

return [

    // (Generic buttons & actions removed - now in global language file)

    // (Generic messages removed - now in global language file)

    // ===========================
    // Form Related
    // ===========================
    // (Form helper keys removed - now in global language file)

    // ===========================
    // User Management
    // ===========================
    'user' => [
        'management' => 'User Management',
        'user_list' => 'User List',
        'new_user' => 'New User',
        'edit_user' => 'Edit User',
        'save_changes' => 'Save Changes',
        'account_info' => 'Account Information',
        'quick_actions' => 'Quick Actions',
        'reset_password' => 'Reset Password',
        'delete_user' => 'Delete User',
        'confirm_reset_password' => 'Are you sure you want to reset this user\'s password?',
        'confirm_delete' => 'Are you sure you want to delete this user?',
        'confirm_delete_text' => 'Deleting a user is permanent and cannot be undone.',
        'password_note' => 'Leave password fields empty to keep the current password.',
        'username' => 'Username',
        'full_name' => 'Full Name',
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'user_id' => 'User ID',
        'created_on' => 'Created On',
        'last_modified' => 'Last Modified',
        'user_active' => 'User Active',
        'status' => 'Status',
        'no_groups' => 'No Groups Assigned',
        'groups' => 'User Groups',
        'permissions' => 'Permissions',
        'permissions_assigned' => 'Assigned Permissions',
        'branch_assignments' => 'Branch Assignments',
        'select_branches' => 'Select the branches this user should be assigned to.',
        'total_users' => 'Total Users',
        'active_users' => 'Active Users',
    // Added nested avatar label (previous dotted key removed)
    'avatar' => 'Avatar',
    ],

    // ===========================
    // User Groups
    // ===========================
    'user_groups' => [
        'total_groups' => 'Total Groups',
        'confirm_delete' => 'Are you sure you want to delete this group?',
        'confirm_delete_text' => 'Deleting a group will remove all its permissions.',
    ],

    // ===========================
    // Permissions
    // ===========================
    'permissions' => 'Permissions',

    // ===========================
    // Resources & Related
    // ===========================
    'resource' => 'Resource',
    'permissions_label' => 'Permissions',
    'module' => 'Module',
    'action' => 'Action',

    // (Common field labels removed - now in global language file)

    // (Status labels removed - now in global language file)

    // ===========================
    // Branches
    // ===========================
    'branch' => [
        'name' => 'Branch Name',
        'location' => 'Location',
        'no_branches' => 'No branches available.',
    ],

    // ===========================
    // Assignments
    // ===========================
    'assign' => 'Assign',
    'primary' => 'Primary',

    // ===========================
    // Profile Management
    // ===========================
'profile' => [
    'page_title' => 'User Profile',

    // Password Update
    'current_password_required' => 'Current password is required.',
    'new_password_required' => 'New password is required.',
    'password_min_length' => 'Password must be at least 6 characters long.',
    'passwords_do_not_match' => 'Passwords do not match.',
    'current_password_incorrect' => 'Current password is incorrect.',
    'password_updated_successfully' => 'Password updated successfully.',
    'failed_to_update_password' => 'Failed to update password.',

    // Username Update
    'username_required' => 'Username is required.',
    'username_min_length' => 'Username must be at least 3 characters long.',
    'username_already_exists' => 'Username already exists.',
    'username_updated_successfully' => 'Username updated successfully.',
    'failed_to_update_username' => 'Failed to update username.',

    // Avatar Update
    'invalid_image_format' => 'Invalid image format. Allowed: JPG, JPEG, PNG, GIF.',
    'avatar_updated_successfully' => 'Profile picture updated successfully.',
    'failed_to_update_avatar' => 'Failed to update profile picture.',
    'failed_to_upload_avatar' => 'Failed to upload profile picture.',
    'error_uploading_file' => 'Error uploading file',
    'avatar_removed_successfully' => 'Profile picture removed successfully.',
    'failed_to_remove_avatar' => 'Failed to remove profile picture.',
],

    'profile' => 'Profile',
    // Avatar nested inside existing user array above
    // (Moved avatar label into the main 'user' array earlier if needed)
    'update_profile_picture' => 'Update Profile Picture',
    'upload_avatar' => 'Upload Avatar',
    'remove_avatar' => 'Remove Avatar',
    'username' => 'Username',
    'password' => 'Password',
    'update_username' => 'Update Username',
    'current_password' => 'Current Password',
    'new_password' => 'New Password',
    'confirm_new_password' => 'Confirm New Password',
    'update_password' => 'Update Password',


];
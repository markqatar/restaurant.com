# Pretty URLs Setup

This document explains how to enable pretty URLs for the restaurant management system.

## Pretty URL Format

With this setup, URLs will change from:
- `http://www.restaurant.com/admin/users.php` to `http://www.restaurant.com/admin/users`
- `http://www.restaurant.com/admin/users.php?action=edit&id=1` to `http://www.restaurant.com/admin/users/edit/1`

## Setup Instructions

### For Apache Server

1. Make sure the `.htaccess` file is present in the root directory
2. Ensure that `mod_rewrite` is enabled on your Apache server:
   ```
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   ```
3. Verify that your Apache configuration allows `.htaccess` overrides:
   ```
   <Directory /var/www/html>
       AllowOverride All
   </Directory>
   ```

### For Nginx Server

1. Use the provided `nginx-pretty-urls.conf` configuration
2. Update your server block in the main Nginx configuration
3. Test the configuration: `sudo nginx -t`
4. Reload Nginx: `sudo systemctl reload nginx`

### Application Changes

The application has been updated to use the new URL format. The following files have been modified:

- Added `router.php` to handle URL routing
- Added `includes/url_functions.php` with URL helper functions
- Updated template files to use the new URL format
- Updated sidebar and navigation links

## URL Helper Functions

Use the following functions to generate URLs in your code:

```php
// Generate a URL for an admin page
admin_url($page, $action = null, $id = null, $params = [])

// Examples:
$users_url = admin_url('users'); // /admin/users
$edit_url = admin_url('users', 'edit', 1); // /admin/users/edit/1
$search_url = admin_url('users', null, null, ['q' => 'john']); // /admin/users?q=john
```

## Troubleshooting

If you encounter issues with pretty URLs:

1. Check that your server is properly configured
2. Verify that the router.php file is in the root directory
3. Make sure the URL helper functions are included
4. Check that all URLs in templates are updated
5. Try clearing your browser cache

For more help, please contact your system administrator.
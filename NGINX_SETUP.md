# Nginx Configuration for Pretty URLs

## Problem

The restaurant management system has been updated to support pretty URLs, but the Nginx configuration needs to be properly set up to handle these URLs.

## Solution

I've created a new Nginx configuration file that works with the updated URL structure and is specifically tailored for macOS Homebrew installations.

## How to Install

1. Copy the `nginx-pretty-urls-mac.conf` file to your Nginx configuration directory:

```bash
cp nginx-pretty-urls-mac.conf /opt/homebrew/etc/nginx/sites-available/restaurant.com.conf
```

2. Create a symbolic link to enable the site:

```bash
ln -s /opt/homebrew/etc/nginx/sites-available/restaurant.com.conf /opt/homebrew/etc/nginx/sites-enabled/
```

3. Test the configuration:

```bash
nginx -t
```

4. Restart Nginx:

```bash
brew services restart nginx
```

## Key Changes

1. **Using index.php as the front controller**:
   - All requests are now routed through index.php
   - The router.php functionality is integrated into index.php

2. **Fixed FastCGI Parameters**:
   - Using `$document_root` instead of `$request_filename`
   - Properly setting up path info splitting

3. **Error Logging**:
   - Added debug level logging to help identify issues
   - Set proper log paths for macOS Homebrew

## Troubleshooting

If you continue to experience issues:

1. Check the error logs:
   ```bash
   tail -f /opt/homebrew/var/log/nginx/restaurant.com.error.log
   ```

2. Make sure PHP-FPM is running:
   ```bash
   brew services list | grep php
   ```

3. Verify the file permissions:
   ```bash
   chmod -R 755 /opt/homebrew/var/www/restaurant.com
   ```

4. Test a simple PHP file directly:
   ```bash
   echo "<?php phpinfo();" > /opt/homebrew/var/www/restaurant.com/public/test.php
   ```
   Then visit http://restaurant1.com/test.php
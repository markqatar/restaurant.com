#!/bin/bash

# Nginx Configuration Setup Script for restaurant.com
# Run as root: sudo bash nginx-setup.sh

set -e

echo "🍕 Restaurant Management System - Nginx Setup"
echo "=============================================="

# Variables
DOMAIN="restaurant.com"
PROJECT_PATH="/var/www/restaurant.com"
NGINX_AVAILABLE="/etc/nginx/sites-available"
NGINX_ENABLED="/etc/nginx/sites-enabled"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if running as root
if [[ $EUID -ne 0 ]]; then
   echo -e "${RED}This script must be run as root${NC}" 
   exit 1
fi

echo -e "${YELLOW}Step 1: Creating project directory...${NC}"
mkdir -p $PROJECT_PATH
mkdir -p $PROJECT_PATH/uploads
mkdir -p $PROJECT_PATH/uploads/products
mkdir -p $PROJECT_PATH/uploads/temp
mkdir -p /var/log/nginx

echo -e "${YELLOW}Step 2: Setting up permissions...${NC}"
chown -R www-data:www-data $PROJECT_PATH
chmod -R 755 $PROJECT_PATH
chmod -R 775 $PROJECT_PATH/uploads

echo -e "${YELLOW}Step 3: Copying project files...${NC}"
if [ -d "/workspace/restaurant-management" ]; then
    cp -r /workspace/restaurant-management/* $PROJECT_PATH/
    echo -e "${GREEN}✓ Project files copied${NC}"
else
    echo -e "${RED}Warning: Source directory not found at /workspace/restaurant-management${NC}"
    echo "Please manually copy your project files to $PROJECT_PATH"
fi

echo -e "${YELLOW}Step 4: Installing required packages...${NC}"
apt-get update
apt-get install -y nginx php8.1-fpm php8.1-mysql php8.1-curl php8.1-gd php8.1-mbstring php8.1-xml php8.1-zip

echo -e "${YELLOW}Step 5: Configuring PHP-FPM...${NC}"
# PHP-FPM configuration
cat > /etc/php/8.1/fpm/pool.d/restaurant.conf << EOF
[restaurant]
user = www-data
group = www-data
listen = /var/run/php/php8.1-fpm-restaurant.sock
listen.owner = www-data
listen.group = www-data
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.process_idle_timeout = 10s
pm.max_requests = 500
php_admin_value[upload_max_filesize] = 10M
php_admin_value[post_max_size] = 10M
php_admin_value[max_execution_time] = 300
php_admin_value[max_input_time] = 300
php_admin_value[memory_limit] = 256M
EOF

echo -e "${YELLOW}Step 6: Setting up Nginx configuration...${NC}"
# Copy nginx config
cp nginx.conf $NGINX_AVAILABLE/$DOMAIN

# Update socket path in nginx config
sed -i 's|unix:/var/run/php/php8.1-fpm.sock|unix:/var/run/php/php8.1-fpm-restaurant.sock|g' $NGINX_AVAILABLE/$DOMAIN

# Enable site
ln -sf $NGINX_AVAILABLE/$DOMAIN $NGINX_ENABLED/$DOMAIN

# Remove default site if exists
if [ -f "$NGINX_ENABLED/default" ]; then
    rm $NGINX_ENABLED/default
fi

echo -e "${YELLOW}Step 7: Adding rate limiting to nginx.conf...${NC}"
# Add rate limiting if not exists
if ! grep -q "limit_req_zone" /etc/nginx/nginx.conf; then
    sed -i '/http {/a\\n\t# Rate limiting zones\n\tlimit_req_zone $binary_remote_addr zone=login:10m rate=1r/s;\n\tlimit_req_zone $binary_remote_addr zone=api:10m rate=10r/s;' /etc/nginx/nginx.conf
fi

echo -e "${YELLOW}Step 8: Setting up SSL (Let's Encrypt)...${NC}"
if command -v certbot &> /dev/null; then
    echo "Certbot found, setting up SSL..."
    certbot --nginx -d $DOMAIN -d www.$DOMAIN --non-interactive --agree-tos --email admin@$DOMAIN
else
    echo -e "${YELLOW}Certbot not found. Installing...${NC}"
    apt-get install -y certbot python3-certbot-nginx
    echo -e "${GREEN}Run the following command manually to setup SSL:${NC}"
    echo "certbot --nginx -d $DOMAIN -d www.$DOMAIN"
fi

echo -e "${YELLOW}Step 9: Configuring firewall...${NC}"
if command -v ufw &> /dev/null; then
    ufw allow 'Nginx Full'
    ufw allow ssh
    echo -e "${GREEN}✓ Firewall configured${NC}"
fi

echo -e "${YELLOW}Step 10: Starting services...${NC}"
systemctl restart php8.1-fpm
systemctl restart nginx
systemctl enable php8.1-fpm
systemctl enable nginx

echo -e "${YELLOW}Step 11: Testing configuration...${NC}"
nginx -t
php-fpm8.1 -t

echo -e "${GREEN}✓ Nginx configuration test passed${NC}"

echo -e "${YELLOW}Step 12: Setting up database configuration...${NC}"
if [ -f "$PROJECT_PATH/config/database.php" ]; then
    echo "Please update your database configuration in:"
    echo "$PROJECT_PATH/config/database.php"
    echo ""
    echo "Example configuration:"
    echo "define('DB_HOST', 'localhost');"
    echo "define('DB_NAME', 'restaurant_db');"
    echo "define('DB_USER', 'restaurant_user');"
    echo "define('DB_PASS', 'your_password');"
fi

echo ""
echo -e "${GREEN}🎉 Setup completed successfully!${NC}"
echo ""
echo -e "${YELLOW}Next steps:${NC}"
echo "1. Update DNS records to point $DOMAIN to this server"
echo "2. Configure database settings in $PROJECT_PATH/config/database.php"
echo "3. Run the installer at https://$DOMAIN/install/install.php"
echo "4. Check logs at /var/log/nginx/$DOMAIN.*.log"
echo ""
echo -e "${YELLOW}Useful commands:${NC}"
echo "• Test nginx config: nginx -t"
echo "• Reload nginx: systemctl reload nginx"
echo "• View nginx logs: tail -f /var/log/nginx/$DOMAIN.access.log"
echo "• View error logs: tail -f /var/log/nginx/$DOMAIN.error.log"
echo "• Restart PHP-FPM: systemctl restart php8.1-fpm"
echo ""
echo -e "${GREEN}Your restaurant management system is ready! 🍕${NC}"
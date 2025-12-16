#!/bin/bash

# Configuration
DB_NAME="socialnets"
DB_USER="socialnets"
DB_PASS="secret_password" # Change this!
SITE_DOMAIN="your-domain.com" # Change this or use IP
PROJECT_DIR="/var/www/socialnets"
GITHUB_REPO_URL="https://github.com/your-repo/socialnets.git" # Change this

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${GREEN}Starting Social Network Deployment...${NC}"

# Check if running as root
if [ "$EUID" -ne 0 ]; then
  echo -e "${RED}Please run as root${NC}"
  exit 1
fi

# 1. Update and Install Dependencies
echo -e "${GREEN}Updating system and installing dependencies...${NC}"
apt-get update
apt-get install -y apache2 php php-mysql php-curl php-json php-xml mysql-server git composer unzip

# Enable mod_rewrite
a2enmod rewrite
systemctl restart apache2

# 2. Database Setup
echo -e "${GREEN}Setting up MySQL database...${NC}"
mysql -u root -e "CREATE DATABASE IF NOT EXISTS ${DB_NAME};"
mysql -u root -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -u root -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';"
mysql -u root -e "FLUSH PRIVILEGES;"

# 3. Project Deployment (assuming this script is running from the repo or we clone it)
# If we are already in the project dir (e.g. cloned manually), we skip cloning
if [ -d "$PROJECT_DIR" ]; then
    echo -e "${GREEN}Project directory exists. Pulling latest changes...${NC}"
    cd $PROJECT_DIR
    git pull
else
    echo -e "${GREEN}Cloning repository...${NC}"
    # This part assumes this script might be run standalone. 
    # If this script is INSIDE the repo, the user probably already cloned it.
    # But let's assume standard path usage.
    if [ "$PWD" != "$PROJECT_DIR" ]; then
         mkdir -p /var/www
         # Note: You need to replace the repo URL
         # git clone $GITHUB_REPO_URL $PROJECT_DIR
         echo -e "${RED}Please ensure the project is at $PROJECT_DIR${NC}"
         # For this script to be useful when included in the repo:
         # We assume the user ran 'git clone ... socialnets' and is running this script from inside.
         # So we will copy the current files to the target if it's different, OR just set permissions if we are already there.
    fi
fi

# Let's assume the user is running this script from inside /var/www/socialnets
# or we are setting up the current directory.

CURRENT_DIR=$(pwd)
if [ "$CURRENT_DIR" != "$PROJECT_DIR" ]; then
    echo -e "${GREEN}Moving/Copying project files to $PROJECT_DIR...${NC}"
    # If the script is run from a downloaded folder but not the final destination
    if [ ! -d "$PROJECT_DIR" ]; then
        mkdir -p $(dirname $PROJECT_DIR)
        cp -r . $PROJECT_DIR
    fi
fi

# Ensure we are in the project dir
cd $PROJECT_DIR

# Install PHP dependencies
echo -e "${GREEN}Installing Composer dependencies...${NC}"
# Use --no-interaction to avoid prompts
composer install --no-interaction --optimize-autoloader --no-dev

# Permissions
echo -e "${GREEN}Setting permissions...${NC}"
chown -R www-data:www-data $PROJECT_DIR
chmod -R 755 $PROJECT_DIR
if [ -d "storage" ]; then
    chmod -R 777 storage
fi

# 4. Configuration
echo -e "${GREEN}Configuring application...${NC}"
if [ -f "config/database.php.example" ]; then
    cp config/database.php.example config/database.php
    # Sed to replace placeholder password
    sed -i "s/YOUR_PASSWORD/${DB_PASS}/" config/database.php
fi

# Initialize Database
echo -e "${GREEN}Initializing database tables...${NC}"
if [ -f "database/init.sql" ]; then
    mysql -u ${DB_USER} -p${DB_PASS} ${DB_NAME} < database/init.sql
fi

# 5. Apache Configuration
echo -e "${GREEN}Configuring Apache VirtualHost...${NC}"
cat > /etc/apache2/sites-available/socialnets.conf <<EOF
<VirtualHost *:80>
    ServerName ${SITE_DOMAIN}
    ServerAdmin webmaster@localhost
    DocumentRoot ${PROJECT_DIR}/public

    <Directory ${PROJECT_DIR}/public>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF

a2ensite socialnets
a2dissite 000-default.conf
systemctl restart apache2

echo -e "${GREEN}Deployment complete!${NC}"
echo -e "Don't forget to update your DNS to point to this server."

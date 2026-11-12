#!/bin/bash
# Docker Entrypoint Script
# Waits for MySQL to be ready before starting Apache

set -e

echo "==========================================="
echo "Website Desa - Docker Startup"
echo "==========================================="

# Function to check MySQL connection
wait_for_mysql() {
    echo "Waiting for MySQL to be ready..."
    
    MAX_TRIES=60
    COUNT=0
    
    until php -r "
        \$conn = @new mysqli(
            getenv('DB_HOST') ?: 'mysql',
            getenv('DB_USER') ?: 'desa_user',
            getenv('DB_PASS') ?: 'desa_pass_2024',
            getenv('DB_NAME') ?: 'db_desa_sejahtera',
            getenv('DB_PORT') ?: 3306
        );
        exit(\$conn->connect_error ? 1 : 0);
    "; do
        COUNT=$((COUNT + 1))
        
        if [ $COUNT -ge $MAX_TRIES ]; then
            echo "ERROR: MySQL not ready after ${MAX_TRIES} attempts"
            echo "Starting Apache anyway (will show error page)..."
            break
        fi
        
        echo "MySQL not ready yet (attempt $COUNT/$MAX_TRIES)..."
        sleep 2
    done
    
    if [ $COUNT -lt $MAX_TRIES ]; then
        echo "✓ MySQL is ready!"
    fi
}

# Check if we need to wait for MySQL
if [ -n "$DB_HOST" ]; then
    wait_for_mysql
fi

# Set proper permissions
echo "Setting up permissions..."
chown -R www-data:www-data /var/www/html/assets/uploads 2>/dev/null || true
chmod -R 755 /var/www/html/assets/uploads 2>/dev/null || true

# Enable Apache modules if not already enabled
echo "Enabling Apache modules..."
a2enmod rewrite headers expires deflate 2>/dev/null || true

echo "==========================================="
echo "Starting Apache..."
echo "==========================================="

# Execute the main command (apache2-foreground)
exec "$@"

#!/bin/bash
set -e

cd /var/www/html/backend

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Ensure queue table migrations are published
if [ ! -f database/migrations/*_create_jobs_table.php ]; then
    php artisan queue:table
    php artisan queue:failed-table
fi

# Migrate database
php artisan migrate --force

# Create storage symlink if it doesn't exist
php artisan storage:link

# Fix permissions
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html/backend/storage

# Create health check endpoint
mkdir -p /var/www/html/backend/public/api
echo '{"status":"ok"}' > /var/www/html/backend/public/api/health

# Start supervisor
exec "$@" 
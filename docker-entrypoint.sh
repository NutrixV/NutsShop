#!/bin/bash
set -e

cd /var/www/html/backend

# Fix permissions first
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html/backend/storage
chmod -R 775 /var/www/html/backend/bootstrap/cache

# Run cache commands as www-data
su -s /bin/bash -c "php artisan cache:clear" www-data
su -s /bin/bash -c "php artisan config:clear" www-data
su -s /bin/bash -c "php artisan view:clear" www-data
su -s /bin/bash -c "php artisan route:clear" www-data

# Create queue migration files if they don't exist yet
# This ensures the migrations are there, but Laravel will skip them if already run
if [ ! -f database/migrations/*_create_jobs_table.php ]; then
    su -s /bin/bash -c "php artisan queue:table" www-data
fi

if [ ! -f database/migrations/*_create_failed_jobs_table.php ]; then
    su -s /bin/bash -c "php artisan queue:failed-table" www-data
fi

# Run migrations with force flag - Laravel will automatically skip existing tables
su -s /bin/bash -c "php artisan migrate --force" www-data

# Create storage symlink if it doesn't exist
su -s /bin/bash -c "php artisan storage:link" www-data

# Create health check endpoint
mkdir -p /var/www/html/backend/public/api
echo '{"status":"ok"}' > /var/www/html/backend/public/api/health
chown www-data:www-data /var/www/html/backend/public/api/health

# Start supervisor
exec "$@" 
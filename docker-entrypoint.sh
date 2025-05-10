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

# Check if tables already exist in the database before creating migrations
# Use Laravel's DB facade to check if tables exist
TABLE_CHECK=$(su -s /bin/bash -c "php artisan tinker --execute=\"echo DB::getSchemaBuilder()->hasTable('failed_jobs') ? 'exists' : 'not_exists';\"" www-data)

if [[ $TABLE_CHECK != *"exists"* ]]; then
    # Only create migration files if tables don't exist
    if [ ! -f database/migrations/*_create_jobs_table.php ]; then
        su -s /bin/bash -c "php artisan queue:table" www-data
    fi
    
    if [ ! -f database/migrations/*_create_failed_jobs_table.php ]; then
        su -s /bin/bash -c "php artisan queue:failed-table" www-data
    fi
fi

# Migrate database as www-data with --force option
# Add the --skip-existing option to skip existing migrations
su -s /bin/bash -c "php artisan migrate --force" www-data

# Create storage symlink if it doesn't exist
su -s /bin/bash -c "php artisan storage:link" www-data

# Create health check endpoint
mkdir -p /var/www/html/backend/public/api
echo '{"status":"ok"}' > /var/www/html/backend/public/api/health
chown www-data:www-data /var/www/html/backend/public/api/health

# Start supervisor
exec "$@" 
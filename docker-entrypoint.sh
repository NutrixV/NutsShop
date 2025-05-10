#!/bin/bash
# Не використовуємо set -e, щоб уникнути зупинки скрипту при помилках міграції
# set -e 

cd /var/www/html/backend

# Fix permissions first
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html/backend/storage
chmod -R 775 /var/www/html/backend/bootstrap/cache

# Генеруємо новий ключ шифрування, якщо він відсутній або має неправильний формат
if [ -z "$APP_KEY" ] || [[ ! "$APP_KEY" =~ ^base64:.{44}$ ]]; then
    echo "Generating new encryption key..."
    # Створюємо тимчасовий файл .env, якщо його немає
    if [ ! -f .env ]; then
        echo "APP_NAME=NutsShop" > .env
        echo "APP_ENV=production" >> .env
        echo "APP_DEBUG=false" >> .env
    fi
    su -s /bin/bash -c "php artisan key:generate --force" www-data
    # Зчитуємо згенерований ключ з .env
    NEW_APP_KEY=$(grep APP_KEY .env | cut -d '=' -f2)
    # Експортуємо його як змінну середовища
    export APP_KEY=$NEW_APP_KEY
    echo "Generated new APP_KEY: $APP_KEY"
fi

# Run cache commands as www-data
su -s /bin/bash -c "php artisan cache:clear" www-data || true
su -s /bin/bash -c "php artisan config:clear" www-data
su -s /bin/bash -c "php artisan view:clear" www-data
su -s /bin/bash -c "php artisan route:clear" www-data

# Get database connection info from Laravel environment
DB_HOST=${DB_HOST:-postgres}
DB_PORT=${DB_PORT:-5432}
DB_DATABASE=${DB_DATABASE:-postgres}
DB_USERNAME=${DB_USERNAME:-postgres}
DB_PASSWORD=${DB_PASSWORD:-postgres}

# Check if tables already exist using psql (faster and more reliable than using Laravel)
JOBS_TABLE_EXISTS=$(PGPASSWORD="$DB_PASSWORD" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME" -d "$DB_DATABASE" -t -c "SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'jobs')" 2>/dev/null || echo "false")
FAILED_JOBS_TABLE_EXISTS=$(PGPASSWORD="$DB_PASSWORD" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME" -d "$DB_DATABASE" -t -c "SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'failed_jobs')" 2>/dev/null || echo "false")

# Create queue migration files only if tables don't exist
if [[ $JOBS_TABLE_EXISTS != *"t"* ]] && [ ! -f database/migrations/*_create_jobs_table.php ]; then
    su -s /bin/bash -c "php artisan queue:table" www-data
fi

if [[ $FAILED_JOBS_TABLE_EXISTS != *"t"* ]] && [ ! -f database/migrations/*_create_failed_jobs_table.php ]; then
    su -s /bin/bash -c "php artisan queue:failed-table" www-data
fi

# Run migrations with force flag and continue even if there are errors
su -s /bin/bash -c "php artisan migrate --force" www-data || true

# Create storage symlink if it doesn't exist
su -s /bin/bash -c "php artisan storage:link" www-data || true

# Create health check endpoint
mkdir -p /var/www/html/backend/public/api
echo '{"status":"ok"}' > /var/www/html/backend/public/api/health
chown www-data:www-data /var/www/html/backend/public/api/health

# Start supervisor
exec "$@" 
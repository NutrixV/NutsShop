#!/bin/bash
# Не використовуємо set -e, щоб уникнути зупинки скрипту при помилках міграції
# set -e 

cd /var/www/html/backend

# Fix permissions first
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html/backend/storage
chmod -R 775 /var/www/html/backend/bootstrap/cache

# Ensure we have a proper .env file
if [ ! -f .env ]; then
    echo "Creating new .env file..."
    cat > .env << EOF
APP_NAME=NutsShop
APP_ENV=production
APP_DEBUG=false
APP_URL=${APP_URL:-https://nutsshop-api.onrender.com}
FRONTEND_URL=${FRONTEND_URL:-https://nutsshop-frontend.onrender.com}
DB_CONNECTION=pgsql
DB_HOST=${DB_HOST:-postgres}
DB_PORT=${DB_PORT:-5432}
DB_DATABASE=${DB_DATABASE:-postgres}
DB_USERNAME=${DB_USERNAME:-postgres}
DB_PASSWORD=${DB_PASSWORD:-postgres}
LOG_CHANNEL=stderr
EOF
    chown www-data:www-data .env
    echo "Created .env file with content:"
    cat .env
fi

# Directly generate and set encryption key if needed
if [ -z "$APP_KEY" ] || ! grep -q "^APP_KEY=" .env; then
    # Generate a base64 key of correct length for Laravel
    echo "Generating new app key..."
    NEW_KEY="base64:$(openssl rand -base64 32)"
    echo "Generated key: $NEW_KEY"
    
    # Check if APP_KEY line exists and replace it, otherwise add it
    if grep -q "^APP_KEY=" .env; then
        sed -i "s/^APP_KEY=.*$/APP_KEY=$NEW_KEY/" .env
    else
        echo "APP_KEY=$NEW_KEY" >> .env
    fi
    
    # Set as environment variable for current process
    export APP_KEY=$NEW_KEY
    
    echo "Final .env file after key generation:"
    cat .env
    echo "Current APP_KEY environment variable: $APP_KEY"
else
    echo "Using existing APP_KEY: $APP_KEY"
fi

# Run cache commands as www-data (continue even if errors)
echo "Clearing Laravel cache..."
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

# Run tests if enabled by environment variable
if [ "${RUN_TESTS:-false}" = "true" ]; then
    echo "Running tests before deployment..."
    
    # Ensure dev dependencies are installed
    echo "Installing dev dependencies for testing..."
    su -s /bin/bash -c "composer install --no-interaction" www-data || true
    
    # Create test database
    echo "Creating SQLite test database..."
    touch database/database.sqlite
    chown www-data:www-data database/database.sqlite
    
    # Підготовка тестового середовища - виконання міграцій для тестів
    echo "Running migrations for test database..."
    su -s /bin/bash -c "DB_CONNECTION=sqlite DB_DATABASE=database/database.sqlite php artisan migrate --force" www-data
    
    # Створюємо директорію для логів тестів, якщо потрібно
    if [ "${TEST_LOG:-false}" = "true" ]; then
        mkdir -p tests
        touch tests/log
        chown -R www-data:www-data tests
        chmod 755 tests/log
    fi
    
    # Run tests
    echo "Executing tests..."
    TEST_RESULT=0
    
    if [ "${TEST_LOG:-false}" = "true" ]; then
        # Запускаємо тести з виводом у файл логів
        su -s /bin/bash -c "DB_CONNECTION=sqlite DB_DATABASE=database/database.sqlite php artisan test | tee tests/log" www-data || TEST_RESULT=$?
    else
        # Запускаємо тести без збереження логів
        su -s /bin/bash -c "DB_CONNECTION=sqlite DB_DATABASE=database/database.sqlite php artisan test" www-data || TEST_RESULT=$?
    fi
    
    if [ $TEST_RESULT -ne 0 ]; then
        echo "Tests failed. Deployment aborted."
        if [ "${ABORT_ON_TEST_FAILURE:-true}" = "true" ]; then
            exit $TEST_RESULT
        else 
            echo "Continuing deployment despite test failures..."
        fi
    else
        echo "All tests passed successfully."
    fi
    
    # Remove dev dependencies to keep the production image slim
    if [ "${KEEP_DEV_DEPS:-false}" = "false" ]; then
        echo "Removing dev dependencies..."
        su -s /bin/bash -c "composer install --no-interaction --no-dev --optimize-autoloader" www-data || true
    fi
fi

# Check if tables already exist using psql (faster and more reliable than using Laravel)
echo "Checking for existing database tables..."
JOBS_TABLE_EXISTS=$(PGPASSWORD="$DB_PASSWORD" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME" -d "$DB_DATABASE" -t -c "SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'jobs')" 2>/dev/null || echo "false")
FAILED_JOBS_TABLE_EXISTS=$(PGPASSWORD="$DB_PASSWORD" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME" -d "$DB_DATABASE" -t -c "SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'failed_jobs')" 2>/dev/null || echo "false")

echo "Jobs table exists: $JOBS_TABLE_EXISTS"
echo "Failed jobs table exists: $FAILED_JOBS_TABLE_EXISTS"

# Create queue migration files only if tables don't exist
if [[ $JOBS_TABLE_EXISTS != *"t"* ]] && [ ! -f database/migrations/*_create_jobs_table.php ]; then
    echo "Creating jobs table migration..."
    su -s /bin/bash -c "php artisan queue:table" www-data
fi

if [[ $FAILED_JOBS_TABLE_EXISTS != *"t"* ]] && [ ! -f database/migrations/*_create_failed_jobs_table.php ]; then
    echo "Creating failed jobs table migration..."
    su -s /bin/bash -c "php artisan queue:failed-table" www-data
fi

# Run migrations with force flag and continue even if there are errors
echo "Running migrations..."
su -s /bin/bash -c "php artisan migrate --force" www-data || true

# Create storage symlink if it doesn't exist
echo "Creating storage symlink..."
su -s /bin/bash -c "php artisan storage:link" www-data || true

# Create health check endpoint
echo "Creating health check endpoint..."
mkdir -p /var/www/html/backend/public/api
echo '{"status":"ok"}' > /var/www/html/backend/public/api/health
chown www-data:www-data /var/www/html/backend/public/api/health

# Show supervisor config for debugging
echo "Supervisor configuration:"
cat /etc/supervisor/conf.d/supervisord.conf

# Start supervisor
echo "Starting supervisor..."
exec "$@" 
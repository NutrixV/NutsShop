FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    unzip \
    nginx \
    supervisor \
    postgresql-client \
    libpq-dev \
    libicu-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip pdo_pgsql intl

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . /var/www/html

# Copy configuration files
COPY ./nginx_config.conf /etc/nginx/sites-available/default
COPY ./devops/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
RUN mkdir -p /etc/supervisor/logs

# Copy scripts
COPY ./docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Set working directory to backend
WORKDIR /var/www/html/backend

# Create all necessary Laravel cache directories and set permissions
RUN mkdir -p bootstrap/cache \
    && mkdir -p storage/framework/cache \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && chmod -R 775 bootstrap/cache \
    && chmod -R 775 storage/framework

# Set directory permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

# Install dependencies and run artisan commands as the www-data user
USER www-data
RUN composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader

# Create a basic .env file if it doesn't exist
RUN if [ ! -f .env ]; then \
    echo "APP_NAME=NutsShop" > .env && \
    echo "APP_ENV=production" >> .env && \
    echo "APP_DEBUG=false" >> .env && \
    echo "APP_URL=https://nutsshop-api.onrender.com" >> .env && \
    echo "FRONTEND_URL=https://nutsshop-frontend.onrender.com" >> .env && \
    echo "DB_CONNECTION=pgsql" >> .env && \
    echo "LOG_CHANNEL=stderr" >> .env; \
    fi

# Generate app key if needed
RUN php artisan key:generate --force

# Create storage symlink for public access to uploaded files
RUN php artisan storage:link

# Note: Migrations will be run in the entrypoint script to ensure database connection is available
USER root

# Expose port 8080
EXPOSE 8080

# Start services with supervisor
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"] 
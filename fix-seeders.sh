#!/bin/bash

# Виправлення прав доступу для директорій storage
echo "🔧 Виправлення прав доступу для storage..."
mkdir -p /var/www/storage/app/public/images/category
mkdir -p /var/www/storage/app/public/images/products
chown -R www-data:www-data /var/www/storage
chmod -R 775 /var/www/storage
echo "✅ Права доступу для storage виправлені"

# Створення символічних посилань для виправлення шляхів
echo "🔧 Створення символічних посилань для директорій..."
if [ ! -L "/var/www/database" ]; then
    ln -sf /var/www/html/backend/database /var/www/database
    echo "✅ Створено посилання: /var/www/database -> /var/www/html/backend/database"
else
    echo "ℹ️ Посилання /var/www/database вже існує"
fi

# Перевірка наявності директорій з зображеннями
echo "🔍 Перевірка наявності директорій з зображеннями..."
if [ -d "/var/www/html/backend/database/seeders/images/category" ]; then
    echo "✅ Директорія з зображеннями категорій знайдена"
else
    echo "❌ Директорія /var/www/html/backend/database/seeders/images/category не знайдена!"
fi

if [ -d "/var/www/html/backend/database/seeders/images/products" ]; then
    echo "✅ Директорія з зображеннями продуктів знайдена"
else
    echo "❌ Директорія /var/www/html/backend/database/seeders/images/products не знайдена!"
fi

# Перевірка наявності CSV-файлів
echo "🔍 Перевірка наявності CSV-файлів..."
if [ -f "/var/www/html/backend/database/seeders/fixture/products.csv" ]; then
    echo "✅ CSV-файл продуктів знайдено"
else
    echo "❌ CSV-файл /var/www/html/backend/database/seeders/fixture/products.csv не знайдено!"
fi

echo ""
echo "🚀 Тепер можна запустити сідери:"
echo "su -s /bin/bash -c \"php artisan db:seed --force\" www-data" 
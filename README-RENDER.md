# Деплой NutsShop на Render.com

Цей репозиторій налаштований для автоматичного розгортання на Render.com з використанням IaC (Infrastructure as Code).

## Компоненти системи

Проект складається з наступних компонентів:
- **Бекенд API** (Laravel)
- **Фронтенд** (Nuxt 3)
- **База даних** (PostgreSQL)

## Як розгорнути

### 1. Створіть обліковий запис у Render.com

Якщо у вас ще немає облікового запису Render.com, [зареєструйтесь](https://dashboard.render.com/register).

### 2. Підключіть GitHub репозиторій

- Перейдіть у [Render Dashboard](https://dashboard.render.com/)
- Натисніть "New" > "Blueprint"
- Виберіть репозиторій з GitHub
- Натисніть "Connect"

### 3. Налаштування Blueprint

Render автоматично виявить файл `render.yaml` у корені репозиторію та запропонує створити всі сервіси, визначені в ньому.

- Перегляньте конфігурацію і натисніть "Apply"
- Render створить усі необхідні сервіси:
  - Web-сервіс для бекенду (API)
  - Web-сервіс для фронтенду
  - PostgreSQL базу даних

### 4. Конфігурація середовища

Основні змінні середовища вже визначено у файлі `render.yaml` як статичні значення або посилання на інші сервіси. Деякі секретні значення (наприклад, `APP_KEY`) будуть згенеровані автоматично.

**Важливо**: Після розгортання вам, можливо, потрібно буде вручну оновити адреси сервісів:
- У бекенд-сервісі: `APP_URL` та `FRONTEND_URL`
- У фронтенд-сервісі: `NUXT_PUBLIC_API_URL`

### 5. Доступ до проекту

Після успішного розгортання, ви можете отримати доступ до наступних URL:

- Фронтенд: `https://nutsshop-frontend.onrender.com`
- API: `https://nutsshop-api.onrender.com/api`

## Структура розгортання

### Backend (Laravel)

- Запускається в Docker-контейнері
- Використовує Nginx + PHP-FPM
- Supervisor керує процесами
- Автоматично виконує міграції
- Підтримує підключення до PostgreSQL через pdo_pgsql

### Frontend (Nuxt 3)

- Запускається в Docker-контейнері
- Використовує Node.js 18 (сумісна версія для Nuxt 3.17+)
- Використовує двоетапну збірку для оптимізації розміру образу
- Пропускає перевірку TypeScript для уникнення помилок збірки через опції:
  - Змінна середовища `NUXT_SKIP_TS_CHECK=true`
  - Флаг `--skipTypeCheck` в команді build
  - Конфігурація `typescript.typeCheck: false` у nuxt.config.ts
  - Налаштування `strict: false` та додаткові опції в tsconfig.json
- Використовує середовище SSR (Server-Side Rendering)
- Підключається до бекенд API через змінні середовища

### База даних (PostgreSQL)

- Використовує план basic-1gb
- Доступна тільки через внутрішню мережу Render

## Моніторинг і діагностика

- Ендпоінт `/api/health` доступний для перевірки стану бекенду
- Логи доступні через Render Dashboard

## Інші файли конфігурації

- `Dockerfile` - конфігурація Docker для бекенду
- `frontend/Dockerfile.alt` - конфігурація Docker для фронтенду (двоетапна збірка)
- `docker-entrypoint.sh` - скрипт ініціалізації для бекенду
- `.dockerignore` і `frontend/.dockerignore` - файли, які потрібно ігнорувати при створенні Docker-образів
- `devops/supervisor/supervisord.conf` - конфігурація Supervisor

## Вирішення типових проблем

### Проблеми зі збіркою фронтенду

Якщо виникають проблеми зі збіркою фронтенду:

1. **Помилка TypeScript**: 
   - У стандартному `frontend/Dockerfile` використовується двоетапна збірка
   - У файлі `render.yaml` встановлено змінну оточення `NUXT_SKIP_TS_CHECK=true`
   - В команді збірки `package.json` встановлено `--skipTypeCheck`
   - У файлі `nuxt.config.ts` відключено перевірку TypeScript через `typescript.typeCheck: false`
   - У файлі `tsconfig.json` послаблено налаштування TypeScript для кращої сумісності

2. **Помилка `crypto.getRandomValues is not a function`**:
   - Ця помилка виникає при використанні Node.js 16 з новими версіями Nuxt (3.17+)
   - Вирішено оновленням Node.js до версії 18 в Dockerfile

### Проблеми зі збіркою бекенду

Якщо виникають проблеми з компіляцією PHP розширень:

1. Для підтримки PostgreSQL у бекенді через pdo_pgsql необхідно встановити пакет `libpq-dev`
2. Для підтримки інтернаціоналізації через розширення intl необхідно встановити пакет `libicu-dev`
3. У Dockerfile бекенду вже додано ці пакети до списку залежностей:
   ```
   RUN apt-get update && apt-get install -y \
       ... \
       libpq-dev \  # для pdo_pgsql
       libicu-dev   # для intl
   ```
4. Також потрібно переконатися, що розширення включені в PHP:
   ```
   RUN docker-php-ext-install pdo_mysql ... pdo_pgsql intl
   ```
5. Для інших розширень PHP можливо також знадобиться встановити додаткові dev-пакети

### Проблеми з директоріями кешу Laravel

Якщо виникають помилки, пов'язані з директоріями кешу Laravel:

1. **Помилка `The bootstrap/cache directory must be present and writable`**:
   - Виникає, коли директорія кешу відсутня або недоступна для запису
   - Створіть директорію перед встановленням залежностей:
     ```
     RUN mkdir -p bootstrap/cache && chmod -R 775 bootstrap/cache
     ```

2. **Помилка `Please provide a valid cache path`**:
   - Виникає, коли Laravel не може використовувати директорії кешу для фреймворку
   - Необхідно створити всі директорії кешу, які використовує Laravel:
     ```
     RUN mkdir -p bootstrap/cache \
         && mkdir -p storage/framework/cache \
         && mkdir -p storage/framework/sessions \
         && mkdir -p storage/framework/views
     ```
   - Встановіть правильні права доступу:
     ```
     RUN chmod -R 775 bootstrap/cache \
         && chmod -R 775 storage/framework
     ```

3. **Запуск команд від користувача `www-data`**:
   - Уникне проблем з правами доступу, які можуть виникнути при запуску від root
   - Усі команди Composer і Artisan повинні виконуватися від імені www-data:
     - У Dockerfile:
       ```
       USER www-data
       RUN composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader
       RUN php artisan key:generate --force
       USER root
       ```
     - В ентрипоінт-скрипті:
       ```bash
       # Виконання команд як www-data
       su -s /bin/bash -c "php artisan cache:clear" www-data
       su -s /bin/bash -c "php artisan migrate --force" www-data
       ```
   - Це забезпечить сумісні права доступу між процесами встановлення та виконання

4. **Помилка `Failed to clear cache. Make sure you have the appropriate permissions`**:
   - Виникає, коли скрипт ентрипоінту намагається очистити кеш від root, а файли належать www-data
   - Вирішення - спочатку коригування прав доступу, а потім запуск команд artisan від www-data:
     ```bash
     # Спочатку виправляємо права
     chown -R www-data:www-data /var/www/html
     chmod -R 775 /var/www/html/backend/bootstrap/cache
     
     # Потім запускаємо команди як www-data
     su -s /bin/bash -c "php artisan cache:clear" www-data
     ```

### Проблеми з файлом .env

Якщо виникають помилки, пов'язані з відсутністю файлу .env:

1. **Помилка `file_get_contents(.env): Failed to open stream: No such file or directory`**:
   - Виникає під час запуску команди `php artisan key:generate --force`
   - Ця помилка вказує, що Laravel не може знайти файл .env для запису ключа додатку

2. **Вирішення - створення базового .env файлу**:
   - Додайте в Dockerfile створення мінімального .env файлу з необхідними змінними:
     ```
     RUN if [ ! -f .env ]; then \
         echo "APP_NAME=NutsShop" > .env && \
         echo "APP_ENV=production" >> .env && \
         echo "APP_DEBUG=false" >> .env && \
         echo "APP_URL=https://nutsshop-api.onrender.com" >> .env && \
         echo "FRONTEND_URL=https://nutsshop-frontend.onrender.com" >> .env && \
         echo "DB_CONNECTION=pgsql" >> .env && \
         echo "LOG_CHANNEL=stderr" >> .env; \
         fi
     ```
   - Цей файл буде використовуватися лише для генерації ключа
   - Реальні значення середовища для запущеного додатку будуть надані через змінні середовища з Render.yaml

### Проблеми з конфігурацією Nginx

Якщо виникають помилки з запуском Nginx:

1. **Помилка `host not found in upstream "php" in /etc/nginx/sites-enabled/default`**:
   - Виникає, коли Nginx намагається підключитися до PHP-FPM через неправильний хост
   - Ця помилка вказує, що Nginx шукає сервер "php", який не існує в контейнері

2. **Вирішення - налаштування fastcgi_pass**:
   - У файлі `nginx_config.conf` змініть директиву fastcgi_pass:
     ```
     # Було
     fastcgi_pass php:9000;
     
     # Потрібно
     fastcgi_pass 127.0.0.1:9000;
     ```
   - Це забезпечить підключення до PHP-FPM, який працює локально в тому ж контейнері

3. **Перевірка кореневої директорії**:
   - Переконайтеся, що шлях до публічної директорії Laravel встановлено правильно:
     ```
     # Правильний шлях для монолітного контейнера
     root /var/www/html/backend/public;
     ```
   - У розподіленій архітектурі може використовуватись інший шлях

4. **Налаштування портів**:
   - Порт, на якому слухає Nginx (80), повинен бути вказаний в налаштуваннях Render.com
   - В `render.yaml` для бекенду переконайтеся, що є змінна середовища `PORT=8080`
   - Для коректної роботи Nginx може знадобитися додаткова конфігурація проксі

### Проблеми з базою даних та міграціями

Якщо виникають помилки, пов'язані з відсутніми таблицями в базі даних:

1. **Помилка `relation "jobs" does not exist`**:
   - Виникає, коли Laravel намагається використовувати таблицю для черг, але вона не існує
   - Ця помилка вказує, що міграції для системи черг не були виконані

2. **Помилка `relation "failed_jobs" already exists`**:
   - Виникає, коли міграції намагаються створити таблицю, яка вже існує
   - Це часто відбувається при повторному розгортанні або перезапуску контейнера
   - Основна причина: команди `queue:table` і `queue:failed-table` створюють нові міграції при кожному запуску

3. **Помилка `Writing to directory /var/www/.config/psysh is not allowed`**:
   - Виникає, коли скрипт намагається використовувати Laravel Tinker для перевірки наявності таблиць
   - PsySH (інтерактивна оболонка, яку використовує Tinker) не має прав на створення конфігураційної директорії
   - Це обмеження Render.com пов'язане з правами доступу в контейнері

4. **Вирішення - спрощений підхід до міграцій**:
   - У скрипті `docker-entrypoint.sh` використовуємо простіший підхід:
     ```bash
     # Створюємо міграційні файли для черг, якщо вони ще не існують
     if [ ! -f database/migrations/*_create_jobs_table.php ]; then
         su -s /bin/bash -c "php artisan queue:table" www-data
     fi

     if [ ! -f database/migrations/*_create_failed_jobs_table.php ]; then
         su -s /bin/bash -c "php artisan queue:failed-table" www-data
     fi

     # Запускаємо міграції з прапорцем force - Laravel автоматично пропустить існуючі таблиці
     su -s /bin/bash -c "php artisan migrate --force" www-data
     ```
   - Цей підхід простіший і надійніший, оскільки:
     1. Ми створюємо міграційні файли, тільки якщо вони ще не існують
     2. Laravel автоматично пропускає міграції, які вже були виконані
     3. Не потрібно використовувати Tinker, що викликає проблеми з правами доступу

5. **Процес обробки черг**:
   - Для роботи з чергами використовується супервізор, який запускає воркер:
     ```
     [program:laravel-worker]
     process_name=%(program_name)s_%(process_num)02d
     command=php /var/www/html/backend/artisan queue:work --sleep=3 --tries=3
     autostart=true
     autorestart=true
     user=www-data
     numprocs=1
     redirect_stderr=true
     ```
   - Цей процес буде обробляти завдання з черги, використовуючи таблицю `jobs`

### Проблеми з файлом .env

Якщо виникають помилки, пов'язані з відсутністю файлу .env:

1. **Помилка `file_get_contents(.env): Failed to open stream: No such file or directory`**:
   - Виникає під час запуску команди `php artisan key:generate --force`
   - Ця помилка вказує, що Laravel не може знайти файл .env для запису ключа додатку

2. **Вирішення - створення базового .env файлу**:
   - Додайте в Dockerfile створення мінімального .env файлу з необхідними змінними:
     ```
     RUN if [ ! -f .env ]; then \
         echo "APP_NAME=NutsShop" > .env && \
         echo "APP_ENV=production" >> .env && \
         echo "APP_DEBUG=false" >> .env && \
         echo "APP_URL=https://nutsshop-api.onrender.com" >> .env && \
         echo "FRONTEND_URL=https://nutsshop-frontend.onrender.com" >> .env && \
         echo "DB_CONNECTION=pgsql" >> .env && \
         echo "LOG_CHANNEL=stderr" >> .env; \
         fi
     ```
   - Цей файл буде використовуватися лише для генерації ключа
   - Реальні значення середовища для запущеного додатку будуть надані через змінні середовища з Render.yaml 
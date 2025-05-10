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
     ```
     USER www-data
     RUN composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader
     RUN php artisan key:generate --force
     USER root
     ```
   - Це забезпечить сумісні права доступу між процесами встановлення та виконання

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

### Проблеми з базою даних та міграціями

Якщо виникають помилки, пов'язані з відсутніми таблицями в базі даних:

1. **Помилка `relation "jobs" does not exist`**:
   - Виникає, коли Laravel намагається використовувати таблицю для черг, але вона не існує
   - Ця помилка вказує, що міграції для системи черг не були виконані

2. **Вирішення - публікація та запуск міграцій**:
   - У скрипті `docker-entrypoint.sh` перевіряємо наявність міграції для черг і публікуємо її, якщо необхідно:
     ```bash
     # Ensure queue table migrations are published
     if [ ! -f database/migrations/*_create_jobs_table.php ]; then
         php artisan queue:table
         php artisan queue:failed-table
     fi
     
     # Migrate database
     php artisan migrate --force
     ```
   - Це забезпечить створення необхідних таблиць для роботи системи черг Laravel
   - Міграції запускаються в ентрипоінт-скрипті, а не в Dockerfile, щоб гарантувати доступність бази даних

3. **Процес обробки черг**:
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
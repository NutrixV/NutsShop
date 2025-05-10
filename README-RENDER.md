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

### Проблеми з ключем шифрування Laravel

Якщо виникають помилки, пов'язані з шифруванням:

1. **Помилка `Unsupported cipher or incorrect key length. Supported ciphers are: aes-128-cbc, aes-256-cbc, aes-128-gcm, aes-256-gcm`**:
   - Виникає, коли Laravel не може використовувати ключ APP_KEY для шифрування
   - Можливі причини:
     - Ключ має неправильну довжину
     - Ключ має неправильний формат (повинен починатися з "base64:")
     - Ключ не передається через змінну середовища
     - Ключ генерується під час збірки образу, але не зберігається для запуску

2. **Помилка `No application encryption key has been specified` або `Unable to set application key. No APP_KEY variable was found in the .env file`**:
   - Виникає, коли Laravel не може знайти ключ APP_KEY ні в змінних середовища, ні в файлі .env
   - Часто зустрічається при використанні команди `php artisan key:generate --force` в контейнері Docker
   - Проблема може бути пов'язана з тим, що .env файл не існує або не містить APP_KEY

3. **Вирішення - пряма генерація ключа за допомогою openssl**:
   - Використовуйте в скрипті `docker-entrypoint.sh` прямий метод генерації ключа:
     ```bash
     # Створюємо файл .env з необхідними змінними
     if [ ! -f .env ]; then
         cat > .env << EOF
     APP_NAME=NutsShop
     APP_ENV=production
     APP_DEBUG=false
     APP_URL=${APP_URL:-https://nutsshop-api.onrender.com}
     # ... інші змінні ...
     EOF
     fi
     
     # Генеруємо і встановлюємо ключ шифрування
     if [ -z "$APP_KEY" ] || ! grep -q "^APP_KEY=" .env; then
         # Генеруємо base64 ключ правильної довжини для Laravel
         NEW_KEY="base64:$(openssl rand -base64 32)"
         
         # Додаємо або замінюємо рядок APP_KEY в .env
         if grep -q "^APP_KEY=" .env; then
             sed -i "s/^APP_KEY=.*$/APP_KEY=$NEW_KEY/" .env
         else
             echo "APP_KEY=$NEW_KEY" >> .env
         fi
         
         # Встановлюємо як змінну середовища для поточного процесу
         export APP_KEY=$NEW_KEY
     fi
     ```
   - Цей підхід:
     1. Не використовує `php artisan key:generate` і тому працює стабільніше
     2. Створює ключ правильного формату і довжини для Laravel
     3. Зберігає ключ і в файлі .env, і як змінну середовища
     4. Підтримує як створення нового ключа, так і використання існуючого

4. **Збереження ключа в Render.com**:
   - Після першого успішного запуску знайдіть в логах рядок `Generated key: base64:...`
   - Додайте цей ключ як статичне значення в `render.yaml`:
     ```yaml
     - key: APP_KEY
       value: "base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx="  # Фактичний ключ з логів
     ```
   - Або встановіть його через інтерфейс Render.com у змінні середовища сервісу
   - Після збереження ключа в налаштуваннях Render.com, він буде доступний як змінна середовища при наступних запусках

### Проблеми з конфігурацією Nginx

Якщо виникають помилки з запуском Nginx:

1. **Помилка `host not found in upstream "php" in /etc/nginx/sites-enabled/default`**:
   - Виникає, коли Nginx намагається підключитися до PHP-FPM через неправильний хост
   - Ця помилка вказує, що Nginx шукає сервер "php", який не існує в контейнері

2. **Помилка `host not found in upstream "frontend" in /etc/nginx/sites-enabled/default`**:
   - Виникає, коли Nginx намагається підключитися до сервера "frontend", який не існує
   - Ця помилка виникає при використанні монолітного контейнера на Render.com
   - У конфігурації Nginx не повинно бути другого блоку server для фронтенду

3. **Вирішення - налаштування fastcgi_pass і видалення блоку frontend**:
   - У файлі `nginx_config.conf` змініть директиву fastcgi_pass:
     ```
     # Було
     fastcgi_pass php:9000;
     
     # Потрібно
     fastcgi_pass 127.0.0.1:9000;
     ```
   - Закоментуйте або видаліть другий блок сервера для фронтенду:
     ```
     # Закоментуйте цей блок в монолітному середовищі
     # server {
     #     listen 80;
     #     server_name frontend;
     #     ...
     # }
     ```
   - У розподіленій архітектурі з окремими сервісами ці блоки можуть бути потрібні

4. **Перевірка кореневої директорії**:
   - Переконайтеся, що шлях до публічної директорії Laravel встановлено правильно:
     ```
     # Правильний шлях для монолітного контейнера
     root /var/www/html/backend/public;
     ```
   - У розподіленій архітектурі може використовуватись інший шлях

5. **Налаштування портів**:
   - Порт, на якому слухає Nginx (80), повинен бути вказаний в налаштуваннях Render.com
   - В `render.yaml` для бекенду переконайтеся, що є змінна середовища `PORT=8080`
   - Для коректної роботи Nginx може знадобитися додаткова конфігурація проксі

6. **Помилка "Bad Gateway"**:
   - Ця помилка виникає, коли Render.com не може підключитися до вашого додатку
   - Основні причини:
     - Nginx не слухає на порту, вказаному в змінній `PORT` (зазвичай 8080)
     - Додаток не запускається через помилки в конфігурації
     - Проблеми з healthcheck ендпоінтом
   - Рішення:
     - Додайте додатковий блок server в nginx_config.conf, який слухає на порту 8080:
     ```
     server {
         listen 8080;
         server_name _;
         
         location / {
             proxy_pass http://localhost:80;
             proxy_set_header Host $host;
             proxy_set_header X-Real-IP $remote_addr;
             proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
             proxy_set_header X-Forwarded-Proto $scheme;
         }
     }
     ```
     - Перевірте, що у вашому контейнері відкритий порт 8080 (через EXPOSE в Dockerfile)
     - Переконайтеся, що змінна `PORT=8080` встановлена в render.yaml

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

4. **Вирішення - використання psql для перевірки таблиць**:
   - У скрипті `docker-entrypoint.sh` використовуємо прямий SQL-запит:
     ```bash
     # Отримуємо інформацію про підключення до бази даних з оточення Laravel
     DB_HOST=${DB_HOST:-postgres}
     DB_PORT=${DB_PORT:-5432}
     DB_DATABASE=${DB_DATABASE:-postgres}
     DB_USERNAME=${DB_USERNAME:-postgres}
     DB_PASSWORD=${DB_PASSWORD:-postgres}

     # Перевіряємо, чи існують таблиці, за допомогою psql
     JOBS_TABLE_EXISTS=$(PGPASSWORD="$DB_PASSWORD" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME" -d "$DB_DATABASE" -t -c "SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'jobs')" 2>/dev/null || echo "false")
     
     # Створюємо міграції, тільки якщо таблиці не існують
     if [[ $JOBS_TABLE_EXISTS != *"t"* ]] && [ ! -f database/migrations/*_create_jobs_table.php ]; then
         su -s /bin/bash -c "php artisan queue:table" www-data
     fi
     ```
   - Продовжуємо виконання скрипту навіть при помилках:
     ```bash
     # Виконуємо міграції та продовжуємо, навіть якщо є помилки
     su -s /bin/bash -c "php artisan migrate --force" www-data || true
     ```
   - Цей підхід надійніший, оскільки:
     1. Ми спочатку перевіряємо наявність таблиць у базі даних напряму через SQL
     2. Створюємо міграційні файли тільки для таблиць, яких не існує
     3. Продовжуємо виконання скрипту навіть при помилках міграції
     4. Не використовуємо Tinker, що викликає проблеми з правами доступу

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

### Проблеми з CORS

Якщо виникають помилки, пов'язані з CORS-політикою:

1. **Помилка `Access to fetch at 'URL' from origin 'ORIGIN' has been blocked by CORS policy`**:
   - Виникає, коли браузер блокує запити з одного домену (origin) до іншого через політику безпеки
   - Часто зустрічається при локальній розробці або коли фронтенд і бекенд знаходяться на різних доменах

2. **Вирішення - правильне налаштування CORS-заголовків**:
   - У файлі `nginx_config.conf` налаштуйте динамічні CORS-заголовки для підтримки як локального, так і продакшн середовища:
     ```nginx
     # CORS headers
     set $cors_origin "";
     if ($http_origin ~ "^(https://nutsshop-frontend\.onrender\.com|http://localhost:3000)$") {
         set $cors_origin $http_origin;
     }
     
     add_header Access-Control-Allow-Origin $cors_origin always;
     add_header Access-Control-Allow-Methods "GET, POST, OPTIONS, PUT, DELETE" always;
     add_header Access-Control-Allow-Headers "Content-Type, Authorization, X-Requested-With" always;
     add_header Access-Control-Allow-Credentials "true" always;
     ```
   - Додайте обробку preflight-запитів:
     ```nginx
     # Pre-flight request handling
     if ($request_method = OPTIONS) {
         return 204;
     }
     ```
   - Не забудьте додати ці заголовки до всіх блоків server

3. **Вирішення - правильне налаштування URL API на фронтенді**:
   - Переконайтеся, що фронтенд використовує правильний URL API:
     - Для локального середовища: `http://localhost:8090/api`
     - Для продакшн: `https://nutsshop-api.onrender.com/api`
   - У файлі `render.yaml` переконайтеся, що змінна `NUXT_PUBLIC_API_URL` встановлена правильно:
     ```yaml
     - key: NUXT_PUBLIC_API_URL
       value: https://nutsshop-api.onrender.com/api
     ```

4. **Тестування CORS-налаштувань**:
   - Використовуйте браузерні інструменти розробника (Network вкладка) для перевірки заголовків відповіді
   - Перевірте, чи присутній заголовок `Access-Control-Allow-Origin` з правильним значенням
   - Переконайтеся, що для OPTIONS-запитів сервер повертає статус 204 з правильними CORS-заголовками

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

### Проблеми з сідерами

Якщо виникають помилки при запуску сідерів, пов'язані з доступом до файлів чи директорій:

1. **Помилка `mkdir(): Permission denied`**:
   - Виникає, коли сідер намагається створити директорію, але не має відповідних прав
   - Ця помилка вказує на проблему з правами доступу до директорії `/var/www/storage`

2. **Помилка `Директорія не знайдена`**:
   - Виникає, коли сідер не може знайти директорії з вихідними зображеннями чи CSV-файлами
   - Сідери шукають файли за шляхами `/var/www/database/...` замість `/var/www/html/backend/database/...`

3. **Вирішення - скрипт для фіксації шляхів та прав**:
   - Створіть скрипт `fix-seeders.sh`:
     ```bash
     #!/bin/bash
     
     # Виправлення прав доступу для директорій storage
     mkdir -p /var/www/storage/app/public/images/category
     mkdir -p /var/www/storage/app/public/images/products
     chown -R www-data:www-data /var/www/storage
     chmod -R 775 /var/www/storage
     
     # Створення символічних посилань для виправлення шляхів
     if [ ! -L "/var/www/database" ]; then
         ln -sf /var/www/html/backend/database /var/www/database
     fi
     ```
   - Надайте скрипту права на виконання: `chmod +x fix-seeders.sh`
   - Запустіть скрипт перед виконанням сідерів: `./fix-seeders.sh`
   - Потім запустіть сідери: `su -s /bin/bash -c "php artisan db:seed --force" www-data`

4. **Модифікація сідерів** (для постійного вирішення):
   - Замініть абсолютні шляхи на відносні через хелпери Laravel:
     ```php
     // Було:
     $sourcePath = '/var/www/database/seeders/images/category';
     
     // Потрібно:
     $sourcePath = database_path('seeders/images/category');
     ```
   - Додайте обробку помилок для кращої стійкості:
     ```php
     if (!file_exists($sourcePath)) {
         $this->command->warn("Шлях не знайдено: {$sourcePath}");
         // Альтернативна логіка або пропуск
     }
     ``` 
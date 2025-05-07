# NutsShop - Магазин горішків та кондитерських виробів

## Про проект
Монорепозиторій для інтернет-магазину горішків та кондитерських виробів.

### Технології:
- **Бекенд**: Laravel 12 (REST API + Filament admin)
- **Фронтенд**: Nuxt 3 (TypeScript, SSR + SPA)
- **Інфраструктура**: Docker, Render

## Структура проекту
- `backend/` - Laravel 12 API + Filament адмінка
- `frontend/` - Nuxt 3 SPA
- `devops/` - Docker конфігурація та налаштування деплоя
- `scripts/` - Допоміжні скрипти для розробки та CI
- `docs/` - Документація проекту
- `.github/` - CI/CD конфігурація

## Розробка

### Вимоги
- Docker і Docker Compose
- PHP 8.2+
- Node.js 18+
- Composer 2.x

### Локальний запуск
```bash
# Клонувати репозиторій
git clone https://github.com/your-org/nuts-sweets-shop.git
cd nuts-sweets-shop

# Запустити Docker-контейнери
docker-compose up -d

# Встановити залежності та налаштувати бекенд
cd backend
composer install
php artisan migrate --seed

# Встановити залежності та запустити фронтенд
cd ../frontend
npm install
npm run dev
```

## Деплой
Проект налаштований для деплою на Render.com. Деплой виконується автоматично при push у гілку main. 
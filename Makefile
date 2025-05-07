.PHONY: up down restart build install install-backend install-frontend migrate seed key-generate test test-backend test-frontend refresh-db fresh-install lint fix-lint clean-docker ps logs

# Кольори для виводу
GREEN=\033[0;32m
YELLOW=\033[0;33m
RED=\033[0;31m
NC=\033[0m

# Основні команди Docker
up:
	@echo "${GREEN}Запуск Docker контейнерів...${NC}"
	docker compose up -d

down:
	@echo "${YELLOW}Зупинка Docker контейнерів...${NC}"
	docker compose down

restart: down up

build:
	@echo "${GREEN}Збірка Docker контейнерів...${NC}"
	docker compose build

ps:
	@echo "${GREEN}Перегляд запущених контейнерів...${NC}"
	docker compose ps

logs:
	@echo "${GREEN}Перегляд логів...${NC}"
	docker compose logs -f

# Команди встановлення залежностей
install: install-backend install-frontend

install-backend:
	@echo "${GREEN}Встановлення залежностей бекенду...${NC}"
	docker compose exec php composer install

install-frontend:
	@echo "${GREEN}Встановлення залежностей фронтенду...${NC}"
	docker compose exec frontend npm install

# Команди для роботи з базою даних
migrate:
	@echo "${GREEN}Запуск міграцій...${NC}"
	docker compose exec php php artisan migrate

seed:
	@echo "${GREEN}Запуск сідів...${NC}"
	docker compose exec php php artisan db:seed

refresh-db:
	@echo "${YELLOW}Оновлення бази даних...${NC}"
	docker compose exec php php artisan migrate:fresh --seed

# Ключі та налаштування
key-generate:
	@echo "${GREEN}Генерація ключа додатку...${NC}"
	docker compose exec php php artisan key:generate

# Тести
test: test-backend test-frontend

test-backend:
	@echo "${GREEN}Запуск тестів бекенду...${NC}"
	docker compose exec php php artisan test

test-frontend:
	@echo "${GREEN}Запуск тестів фронтенду...${NC}"
	docker compose exec frontend npm run test

# Лінтинг
lint:
	@echo "${GREEN}Перевірка коду...${NC}"
	docker compose exec php ./vendor/bin/pint --test
	docker compose exec frontend npm run lint

fix-lint:
	@echo "${GREEN}Виправлення помилок лінтингу...${NC}"
	docker compose exec php ./vendor/bin/pint
	docker compose exec frontend npm run lint:fix

# Очищення Docker
clean-docker:
	@echo "${RED}Видалення всіх Docker артефактів...${NC}"
	docker compose down -v --remove-orphans

# Повна інсталяція проекту
fresh-install: clean-docker build up install key-generate migrate seed
	@echo "${GREEN}Проект успішно налаштований!${NC}"

# Команда для створення нового адміністратора
create-admin:
	@echo "${GREEN}Створення нового адміністратора...${NC}"
	docker compose exec php php artisan make:filament-user

# За замовчуванням запуск проекту
default: up

help:
	@echo "${GREEN}Доступні команди:${NC}"
	@echo "  ${YELLOW}make up${NC}              - Запустити Docker контейнери"
	@echo "  ${YELLOW}make down${NC}            - Зупинити Docker контейнери"
	@echo "  ${YELLOW}make restart${NC}         - Перезапустити Docker контейнери"
	@echo "  ${YELLOW}make build${NC}           - Зібрати Docker контейнери"
	@echo "  ${YELLOW}make install${NC}         - Встановити залежності (backend + frontend)"
	@echo "  ${YELLOW}make migrate${NC}         - Запустити міграції БД"
	@echo "  ${YELLOW}make seed${NC}            - Запустити сіди БД"
	@echo "  ${YELLOW}make refresh-db${NC}      - Оновити базу даних (fresh + seed)"
	@echo "  ${YELLOW}make key-generate${NC}    - Згенерувати ключ додатку"
	@echo "  ${YELLOW}make test${NC}            - Запустити всі тести"
	@echo "  ${YELLOW}make lint${NC}            - Перевірити код на відповідність стандартам"
	@echo "  ${YELLOW}make fix-lint${NC}        - Виправити помилки лінтингу"
	@echo "  ${YELLOW}make clean-docker${NC}    - Видалити всі Docker артефакти"
	@echo "  ${YELLOW}make fresh-install${NC}   - Чиста інсталяція проекту"
	@echo "  ${YELLOW}make create-admin${NC}    - Створити нового адміністратора"
	@echo "  ${YELLOW}make ps${NC}              - Переглянути запущені контейнери"
	@echo "  ${YELLOW}make logs${NC}            - Переглянути логи"
	@echo "  ${YELLOW}make help${NC}            - Показати цю підказку" 
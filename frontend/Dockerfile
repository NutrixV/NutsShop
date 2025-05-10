FROM node:20-alpine

# Встановлюємо робочу директорію
WORKDIR /app

# Копіюємо package.json та package-lock.json
COPY package*.json ./

# Встановлюємо залежності
RUN npm ci

# Копіюємо решту файлів проекту
COPY . .

# Будуємо проект для виробництва
RUN npm run build

# Відкриваємо порт 3000
EXPOSE 3000

# Запускаємо сервер
CMD ["node", ".output/server/index.mjs"] 
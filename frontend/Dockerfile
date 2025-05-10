# Етап збірки
FROM node:18-alpine AS build

# Встановлюємо робочу директорію
WORKDIR /app

# Копіюємо package.json та package-lock.json
COPY package*.json ./

# Встановлюємо залежності
RUN npm ci

# Копіюємо решту файлів проекту
COPY . .

# Виправлення для estree-walker - додаємо exports в package.json
RUN if [ -d "node_modules/estree-walker" ]; then \
    echo '{"type":"module","exports":{"import":"./src/index.js","require":"./dist/estree-walker.umd.js"}}' > node_modules/estree-walker/package.json; \
    fi

# Змінна оточення для пропуску TypeScript перевірки
ENV NUXT_SKIP_TS_CHECK=true
ENV NITRO_PRESET=node-server

# Будуємо проект для виробництва
RUN npm run build

# Етап виробництва
FROM node:18-alpine

# Встановлюємо робочу директорію
WORKDIR /app

# Копіюємо тільки необхідні файли з етапу збірки
COPY --from=build /app/.output /app/.output
COPY --from=build /app/package.json /app/package.json

# Відкриваємо порт 3000
EXPOSE 3000

# Змінні оточення для виробництва
ENV HOST=0.0.0.0
ENV PORT=3000
ENV NODE_ENV=production

# Запускаємо сервер
CMD ["node", ".output/server/index.mjs"] 
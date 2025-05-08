# Виправлення помилок TypeScript у проекті

У проекті виявлено кілька помилок TypeScript, пов'язаних з імпортами та типами даних. Нижче наведено інструкції для їх виправлення.

## 1. Виправлення імпортів

### Проблема:
```
Module '"vue"' has no exported member 'computed'.
Module '"vue"' has no exported member 'watch'.
Cannot find module 'vue-router' or its corresponding type declarations.
```

### Рішення:
У Nuxt 3 більшість composable-функцій автоматично імпортуються, тому немає потреби імпортувати їх явно.

Змініть наступні файли:

#### 1. `/pages/catalog/index.vue`:
```typescript
<script setup lang="ts">
// Видалити рядки
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';

// Всі ці функції доступні глобально в Nuxt 3
```

#### 2. `/pages/cart.vue`:
```typescript
<script setup lang="ts">
// Видалити рядок
import { ref, computed, onMounted } from 'vue';

// Всі ці функції доступні глобально в Nuxt 3
```

#### 3. `/pages/product/[slug].vue`:
```typescript
<script setup lang="ts">
// Видалити рядки
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';

// Всі ці функції доступні глобально в Nuxt 3
```

## 2. Виправлення типів пов'язаних товарів

### Проблема:
```
Object literal may only specify known properties, but 'category_id' does not exist in type 'Product'.
Property 'in_stock' is missing in type '{ ... }' but required in type 'Product'.
```

### Рішення:
У файлі `/pages/product/[slug].vue` потрібно оновити структуру об'єктів пов'язаних товарів, щоб вони відповідали інтерфейсу `Product`.

1. Замінити властивість `category_id` на об'єкт `category`:
```typescript
// Замість
category_id: 1,

// Використовувати
category: {
  id: 1,
  name: 'Горіхи',
  slug: 'nuts'
},
```

2. Додати обов'язкову властивість `in_stock`:
```typescript
in_stock: true
```

Приклад повного об'єкта:
```typescript
{
  id: 2,
  name: 'Фісташки солоні',
  slug: 'fistashky-soloni',
  description: 'Хрусткі солоні фісташки. Ідеально підходять до пива або вина.',
  price: 320,
  category: {
    id: 1,
    name: 'Горіхи',
    slug: 'nuts'
  },
  image: '/images/products/pistachios.jpg',
  in_stock: true
}
```

## Альтернативний підхід для виправлення імпортів

Якщо глобальні автоімпорти не працюють (наприклад, через специфічну конфігурацію проекту), можна використати прямі імпорти з пакетів Nuxt:

```typescript
import { ref } from 'vue';
import { computed, onMounted, watch, useRoute, useRouter } from '#imports';
```

або прямі імпорти з Vue/Vue Router:

```typescript
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
```

## Після внесення змін

Після внесення змін перезапустіть фронтенд-контейнер:
```bash
docker restart nutsshop-frontend
``` 
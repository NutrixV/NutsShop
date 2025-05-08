# Правильне використання шляхів до зображень у Nuxt 3

## Звичайні зображення в компонентах

У компонентах Vue використовуйте відносні шляхи, починаючи з `/`:

```vue
<template>
  <!-- Правильно -->
  <img src="/images/logo.svg" alt="Logo">
  
  <!-- Неправильно -->
  <img src="http://localhost:3000/images/logo.svg" alt="Logo">
  <img src="images/logo.svg" alt="Logo"> <!-- без слешу на початку -->
</template>
```

## Використання з NuxtImg (якщо модуль @nuxt/image встановлено)

```vue
<template>
  <!-- Базовий приклад -->
  <NuxtImg src="/images/products/almonds.jpg" alt="Almonds" />
  
  <!-- З різними розмірами для різних пристроїв -->
  <NuxtImg 
    src="/images/products/almonds.jpg" 
    alt="Almonds"
    width="400"
    height="300"
    sizes="sm:100vw md:50vw lg:400px"
  />
</template>
```

## Динамічні зображення

```vue
<template>
  <div>
    <!-- Правильно -->
    <img :src="`/images/products/${product.image}`" :alt="product.name">
    
    <!-- Якщо зображення приходить з API з повним URL -->
    <img :src="product.image" :alt="product.name">
    
    <!-- Для зображень з backend сервера через проксі -->
    <img :src="`/api/images/${product.image}`" :alt="product.name">
  </div>
</template>
```

## CSS фони

```vue
<template>
  <div class="hero-banner">
    <!-- Контент -->
  </div>
</template>

<style scoped>
.hero-banner {
  /* Правильно */
  background-image: url('/images/hero-nuts.png');
  
  /* Неправильно */
  /* background-image: url('http://localhost:3000/images/hero-nuts.png'); */
}
</style>
```

## Динамічні CSS фони

```vue
<template>
  <div 
    class="category-banner"
    :style="{ backgroundImage: `url(/images/categories/${category.image})` }"
  >
    {{ category.name }}
  </div>
</template>
```

## Використання з useAssets (для активів у директорії assets/)

```vue
<script setup>
const imageUrl = useAssets('/images/logo.svg')
</script>

<template>
  <img :src="imageUrl" alt="Logo">
</template>
```

## Імпорт зображень безпосередньо у script

```vue
<script setup>
// Якщо файл в директорії public, використовуйте шлях без імпорту
const publicImagePath = '/images/logo.svg'

// Якщо файл в директорії assets, можна використовувати імпорт
import logoFromAssets from '~/assets/images/logo.svg'
</script>
```

## Рекомендації для Nuxt 3

1. Статичні файли розміщуйте в директорії `/public`
2. Для зображень, які потребують обробки, використовуйте директорію `/assets`
3. Завжди використовуйте відносні шляхи, починаючи з `/` для файлів у публічній директорії
4. Для динамічного контенту використовуйте API та правильні URL з бекенду 
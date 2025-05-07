<template>
  <div class="container mx-auto px-4 py-8">
    <div class="text-center mb-12">
      <h1 class="text-4xl font-bold text-primary mb-4">Ласкаво просимо до NutsShop</h1>
      <p class="text-xl text-gray-600">Найкращі горішки та кондитерські вироби за найкращими цінами</p>
    </div>

    <!-- Featured Products -->
    <section class="mb-16">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Популярні товари</h2>
        <NuxtLink to="/catalog" class="text-primary hover:underline">Переглянути всі</NuxtLink>
      </div>

      <div v-if="featuredProducts.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <ProductCard v-for="product in featuredProducts" :key="product.id" :product="product" />
      </div>
      <div v-else class="flex justify-center items-center h-40 bg-gray-100 rounded-lg">
        <p class="text-gray-500">Завантаження популярних товарів...</p>
      </div>
    </section>

    <!-- Categories Section -->
    <section class="mb-16">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Категорії</h2>
      </div>

      <div v-if="categories.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <NuxtLink 
          v-for="category in categories" 
          :key="category.id" 
          :to="`/catalog?category=${category.slug}`"
          class="relative overflow-hidden rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 group"
        >
          <img 
            :src="category.image || '/images/placeholder-category.jpg'" 
            :alt="category.name"
            class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
          />
          <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end justify-center p-4">
            <h3 class="text-white text-xl font-bold">{{ category.name }}</h3>
          </div>
        </NuxtLink>
      </div>
      <div v-else class="flex justify-center items-center h-40 bg-gray-100 rounded-lg">
        <p class="text-gray-500">Завантаження категорій...</p>
      </div>
    </section>

    <!-- Benefits Section -->
    <section class="mb-16">
      <h2 class="text-2xl font-semibold mb-6 text-center">Чому обирають нас</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="p-6 bg-gray-50 rounded-xl shadow-sm text-center">
          <div class="text-primary text-4xl mb-4"><i class="fas fa-shipping-fast"></i></div>
          <h3 class="text-lg font-medium mb-2">Швидка доставка</h3>
          <p class="text-gray-600">Доставка по всій країні протягом 1-3 робочих днів</p>
        </div>
        <div class="p-6 bg-gray-50 rounded-xl shadow-sm text-center">
          <div class="text-primary text-4xl mb-4"><i class="fas fa-check-circle"></i></div>
          <h3 class="text-lg font-medium mb-2">Гарантія якості</h3>
          <p class="text-gray-600">Всі наші товари проходять ретельний контроль якості</p>
        </div>
        <div class="p-6 bg-gray-50 rounded-xl shadow-sm text-center">
          <div class="text-primary text-4xl mb-4"><i class="fas fa-tags"></i></div>
          <h3 class="text-lg font-medium mb-2">Найкращі ціни</h3>
          <p class="text-gray-600">Ми постійно моніторимо ринок для найвигідніших пропозицій</p>
        </div>
        <div class="p-6 bg-gray-50 rounded-xl shadow-sm text-center">
          <div class="text-primary text-4xl mb-4"><i class="fas fa-headset"></i></div>
          <h3 class="text-lg font-medium mb-2">Підтримка 24/7</h3>
          <p class="text-gray-600">Наша команда завжди готова відповісти на ваші запитання</p>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRuntimeConfig } from '#app';

interface Product {
  id: number;
  name: string;
  slug: string;
  description: string;
  price: number;
  image: string;
}

interface Category {
  id: number;
  name: string;
  slug: string;
  image: string;
}

const featuredProducts = ref<Product[]>([]);
const categories = ref<Category[]>([]);

onMounted(async () => {
  try {
    const runtimeConfig = useRuntimeConfig();
    // Fetch featured products
    const productsResponse = await fetch(`${runtimeConfig.public.apiBaseUrl}/products?featured=1&limit=4`);
    if (productsResponse.ok) {
      featuredProducts.value = await productsResponse.json();
    }
    
    // Fetch categories
    const categoriesResponse = await fetch(`${runtimeConfig.public.apiBaseUrl}/categories?active=1`);
    if (categoriesResponse.ok) {
      categories.value = await categoriesResponse.json();
    }
  } catch (error) {
    console.error('Error fetching data:', error);
  }
});
</script>

<style scoped>
.text-primary {
  @apply text-amber-600;
}
</style> 
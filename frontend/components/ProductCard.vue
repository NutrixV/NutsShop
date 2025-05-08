<template>
  <div class="group bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
    <!-- Зображення товару -->
    <div class="relative">
      <div class="aspect-w-4 aspect-h-3 overflow-hidden">
        <img 
          v-if="product.image" 
          :src="product.image" 
          :alt="product.name" 
          class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
        />
        <img 
          v-else 
          src="/images/placeholder-product.jpg" 
          :alt="product.name" 
          class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
        />
      </div>
      
      <!-- Знижка (якщо є) -->
      <div v-if="product.discount_percent" 
           class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
        -{{ product.discount_percent }}%
      </div>
    </div>
    
    <!-- Інформація про товар -->
    <div class="p-4">
      <h3 class="text-gray-900 font-medium text-lg leading-tight mb-1 group-hover:text-amber-600 transition-colors duration-300">
        {{ product.name }}
      </h3>
      
      <p v-if="product.description" class="text-gray-500 text-sm mb-3 line-clamp-2">
        {{ product.description }}
      </p>
      
      <!-- Ціни -->
      <div class="flex items-center space-x-2">
        <span v-if="product.old_price" class="text-gray-400 text-sm line-through">
          {{ formatPrice(product.old_price) }} грн
        </span>
        <span class="text-gray-900 font-bold">
          {{ formatPrice(product.price) }} грн
        </span>
      </div>
      
      <!-- Кнопки -->
      <div class="mt-4 flex items-center justify-between">
        <NuxtLink 
          :to="`/product/${product.slug}`" 
          class="text-sm text-amber-600 hover:text-amber-800 font-medium hover:underline"
        >
          Детальніше
        </NuxtLink>
        
        <button 
          @click="addToCart" 
          class="bg-amber-600 hover:bg-amber-700 text-white rounded-full p-2 transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-opacity-50"
          :disabled="loading"
        >
          <svg 
            xmlns="http://www.w3.org/2000/svg" 
            class="h-5 w-5" 
            fill="none" 
            viewBox="0 0 24 24" 
            stroke="currentColor"
          >
            <path 
              stroke-linecap="round" 
              stroke-linejoin="round" 
              stroke-width="2" 
              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" 
            />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

interface Product {
  id: number;
  name: string;
  slug: string;
  description?: string;
  price: number;
  old_price?: number;
  discount_percent?: number;
  image?: string;
}

const props = defineProps<{
  product: Product;
}>();

const loading = ref(false);

// Форматування ціни
const formatPrice = (price: number): string => {
  return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
};

// Додавання товару до кошика
const addToCart = async () => {
  loading.value = true;
  try {
    // Тут буде код для додавання товару до кошика через API 
    // або за допомогою локального сховища
    console.log('Додано до кошика:', props.product.id);
    
    // Імітація запиту
    await new Promise(resolve => setTimeout(resolve, 500));
    
    // Тут можна викликати глобальну подію або мутацію стану
  } catch (error) {
    console.error('Помилка при додаванні до кошика:', error);
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.aspect-w-4 {
  position: relative;
  padding-bottom: 75%; /* 4:3 Aspect Ratio */
}
.aspect-h-3 {
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 100%;
}
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style> 
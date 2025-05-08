<template>
  <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300">
    <NuxtLink :to="`/product/${product.slug}`">
      <div class="relative h-48 overflow-hidden">
        <ImageProxy 
          v-if="product.image"
          :path="product.image"
          :alt="product.name"
          imgClass="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
          fallback="/images/placeholder-product.jpg"
        />
        <img 
          v-else
          src="/images/placeholder-product.jpg" 
          :alt="product.name"
          class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
        />
        <div v-if="product.is_featured" class="absolute top-2 left-2 bg-amber-500 text-white text-xs px-2 py-1 rounded">
          Популярне
        </div>
      </div>
      
      <div class="p-4">
        <h3 class="text-lg font-semibold line-clamp-1">{{ product.name }}</h3>
        <p class="text-gray-600 text-sm line-clamp-2 mt-1 mb-2">{{ product.description }}</p>
        
        <div class="flex justify-between items-center">
          <span class="text-lg font-bold text-amber-600">{{ formatPrice(product.price) }}</span>
          <button 
            class="bg-amber-600 hover:bg-amber-700 text-white py-1 px-3 rounded-md transition-colors duration-200"
            @click.prevent="addToCart"
          >
            В кошик
          </button>
        </div>
      </div>
    </NuxtLink>
  </div>
</template>

<script setup lang="ts">
import ImageProxy from '../src/components/ImageProxy.vue';

interface Product {
  id: number;
  name: string;
  slug: string;
  description: string;
  price: number;
  image: string;
  is_featured?: boolean;
}

const props = defineProps<{
  product: Product;
}>();

// Format price with currency
const formatPrice = (price: number): string => {
  return `${price.toFixed(2)} грн`;
};

// Add to cart function
const addToCart = () => {
  // Here we would dispatch to store or call API
  console.log('Adding to cart:', props.product.id);
  // Example of how to implement with Pinia store:
  // cartStore.addToCart({ 
  //   productId: props.product.id, 
  //   quantity: 1 
  // });
};
</script> 
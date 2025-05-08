<template>
  <header class="bg-white shadow-md">
    <!-- Мобільне меню -->
    <div class="container mx-auto px-4 py-3 md:py-0">
      <div class="md:hidden flex justify-between items-center">
        <div>
          <button @click="mobileMenuOpen = !mobileMenuOpen" class="focus:outline-none">
            <span class="sr-only">Відкрити меню</span>
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path v-if="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
        </div>
        <NuxtLink to="/" class="flex items-center">
          <img src="/images/logo.svg" alt="NutsShop" class="h-8">
          <span class="ml-2 text-xl font-bold text-amber-600">NutsShop</span>
        </NuxtLink>
        <div class="flex items-center">
          <NuxtLink to="/cart" class="p-2 relative">
            <span class="sr-only">Кошик</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span v-if="cartItemsCount > 0" class="absolute top-0 right-0 bg-amber-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
              {{ cartItemsCount }}
            </span>
          </NuxtLink>
        </div>
      </div>

      <!-- Мобільне меню випадаюче -->
      <div v-show="mobileMenuOpen" class="md:hidden pt-2 pb-4">
        <div class="space-y-1">
          <NuxtLink to="/" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-amber-50 hover:text-amber-600">Головна</NuxtLink>
          <NuxtLink to="/catalog" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-amber-50 hover:text-amber-600">Каталог</NuxtLink>
          <NuxtLink to="/about" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-amber-50 hover:text-amber-600">Про нас</NuxtLink>
          <NuxtLink to="/delivery" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-amber-50 hover:text-amber-600">Доставка</NuxtLink>
          <NuxtLink to="/contacts" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-amber-50 hover:text-amber-600">Контакти</NuxtLink>
        </div>
        <div class="mt-4">
          <div class="flex items-center px-3">
            <input 
              type="text" 
              placeholder="Пошук товарів..."
              class="w-full px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
              v-model="searchQuery"
              @keyup.enter="search"
            >
            <button 
              class="bg-amber-600 text-white px-4 py-2 rounded-r-md hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2"
              @click="search"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </button>
          </div>
        </div>
        <div class="mt-4 border-t border-gray-200 pt-4 flex justify-center space-x-4">
          <NuxtLink to="/auth/login" class="text-amber-600 hover:text-amber-800">Вхід</NuxtLink>
          <span class="text-gray-300">|</span>
          <NuxtLink to="/auth/register" class="text-amber-600 hover:text-amber-800">Реєстрація</NuxtLink>
        </div>
      </div>
    </div>

    <!-- Десктопне меню -->
    <div class="hidden md:block">
      <div class="container mx-auto px-4">
        <!-- Top bar -->
        <div class="flex justify-between items-center py-3 border-b">
          <div class="flex items-center">
            <NuxtLink to="/" class="flex items-center">
              <img src="/images/logo.svg" alt="NutsShop" class="h-10">
              <span class="ml-2 text-2xl font-bold text-amber-600">NutsShop</span>
            </NuxtLink>
          </div>
          
          <div class="flex items-center space-x-6">
            <div class="hidden lg:flex items-center space-x-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
              </svg>
              <span class="text-sm font-medium">0 800 123 456</span>
            </div>
            <div class="flex items-center space-x-4">
              <NuxtLink to="/auth/login" class="text-gray-700 hover:text-amber-600">Вхід</NuxtLink>
              <span class="text-gray-300">|</span>
              <NuxtLink to="/auth/register" class="text-gray-700 hover:text-amber-600">Реєстрація</NuxtLink>
            </div>
            <NuxtLink to="/cart" class="relative p-2">
              <span class="sr-only">Кошик</span>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              <span v-if="cartItemsCount > 0" class="absolute top-0 right-0 bg-amber-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
                {{ cartItemsCount }}
              </span>
            </NuxtLink>
          </div>
        </div>
        
        <!-- Nav + Search -->
        <div class="flex items-center justify-between py-4">
          <nav>
            <ul class="flex space-x-8">
              <li>
                <NuxtLink to="/" class="text-gray-700 hover:text-amber-600 font-medium">Головна</NuxtLink>
              </li>
              <li>
                <NuxtLink to="/catalog" class="text-gray-700 hover:text-amber-600 font-medium">Каталог</NuxtLink>
              </li>
              <li>
                <NuxtLink to="/about" class="text-gray-700 hover:text-amber-600 font-medium">Про нас</NuxtLink>
              </li>
              <li>
                <NuxtLink to="/delivery" class="text-gray-700 hover:text-amber-600 font-medium">Доставка</NuxtLink>
              </li>
              <li>
                <NuxtLink to="/contacts" class="text-gray-700 hover:text-amber-600 font-medium">Контакти</NuxtLink>
              </li>
            </ul>
          </nav>
          
          <div class="flex items-center">
            <div class="w-64">
              <div class="relative">
                <input 
                  type="text" 
                  placeholder="Пошук товарів..."
                  class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                  v-model="searchQuery"
                  @keyup.enter="search"
                >
                <button 
                  class="absolute inset-y-0 right-0 px-3 flex items-center"
                  @click="search"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 hover:text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const mobileMenuOpen = ref(false);
const searchQuery = ref('');
const cartItemsCount = ref(0); // Тут має бути логіка отримання кількості товарів

// Функція пошуку
const search = () => {
  if (searchQuery.value.trim()) {
    router.push({ path: '/catalog', query: { search: searchQuery.value.trim() } });
    mobileMenuOpen.value = false;
  }
};
</script> 
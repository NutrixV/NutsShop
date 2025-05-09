<template>
  <footer class="bg-gray-900 text-white pt-12 pb-8">
    <div class="container mx-auto px-4">
      <!-- Головний контент футера -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
        <!-- Колонка з інформацією про магазин -->
        <div>
          <div class="flex items-center mb-4">
            <img src="/images/logo.svg" alt="NutsShop" class="h-20">
            <span class="ml-2 text-xl font-bold text-amber-500">NutsShop</span>
          </div>
          <p class="text-gray-400 mb-4">
            Магазин найякісніших горіхів, сухофруктів і корисних солодощів для здорового харчування.
          </p>
          <div class="flex space-x-4 mt-4">
            <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-amber-500">
              <span class="sr-only">Facebook</span>
              <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
              </svg>
            </a>
            <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-amber-500">
              <span class="sr-only">Instagram</span>
              <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
              </svg>
            </a>
            <a href="https://telegram.org" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-amber-500">
              <span class="sr-only">Telegram</span>
              <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.96 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.064-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
              </svg>
            </a>
          </div>
        </div>

        <!-- Колонка з навігацією -->
        <div>
          <h3 class="text-lg font-semibold mb-4">Навігація</h3>
          <ul class="space-y-2">
            <li><NuxtLink to="/" class="text-gray-400 hover:text-amber-500">Головна</NuxtLink></li>
            <li><NuxtLink to="/catalog" class="text-gray-400 hover:text-amber-500">Каталог</NuxtLink></li>
            <li><NuxtLink to="/about" class="text-gray-400 hover:text-amber-500">Про нас</NuxtLink></li>
            <li><NuxtLink to="/delivery" class="text-gray-400 hover:text-amber-500">Доставка</NuxtLink></li>
            <li><NuxtLink to="/contacts" class="text-gray-400 hover:text-amber-500">Контакти</NuxtLink></li>
          </ul>
        </div>

        <!-- Колонка з категоріями товарів -->
        <div>
          <h3 class="text-lg font-semibold mb-4">Категорії</h3>
          <ul class="space-y-2">
            <li v-if="isLoading" class="text-gray-400">Завантаження...</li>
            <li v-for="category in categories" :key="category.category_id">
              <NuxtLink :to="`/catalog?category_id=${category.category_id}`" class="text-gray-400 hover:text-amber-500">
                {{ category.name }}
              </NuxtLink>
            </li>
          </ul>
        </div>

        <!-- Колонка з контактами -->
        <div>
          <h3 class="text-lg font-semibold mb-4">Контакти</h3>
          <ul class="space-y-3">
            <li class="flex items-start">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500 mr-3 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <span class="text-gray-400">вул. Горіхова, 123<br>м. Київ, 01001</span>
            </li>
            <li class="flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
              </svg>
              <span class="text-gray-400">0 800 123 456</span>
            </li>
            <li class="flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              <a href="mailto:info@nutsshop.com" class="text-gray-400 hover:text-amber-500">info@nutsshop.com</a>
            </li>
          </ul>
        </div>
      </div>

      <!-- Розділювач -->
      <div class="border-t border-gray-800 my-8"></div>

      <!-- Копірайт -->
      <div class="flex flex-col md:flex-row justify-between items-center">
        <p class="text-gray-500 text-sm mb-4 md:mb-0">
          &copy; {{ new Date().getFullYear() }} NutsShop. Всі права захищені.
        </p>
        <div class="flex space-x-4 text-sm">
          <NuxtLink to="/privacy" class="text-gray-500 hover:text-amber-500">Політика конфіденційності</NuxtLink>
          <NuxtLink to="/terms" class="text-gray-500 hover:text-amber-500">Умови використання</NuxtLink>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useConfig } from '~/composables/useConfig';

// Інтерфейс для категорії
interface Category {
  category_id: number;
  name: string;
  url_key: string;
  is_active: boolean;
  parent_id?: number;
  position: number;
  image?: string;
}

// Змінні стану
const categories = ref<Category[]>([]);
const isLoading = ref(true);

// Отримуємо базовий URL API
const { apiBaseUrl } = useConfig();

// Функція для завантаження категорій
const fetchCategories = async () => {
  try {
    isLoading.value = true;
    const response = await fetch(`${apiBaseUrl}/categories`);
    
    if (!response.ok) {
      throw new Error('Не вдалося завантажити категорії');
    }
    
    const data = await response.json();
    categories.value = data;
  } catch (error) {
    console.error('Помилка при завантаженні категорій у футері:', error);
  } finally {
    isLoading.value = false;
  }
};

// Завантажуємо категорії при створенні компонента
onMounted(() => {
  fetchCategories();
});
</script> 
<template>
  <div class="bg-gray-50 py-8 md:py-12">
    <div class="container mx-auto px-4">
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Каталог товарів</h1>
        <div class="flex items-center space-x-2">
          <span class="text-sm text-gray-500">Сортування:</span>
          <select v-model="sortOption" class="px-3 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            <option value="name">За назвою</option>
            <option value="price_asc">Ціна: від низької до високої</option>
            <option value="price_desc">Ціна: від високої до низької</option>
            <option value="created_at">Спочатку нові</option>
          </select>
        </div>
      </div>

      <!-- Мобільний фільтр (відкривається по кнопці) -->
      <div class="lg:hidden mb-6">
        <button 
          @click="showFilters = !showFilters" 
          class="w-full flex items-center justify-center space-x-2 bg-white border border-gray-300 py-3 px-4 rounded-md shadow-sm font-medium text-gray-700 hover:bg-gray-50"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2.99 2.99A1 1 0 017 18v-6.586l-4.707-4.707A1 1 0 012 6V3a1 1 0 011-1zm10 1H7v1.586l.707.707L11 8.586V16.5l1-1V8.586l3.293-3.293L16 4.586V4h-3z" clip-rule="evenodd" />
          </svg>
          <span>{{ showFilters ? 'Сховати фільтри' : 'Показати фільтри' }}</span>
        </button>
      </div>

      <div class="flex flex-col lg:flex-row gap-8">
        <!-- Фільтри (мобільний формат - відкривається по кнопці) -->
        <aside :class="[
          'bg-white p-4 rounded-lg shadow-sm w-full lg:w-64 shrink-0 space-y-6 border',
          { 'hidden lg:block': !showFilters }
        ]">
          <!-- Категорії -->
          <div>
            <h2 class="font-semibold text-lg mb-3 text-gray-900">Категорії</h2>
            <div v-if="loading" class="flex justify-center py-4">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-amber-600"></div>
            </div>
            <div v-else class="space-y-2">
              <div 
                v-for="category in categories" 
                :key="category.category_id" 
                class="flex items-center"
              >
                <input 
                  type="checkbox" 
                  :id="`category-${category.category_id}`" 
                  v-model="selectedCategories" 
                  :value="category.category_id"
                  class="w-4 h-4 text-amber-600 focus:ring-amber-500 rounded"
                />
                <label :for="`category-${category.category_id}`" class="ml-2 text-gray-700">
                  {{ category.name }}
                </label>
              </div>
            </div>
          </div>

          <!-- Ціновий діапазон -->
          <div>
            <h2 class="font-semibold text-lg mb-3 text-gray-900">Ціна</h2>
            <div class="space-y-4">
              <div class="flex space-x-2">
                <div class="w-1/2">
                  <input 
                    type="number" 
                    placeholder="Від" 
                    v-model="priceFrom" 
                    class="w-full px-3 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
                  />
                </div>
                <div class="w-1/2">
                  <input 
                    type="number" 
                    placeholder="До" 
                    v-model="priceTo" 
                    class="w-full px-3 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
                  />
                </div>
              </div>
              <button 
                @click="applyFilters" 
                class="w-full bg-amber-600 hover:bg-amber-700 text-white py-2 px-4 rounded-md transition-colors duration-300"
              >
                Застосувати
              </button>
            </div>
          </div>

          <!-- Акційні товари -->
          <div>
            <div class="flex items-center">
              <input 
                type="checkbox" 
                id="discount-only" 
                v-model="discountOnly" 
                class="w-4 h-4 text-amber-600 focus:ring-amber-500 rounded"
              />
              <label for="discount-only" class="ml-2 text-gray-700 font-semibold">
                Тільки зі знижкою
              </label>
            </div>
          </div>
          
          <!-- Скинути фільтри (мобільна версія) -->
          <div class="lg:hidden pt-4 border-t">
            <button 
              @click="resetFilters" 
              class="w-full py-2 px-4 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-300"
            >
              Скинути всі фільтри
            </button>
          </div>
        </aside>

        <!-- Список товарів -->
        <div class="flex-grow">
          <!-- Інформація про кількість товарів та поточні фільтри -->
          <div class="bg-white mb-6 p-4 rounded-lg shadow-sm flex flex-wrap justify-between items-center gap-4">
            <p class="text-gray-700">
              Знайдено <span class="font-semibold">{{ filteredProducts.length }}</span> товарів
            </p>
            <!-- Активні фільтри/теги -->
            <div v-if="hasActiveFilters" class="flex flex-wrap gap-2">
              <div 
                v-for="(category, index) in selectedCategoryNames" 
                :key="`cat-${index}`"
                class="bg-gray-100 px-3 py-1 rounded-full text-sm text-gray-800 flex items-center"
              >
                {{ category }}
                <button @click="removeCategory(index)" class="ml-1 text-gray-500 hover:text-gray-700">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
              <div 
                v-if="priceFrom || priceTo" 
                class="bg-gray-100 px-3 py-1 rounded-full text-sm text-gray-800 flex items-center"
              >
                Ціна: {{ priceFrom || '0' }} - {{ priceTo || '∞' }} грн
                <button @click="clearPriceFilter" class="ml-1 text-gray-500 hover:text-gray-700">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
              <div 
                v-if="discountOnly" 
                class="bg-gray-100 px-3 py-1 rounded-full text-sm text-gray-800 flex items-center"
              >
                Зі знижкою
                <button @click="discountOnly = false" class="ml-1 text-gray-500 hover:text-gray-700">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
              <!-- Скинути всі фільтри -->
              <button 
                @click="resetFilters" 
                class="text-amber-600 hover:text-amber-800 text-sm font-medium"
              >
                Скинути всі фільтри
              </button>
            </div>
          </div>

          <!-- Завантаження -->
          <div v-if="loading" class="flex justify-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-amber-600"></div>
          </div>

          <!-- Грід товарів -->
          <div v-else-if="filteredProducts.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <ProductCard 
              v-for="product in filteredProducts" 
              :key="product.entity_id" 
              :product="mapProductForComponent(product)" 
            />
          </div>

          <!-- Повідомлення, якщо нічого не знайдено -->
          <div v-else class="bg-white p-8 rounded-lg shadow-sm text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Нічого не знайдено</h3>
            <p class="text-gray-600 mb-4">Спробуйте змінити параметри фільтрації або оберіть іншу категорію</p>
            <button 
              @click="resetFilters" 
              class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-md transition-colors duration-300"
            >
              Скинути всі фільтри
            </button>
          </div>

          <!-- Пагінація -->
          <div v-if="!loading && pagination && pagination.last_page > 1" class="mt-8 flex justify-center">
            <div class="flex space-x-1">
              <button 
                v-for="page in pagination.last_page" 
                :key="page" 
                @click="goToPage(page)"
                :class="[
                  'px-4 py-2 rounded-md', 
                  currentPage === page 
                    ? 'bg-amber-600 text-white' 
                    : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'
                ]"
              >
                {{ page }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useConfig } from '~/composables/useConfig';

// Інтерфейси
interface ApiProduct {
  entity_id: number;
  name: string;
  sku: string;
  description: string;
  short_description?: string;
  price: number;
  is_in_stock: boolean;
  status: number;
  image?: string;
  gallery?: string[];
  categories: ApiCategory[];
}

interface ProductForComponent {
  id: number;
  name: string;
  slug: string;
  description?: string;
  price: number;
  old_price?: number;
  discount_percent?: number;
  image?: string;
}

interface ApiCategory {
  category_id: number;
  parent_id?: number;
  name: string;
  url_key: string;
  is_active: boolean;
  position: number;
  image?: string;
}

interface ApiPagination {
  current_page: number;
  from: number;
  last_page: number;
  per_page: number;
  to: number;
  total: number;
}

interface ApiResponse {
  data: ApiProduct[];
  links: Record<string, string>;
  meta: {
    current_page: number;
    from: number;
    last_page: number;
    path: string;
    per_page: number;
    to: number;
    total: number;
  };
}

// Стан компонента
const route = useRoute();
const router = useRouter();
const showFilters = ref(false);
const products = ref<ApiProduct[]>([]);
const categories = ref<ApiCategory[]>([]);
const selectedCategories = ref<number[]>([]);
const priceFrom = ref<number | null>(null);
const priceTo = ref<number | null>(null);
const discountOnly = ref(false);
const sortOption = ref('name');
const loading = ref(false);
const currentPage = ref(1);
const pagination = ref<ApiPagination | null>(null);

// Рахуємо, чи є активні фільтри
const hasActiveFilters = computed(() => {
  return selectedCategories.value.length > 0 || priceFrom.value !== null || priceTo.value !== null || discountOnly.value;
});

// Отримуємо назви вибраних категорій
const selectedCategoryNames = computed(() => {
  return selectedCategories.value.map(id => 
    categories.value.find(cat => cat.category_id === id)?.name || ''
  ).filter(name => name !== '');
});

// Фільтруємо і сортуємо товари локально, якщо потрібно
// В ідеалі все фільтрування і сортування робити на сервері через API
const filteredProducts = computed(() => {
  return products.value;
});

// Мапимо продукт до формату, який очікує компонент
const mapProductForComponent = (product: ApiProduct): ProductForComponent => {
  // Тут можуть бути додаткові перетворення даних, розрахунки знижок і т.д.
  return {
    id: product.entity_id,
    name: product.name,
    slug: product.sku.toLowerCase().replace(/\s+/g, '-'),
    description: product.short_description || product.description,
    price: Number(product.price),
    image: product.image && `http://localhost:8090/storage/${product.image}`,
    // Додаткові поля можуть бути додані при необхідності
  };
};

// Методи
const applyFilters = () => {
  currentPage.value = 1; // Скидаємо пагінацію при зміні фільтрів
  updateQueryParams();
  showFilters.value = false; // Закриваємо на мобільних після застосування
  fetchProducts();
};

const resetFilters = () => {
  selectedCategories.value = [];
  priceFrom.value = null;
  priceTo.value = null;
  discountOnly.value = false;
  sortOption.value = 'name';
  currentPage.value = 1;
  updateQueryParams();
  fetchProducts();
};

const removeCategory = (index: number) => {
  const categoryId = selectedCategories.value[index];
  selectedCategories.value = selectedCategories.value.filter(id => id !== categoryId);
  updateQueryParams();
  fetchProducts();
};

const clearPriceFilter = () => {
  priceFrom.value = null;
  priceTo.value = null;
  updateQueryParams();
  fetchProducts();
};

const goToPage = (page: number) => {
  currentPage.value = page;
  updateQueryParams();
  fetchProducts();
};

const updateQueryParams = () => {
  const query: Record<string, any> = {};
  
  if (selectedCategories.value.length > 0) {
    query.categories = selectedCategories.value.join(',');
  }
  
  if (priceFrom.value !== null) {
    query.price_from = priceFrom.value;
  }
  
  if (priceTo.value !== null) {
    query.price_to = priceTo.value;
  }
  
  if (discountOnly.value) {
    query.discount = 1;
  }
  
  if (sortOption.value !== 'name') {
    query.sort = sortOption.value;
  }
  
  if (currentPage.value > 1) {
    query.page = currentPage.value;
  }
  
  router.replace({ query });
};

const loadFiltersFromQuery = () => {
  const { query } = route;
  
  if (query.categories) {
    selectedCategories.value = (query.categories as string).split(',').map(Number);
  }
  
  if (query.price_from) {
    priceFrom.value = Number(query.price_from);
  }
  
  if (query.price_to) {
    priceTo.value = Number(query.price_to);
  }
  
  if (query.discount) {
    discountOnly.value = Boolean(Number(query.discount));
  }
  
  if (query.sort) {
    sortOption.value = query.sort as string;
  }
  
  if (query.page) {
    currentPage.value = Number(query.page);
  }
};

// Базовий URL API
const { apiBaseUrl } = useConfig();

// Завантаження категорій
const fetchCategories = async () => {
  try {
    const response = await fetch(`${apiBaseUrl}/categories`);
    if (!response.ok) throw new Error('Не вдалося завантажити категорії');
    
    const data = await response.json();
    categories.value = data;
  } catch (error) {
    console.error('Помилка при завантаженні категорій:', error);
  }
};

// Завантаження продуктів
const fetchProducts = async () => {
  loading.value = true;
  
  try {
    // Формуємо URL з параметрами
    let url = `${apiBaseUrl}/products?page=${currentPage.value}`;
    
    // Додаємо категорії
    if (selectedCategories.value.length > 0) {
      url += `&category_id=${selectedCategories.value[0]}`; // API приймає тільки одну категорію
    }
    
    // Додаємо сортування
    if (sortOption.value) {
      let sort = sortOption.value;
      let direction = 'asc';
      
      if (sort === 'price_desc') {
        sort = 'price';
        direction = 'desc';
      } else if (sort === 'price_asc') {
        sort = 'price';
      }
      
      url += `&sort=${sort}&direction=${direction}`;
    }
    
    // Створюємо об'єкт для додаткових параметрів
    const response = await fetch(url);
    if (!response.ok) throw new Error('Не вдалося завантажити продукти');
    
    const data: ApiResponse = await response.json();
    products.value = data.data || [];
    
    // Перевіряємо наявність meta даних перед доступом до них
    if (data.meta) {
      // Зберігаємо інформацію про пагінацію
      pagination.value = {
        current_page: data.meta.current_page,
        from: data.meta.from,
        last_page: data.meta.last_page,
        per_page: data.meta.per_page,
        to: data.meta.to,
        total: data.meta.total
      };
    } else {
      // Якщо meta даних немає, встановлюємо pagination в null
      pagination.value = null;
      console.warn('API не повернув meta дані для пагінації');
    }
  } catch (error) {
    console.error('Помилка при завантаженні продуктів:', error);
    // Встановлюємо порожній масив продуктів при помилці
    products.value = [];
    pagination.value = null;
  } finally {
    loading.value = false;
  }
};

// Слідкуємо за змінами сортування
watch([sortOption], () => {
  updateQueryParams();
  fetchProducts();
});

// Ініціалізація
onMounted(() => {
  loadFiltersFromQuery();
  fetchCategories();
  fetchProducts();
});
</script> 
<template>
  <div class="bg-gray-50 py-8 md:py-12">
    <div class="container mx-auto px-4">
      <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-8 gap-4">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Каталог товарів</h1>
        <div class="flex items-center space-x-2 self-start">
          <span class="text-sm text-gray-500 whitespace-nowrap">Сортування:</span>
          <select v-model="sortOption" class="flex-grow sm:flex-grow-0 px-3 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            <option value="name">За назвою</option>
            <option value="price_asc">Ціна: від низької</option>
            <option value="price_desc">Ціна: від високої</option>
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
          <!-- Кнопки управління фільтрами -->
          <div class="mb-4 flex flex-col space-y-2">
            <button 
              @click="applyFilters" 
              class="w-full py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-md transition-colors duration-300 flex items-center justify-center"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
              </svg>
              Застосувати фільтри
            </button>
            <button 
              v-if="hasActiveFilters"
              @click="resetFilters" 
              class="w-full py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-medium rounded-md transition-colors duration-300 flex items-center justify-center"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
              Скинути фільтри
            </button>
          </div>

          <!-- Категорії -->
          <div class="border-b pb-4">
            <button 
              @click="toggleFilter('categories')" 
              class="w-full flex justify-between items-center font-semibold text-lg mb-3 text-gray-900 text-left"
            >
              <span>Категорії</span>
              <svg 
                xmlns="http://www.w3.org/2000/svg" 
                class="h-5 w-5 transition-transform flex-shrink-0" 
                :class="{ 'transform rotate-180': expandedFilters.categories }"
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <div v-if="loading && expandedFilters.categories" class="flex justify-center py-4">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-amber-600"></div>
            </div>
            <div v-else-if="expandedFilters.categories" class="space-y-2 mt-2">
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
          <div class="border-b pb-4">
            <button 
              @click="toggleFilter('price')" 
              class="w-full flex justify-between items-center font-semibold text-lg mb-3 text-gray-900 text-left"
            >
              <span>Ціна, грн</span>
              <svg 
                xmlns="http://www.w3.org/2000/svg" 
                class="h-5 w-5 transition-transform flex-shrink-0" 
                :class="{ 'transform rotate-180': expandedFilters.price }"
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <div v-if="expandedFilters.price" class="mt-2">
              <div class="flex space-x-2 mb-4">
                <div class="flex-1">
                  <input 
                    type="number" 
                    v-model="priceFrom" 
                    min="0" 
                    :placeholder="priceMinPlaceholder" 
                    class="w-full px-3 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
                  />
                </div>
                <div class="flex-1">
                  <input 
                    type="number" 
                    v-model="priceTo" 
                    min="0" 
                    :placeholder="priceMaxPlaceholder" 
                    class="w-full px-3 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Знижки -->
          <div v-if="hasDiscountFilter" class="border-b pb-4">
            <button 
              @click="toggleFilter('discount')" 
              class="w-full flex justify-between items-center font-semibold text-lg mb-3 text-gray-900 text-left"
            >
              <span>Знижки</span>
              <svg 
                xmlns="http://www.w3.org/2000/svg" 
                class="h-5 w-5 transition-transform flex-shrink-0" 
                :class="{ 'transform rotate-180': expandedFilters.discount }"
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <div v-if="expandedFilters.discount" class="space-y-2 mt-2">
              <div class="flex items-center">
                <input 
                  type="checkbox" 
                  id="discount-filter" 
                  v-model="discountOnly" 
                  class="w-4 h-4 text-amber-600 focus:ring-amber-500 rounded"
                />
                <label for="discount-filter" class="ml-2 text-gray-700">
                  Зі знижкою
                </label>
              </div>
            </div>
          </div>

          <!-- Динамічні групи фільтрів на основі атрибутів -->
          <template v-for="(group, groupName) in groupedAttributes" :key="groupName">
            <div class="border-b pb-4" v-if="group.length > 0">
              <button 
                @click="toggleFilter(groupName)" 
                class="w-full flex justify-between items-center font-semibold text-lg mb-3 text-gray-900 text-left"
              >
                <span>{{ getGroupDisplayName(groupName) }}</span>
                <svg 
                  xmlns="http://www.w3.org/2000/svg" 
                  class="h-5 w-5 transition-transform flex-shrink-0" 
                  :class="{ 'transform rotate-180': expandedFilters[groupName] }"
                  fill="none" 
                  viewBox="0 0 24 24" 
                  stroke="currentColor"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              <div v-if="expandedFilters[groupName]" class="space-y-4 mt-2">
                <!-- Відображення кожного атрибута в групі -->
                <div v-for="attribute in group" :key="attribute.code" class="mt-4">
                  <div v-if="attribute.type === 'checkbox' && attribute.options && attribute.options.length > 0">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">{{ attribute.name }}</h4>
                    <div class="space-y-2">
                      <div 
                        v-for="option in attribute.options" 
                        :key="`${attribute.code}-${option.id}`" 
                        class="flex items-center"
                      >
                        <input 
                          type="checkbox" 
                          :id="`${attribute.code}-${option.id}`" 
                          v-model="selectedAttributes[attribute.code]" 
                          :value="option.id"
                          class="w-4 h-4 text-amber-600 focus:ring-amber-500 rounded"
                        />
                        <label :for="`${attribute.code}-${option.id}`" class="ml-2 text-gray-700 text-sm flex justify-between w-full">
                          <span>{{ option.value }}</span>
                          <span class="text-gray-500">{{ option.count }}</span>
                        </label>
                      </div>
                    </div>
                  </div>

                  <div v-else-if="attribute.type === 'range'" class="mt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">
                      {{ attribute.name }}
                      <span v-if="attribute.unit" class="text-xs text-gray-500">({{ attribute.unit }})</span>
                    </h4>
                    <div class="flex space-x-2 mb-2">
                      <div class="flex-1">
                        <input 
                          type="number" 
                          v-model="rangeAttributes[attribute.code][0]" 
                          :min="attribute.min" 
                          :max="attribute.max" 
                          placeholder="Від" 
                          class="w-full px-3 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
                        />
                      </div>
                      <div class="flex-1">
                        <input 
                          type="number" 
                          v-model="rangeAttributes[attribute.code][1]" 
                          :min="attribute.min" 
                          :max="attribute.max" 
                          placeholder="До" 
                          class="w-full px-3 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </aside>

        <!-- Список товарів -->
        <div class="flex-grow">
          <!-- Інформація про кількість товарів та поточні фільтри -->
          <div class="bg-white mb-6 p-4 rounded-lg shadow-sm flex flex-wrap justify-between items-center gap-4">
            <p class="text-gray-700">
              Знайдено <span class="font-semibold">{{ pagination?.total || 0 }}</span> товарів
            </p>
            <!-- Активні фільтри/теги -->
            <div v-if="hasActiveFilters" class="flex flex-wrap gap-2">
              <!-- Пошуковий запит -->
              <div 
                v-if="searchQuery" 
                class="bg-gray-100 px-3 py-1 rounded-full text-sm text-gray-800 flex items-center"
              >
                Пошук: {{ searchQuery }}
                <button @click="clearSearch" class="ml-1 text-gray-500 hover:text-gray-700">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
              
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
              <!-- Ціновий фільтр -->
              <div 
                v-if="isPriceFilterApplied && (priceFrom !== null || priceTo !== null)" 
                class="bg-gray-100 px-3 py-1 rounded-full text-sm text-gray-800 flex items-center"
              >
                Ціна: {{ priceFrom || 0 }} - {{ priceTo || '∞' }} грн
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
              
              <!-- Динамічні атрибути: чекбокси -->
              <template v-for="attribute in productAttributes.filter((attr: ProductAttribute) => attr.type === 'checkbox')" :key="`active-${attribute.code}`">
                <div 
                  v-for="(valueId, idx) in selectedAttributes[attribute.code] || []" 
                  :key="`attr-${attribute.code}-${idx}`"
                  class="bg-gray-100 px-3 py-1 rounded-full text-sm text-gray-800 flex items-center"
                >
                  {{ attribute.name }}: 
                  {{ getAttributeOptionLabel(attribute, valueId) }}
                  <button @click="removeAttributeFilter(attribute.code, valueId)" class="ml-1 text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </template>
              
              <!-- Динамічні атрибути: діапазони -->
              <template v-for="attribute in productAttributes.filter((attr: ProductAttribute) => attr.type === 'range')" :key="`active-range-${attribute.code}`">
                <div 
                  v-if="rangeAttributes[attribute.code] && (rangeAttributes[attribute.code][0] !== null || rangeAttributes[attribute.code][1] !== null)" 
                  class="bg-gray-100 px-3 py-1 rounded-full text-sm text-gray-800 flex items-center"
                >
                  {{ attribute.name }}: 
                  {{ rangeAttributes[attribute.code][0] || '0' }} - {{ rangeAttributes[attribute.code][1] || '∞' }} 
                  <span v-if="attribute.unit">{{ attribute.unit }}</span>
                  <button @click="clearRangeFilter(attribute.code)" class="ml-1 text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </template>
              
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
  attributes?: Record<string, any>;
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
  last_page: number;
  total: number;
  per_page: number;
  from?: number;
  to?: number;
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

interface ProductAttribute {
  code: string;
  name: string;
  type: 'checkbox' | 'range' | 'select'; // Тип фільтра
  group?: string; // Група атрибутів для організації (наприклад, "Харчова цінність")
  unit?: string; // Одиниця виміру (г, ккал, тощо)
  options: Array<{
    id: number | string;
    value: string;
    count?: number;
  }>;
  min?: number; // Для числових діапазонів
  max?: number; // Для числових діапазонів
}

interface ExpandedFilters {
  [key: string]: boolean;
  categories: boolean;
  price: boolean;
  discount: boolean;
  nutrition: boolean; // Група "Харчова цінність"
  general: boolean;   // Загальні характеристики
  storage: boolean;   // Умови зберігання
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
const isPriceFilterApplied = ref(false); // Відстежує, чи було встановлено значення ціни користувачем
const discountOnly = ref(false);
const sortOption = ref('name');
const loading = ref(false);
const currentPage = ref(1);
const pagination = ref<ApiPagination | null>(null);
const searchQuery = ref<string>(''); // Змінна для зберігання пошукового запиту
const searchInput = ref<string>(''); // Додаємо нову змінну для поля введення
const expandedFilters = ref<ExpandedFilters>({
  categories: true, // Стартуємо з відкритою категорією для кращого UX
  price: false,
  discount: false,
  nutrition: false,
  general: false,
  storage: false
});
const selectedAttributes = ref<Record<string, (number | string)[]>>({});
const rangeAttributes = ref<Record<string, [number | null, number | null]>>({});
const productAttributes = ref<ProductAttribute[]>([]);

// Оголошуємо змінні для плейсхолдерів цінового фільтра
const priceMinPlaceholder = ref('Від');
const priceMaxPlaceholder = ref('До');

// Чи є активні фільтри
const hasActiveFilters = computed(() => {
  // Перевіряємо, чи є обрані категорії
  if (selectedCategories.value.length > 0) return true;
  
  // Перевіряємо, чи встановлений ціновий діапазон і чи був він явно застосований
  const hasPriceFilter = isPriceFilterApplied.value && (priceFrom.value !== null || priceTo.value !== null);
  if (hasPriceFilter) return true;
  
  // Перевіряємо, чи вибрана опція "тільки зі знижкою"
  if (discountOnly.value) return true;
  
  // Перевіряємо, чи є пошуковий запит
  if (searchQuery.value) return true;
  
  // Перевіряємо, чи вибрані будь-які атрибути фільтрації
  for (const key in selectedAttributes.value) {
    if (selectedAttributes.value[key].length > 0) return true;
  }
  
  // Перевіряємо, чи встановлені будь-які значення діапазону
  for (const key in rangeAttributes.value) {
    if (rangeAttributes.value[key][0] !== null || rangeAttributes.value[key][1] !== null) return true;
  }
  
  return false;
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
  
  // Перевіряємо, чи був встановлений ціновий фільтр
  if (priceFrom.value !== null || priceTo.value !== null) {
    isPriceFilterApplied.value = true;
  }
  
  updateQueryParams();
  showFilters.value = false; // Закриваємо на мобільних після застосування
  fetchProducts();
};

const resetFilters = () => {
  selectedCategories.value = [];
  priceFrom.value = null;
  priceTo.value = null;
  isPriceFilterApplied.value = false; // Скидаємо прапорець застосування цінового фільтра
  discountOnly.value = false;
  
  // Очищаємо всі атрибути
  for (const key in selectedAttributes.value) {
    selectedAttributes.value[key] = [];
  }
  
  // Очищаємо всі діапазони
  for (const key in rangeAttributes.value) {
    rangeAttributes.value[key] = [null, null];
  }
  
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
  isPriceFilterApplied.value = false; // Скидаємо прапорець застосування цінового фільтра
  updateQueryParams();
  fetchProducts();
};

// Видалення фільтра атрибуту
const removeAttributeFilter = (code: string, valueId: number | string) => {
  if (selectedAttributes.value[code]) {
    selectedAttributes.value[code] = selectedAttributes.value[code].filter(id => id !== valueId);
    updateQueryParams();
    fetchProducts();
  }
};

const goToPage = (page: number) => {
  currentPage.value = page;
  updateQueryParams();
  fetchProducts();
};

const updateQueryParams = () => {
  // Використовуємо URLSearchParams для формування URL
  const searchParams = new URLSearchParams();
  
  // Категорії
  selectedCategories.value.forEach(categoryId => {
    searchParams.append("category_id[]", categoryId.toString());
  });
  
  // Ціновий діапазон
  if (isPriceFilterApplied.value) {
    if (priceFrom.value !== null) {
      searchParams.append("price_from", priceFrom.value.toString());
    }
    
    if (priceTo.value !== null) {
      searchParams.append("price_to", priceTo.value.toString());
    }
  }
  
  // Опція "тільки зі знижкою"
  if (discountOnly.value) {
    searchParams.append("discount", "1");
  }
  
  // Сортування
  if (sortOption.value !== 'name') {
    searchParams.append("sort", sortOption.value);
  }
  
  // Поточна сторінка
  if (currentPage.value > 1) {
    searchParams.append("page", currentPage.value.toString());
  }
  
  // Пошуковий запит
  if (searchQuery.value) {
    searchParams.append("search", searchQuery.value);
  }
  
  // Атрибути
  Object.entries(selectedAttributes.value).forEach(([code, values]) => {
    if (values.length > 0) {
      values.forEach(value => {
        searchParams.append(`attr_${code}[]`, value.toString());
      });
    }
  });
  
  // Діапазони атрибутів
  Object.entries(rangeAttributes.value).forEach(([code, [min, max]]) => {
    if (min !== null) {
      searchParams.append(`${code}_from`, min.toString());
    }
    
    if (max !== null) {
      searchParams.append(`${code}_to`, max.toString());
    }
  });
  
  // Оновлюємо URL без перезавантаження сторінки, використовуючи query-string
  const queryString = searchParams.toString();
  const path = router.currentRoute.value.path;
  
  // Використовуємо replaceState для збереження правильного формату URL
  if (queryString) {
    window.history.replaceState({}, '', `${path}?${queryString}`);
  } else {
    window.history.replaceState({}, '', path);
  }
};

const loadFiltersFromQuery = () => {
  // Функція для отримання параметрів з URLSearchParams
  const searchParams = new URLSearchParams(window.location.search);
  
  // Очищаємо поточні фільтри
  selectedCategories.value = [];
  priceFrom.value = null;
  priceTo.value = null;
  isPriceFilterApplied.value = false;
  discountOnly.value = false;
  currentPage.value = 1;
  
  // Очищаємо всі атрибути
  for (const key in selectedAttributes.value) {
    selectedAttributes.value[key] = [];
  }
  
  // Очищаємо всі діапазони
  for (const key in rangeAttributes.value) {
    rangeAttributes.value[key] = [null, null];
  }
  
  // Завантажуємо категорії - обробляємо два формати: 'category_id[]' та 'category_id'
  const categoryIds: number[] = [];
  
  // Формат 'category_id[]' (масив)
  searchParams.getAll('category_id[]').forEach(id => {
    categoryIds.push(parseInt(id));
  });
  
  // Формат 'category_id' (одиночне значення)
  const singleCategoryId = searchParams.get('category_id');
  if (singleCategoryId) {
    categoryIds.push(parseInt(singleCategoryId));
  }
  
  if (categoryIds.length > 0) {
    selectedCategories.value = categoryIds;
  }
  
  // Завантажуємо ціновий діапазон
  const priceFromParam = searchParams.get('price_from');
  if (priceFromParam) {
    priceFrom.value = parseInt(priceFromParam);
    isPriceFilterApplied.value = true;
  }
  
  const priceToParam = searchParams.get('price_to');
  if (priceToParam) {
    priceTo.value = parseInt(priceToParam);
    isPriceFilterApplied.value = true;
  }
  
  // Завантажуємо параметр "тільки зі знижкою"
  if (searchParams.get('discount') === '1') {
    discountOnly.value = true;
  }
  
  // Завантажуємо сортування
  const sortParam = searchParams.get('sort');
  if (sortParam) {
    sortOption.value = sortParam;
  }
  
  // Завантажуємо поточну сторінку
  const pageParam = searchParams.get('page');
  if (pageParam) {
    currentPage.value = parseInt(pageParam);
  }
  
  // Завантажуємо пошуковий запит
  const search = searchParams.get('search');
  if (search) {
    searchQuery.value = search;
  } else {
    searchQuery.value = '';
  }
  
  // Створюємо мапу атрибутів з URL
  const attrMap = new Map();
  searchParams.forEach((value, key) => {
    // Обробка атрибутів у форматі attr_name[]
    if (key.startsWith('attr_') && key.endsWith('[]')) {
      const attrCode = key.slice(5, -2); // Видаляємо 'attr_' та '[]'
      if (!attrMap.has(attrCode)) {
        attrMap.set(attrCode, []);
      }
      attrMap.get(attrCode).push(value);
    }
    
    // Обробка діапазонів
    const fromMatch = key.match(/(.+)_from$/);
    if (fromMatch && fromMatch[1] !== 'price') {
      const attrCode = fromMatch[1];
      if (!rangeAttributes.value[attrCode]) {
        rangeAttributes.value[attrCode] = [null, null];
      }
      rangeAttributes.value[attrCode][0] = parseInt(value);
    }
    
    const toMatch = key.match(/(.+)_to$/);
    if (toMatch && toMatch[1] !== 'price') {
      const attrCode = toMatch[1];
      if (!rangeAttributes.value[attrCode]) {
        rangeAttributes.value[attrCode] = [null, null];
      }
      rangeAttributes.value[attrCode][1] = parseInt(value);
    }
  });
  
  // Оновлюємо вибрані атрибути
  attrMap.forEach((values, attrCode) => {
    selectedAttributes.value[attrCode] = values;
  });
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
    let url = new URL(`${apiBaseUrl}/products`);
    let params = new URLSearchParams();
    
    // Додаємо поточну сторінку
    params.append("page", currentPage.value.toString());
    
    // Додаємо категорії (можуть бути множинні)
    if (selectedCategories.value.length > 0) {
      selectedCategories.value.forEach(categoryId => {
        params.append("category_id[]", categoryId.toString());
      });
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
      
      params.append("sort", sort);
      params.append("direction", direction);
    }
    
    // Додаємо ціновий діапазон
    if (priceFrom.value !== null) {
      params.append("price_from", priceFrom.value.toString());
    }
    
    if (priceTo.value !== null) {
      params.append("price_to", priceTo.value.toString());
    }
    
    // Додаємо фільтр по знижках
    if (discountOnly.value) {
      params.append("discount", "1");
    }
    
    // Додаємо атрибути
    Object.entries(selectedAttributes.value).forEach(([code, values]) => {
      if (values.length > 0) {
        values.forEach(value => {
          params.append(`attr_${code}[]`, value.toString());
        });
      }
    });
    
    // Додаємо діапазони атрибутів
    Object.entries(rangeAttributes.value).forEach(([code, [min, max]]) => {
      if (min !== null) {
        params.append(`${code}_from`, min.toString());
      }
      if (max !== null) {
        params.append(`${code}_to`, max.toString());
      }
    });
    
    // Додаємо пошуковий запит, якщо є
    if (searchQuery.value) {
      params.append('search', searchQuery.value);
    }
    
    // Створюємо повний URL з параметрами
    url.search = params.toString();
    
    // Виконуємо запит
    const response = await fetch(url.toString());
    if (!response.ok) throw new Error('Не вдалося завантажити продукти');
    
    const data = await response.json();
    
    // Оновлюємо стан компонента
    products.value = data.data || [];
    
    // Laravel повертає інший формат пагінації ніж той, на який розраховує фронтенд
    pagination.value = {
      current_page: data.current_page || 1,
      last_page: data.last_page || 1,
      total: data.total || products.value.length,
      per_page: data.per_page || 20
    };
    
    // Також завантажуємо динамічні фільтри на основі поточного вибору
    fetchFilters();
    
  } catch (error) {
    console.error('Помилка при завантаженні продуктів:', error);
    products.value = [];
  } finally {
    loading.value = false;
  }
};

// Отримати фільтри з API
const fetchFilters = async () => {
  try {
    // Формуємо URL з параметрами
    let url = new URL(`${apiBaseUrl}/product-filters`);
    let params = new URLSearchParams();
    
    // Додаємо категорії (можуть бути множинні)
    if (selectedCategories.value.length > 0) {
      selectedCategories.value.forEach(categoryId => {
        params.append("category_id[]", categoryId.toString());
      });
    }
    
    // Додаємо ціновий діапазон
    if (priceFrom.value !== null) {
      params.append("price_from", priceFrom.value.toString());
    }
    
    if (priceTo.value !== null) {
      params.append("price_to", priceTo.value.toString());
    }
    
    // Додаємо фільтр по знижках
    if (discountOnly.value) {
      params.append("discount", "1");
    }
    
    // Додаємо атрибути
    Object.entries(selectedAttributes.value).forEach(([code, values]) => {
      if (values.length > 0) {
        values.forEach(value => {
          params.append(`attr_${code}[]`, value.toString());
        });
      }
    });
    
    // Додаємо діапазони атрибутів
    Object.entries(rangeAttributes.value).forEach(([code, [min, max]]) => {
      if (min !== null) {
        params.append(`${code}_from`, min.toString());
      }
      if (max !== null) {
        params.append(`${code}_to`, max.toString());
      }
    });
    
    // Додаємо пошуковий запит, якщо є
    if (searchQuery.value) {
      params.append('search', searchQuery.value);
    }
    
    // Створюємо повний URL з параметрами
    url.search = params.toString();
    
    const response = await fetch(url.toString());
    if (!response.ok) throw new Error('Не вдалося завантажити фільтри');
    
    const data = await response.json();
    
    // Оновлюємо доступні фільтри
    productAttributes.value = data;
    
    // Оновлюємо тільки плейсхолдери для цінового діапазону, не заповнюючи поля
    const priceFilter = data.find((filter: ProductAttribute) => filter.code === 'price');
    if (priceFilter) {
      priceMinPlaceholder.value = `Від ${priceFilter.min}`;
      priceMaxPlaceholder.value = `До ${priceFilter.max}`;
    }
  } catch (error) {
    console.error('Помилка при завантаженні фільтрів:', error);
  }
};

// Слідкуємо за змінами сортування
watch([sortOption], () => {
  updateQueryParams();
  fetchProducts();
});

// Слідкуємо за змінами URL-параметрів (для пошуку через хедер)
watch(() => route.query, (newQuery: Record<string, any>, oldQuery: Record<string, any>) => {
  // Якщо URL змінився
  if (JSON.stringify(newQuery) !== JSON.stringify(oldQuery)) {
    // Перезавантажуємо фільтри та параметри з URL
    loadFiltersFromQuery();
    
    // Якщо змінився пошуковий запит в URL
    const searchParam = newQuery?.search as string | undefined;
    if (searchParam !== undefined) {
      searchQuery.value = searchParam;
      searchInput.value = searchQuery.value; // Оновлюємо також поле введення
    } else {
      // Якщо параметр search відсутній, очищаємо пошуковий запит
      searchQuery.value = '';
      searchInput.value = '';
    }
    
    // Оновлюємо сторінку і завантажуємо продукти
    fetchProducts();
  }
}, { deep: true });

// Ініціалізація
onMounted(() => {
  loadFiltersFromQuery();
  
  // Встановлюємо значення searchInput при завантаженні сторінки на основі URL
  searchInput.value = searchQuery.value;
  
  fetchCategories();
  fetchProducts();
  
  // Слідкуємо за змінами сортування
  watch([sortOption], () => {
    updateQueryParams();
    fetchProducts();
  });

  // Відкриваємо першу категорію фільтрів при завантаженні для кращого UX
  expandedFilters.value.categories = true;
});

const toggleFilter = (code: string) => {
  if (code in expandedFilters.value) {
    expandedFilters.value[code] = !expandedFilters.value[code];
  }
};

// Отримуємо унікальні атрибути з товарів
const extractProductAttributes = () => {
  const attributes: Record<string, ProductAttribute> = {};
  
  // Визначаємо групи для атрибутів
  const attributeGroups: Record<string, string> = {
    // Харчова цінність
    calories: 'nutrition',
    calories_100g: 'nutrition',
    protein: 'nutrition',
    proteins: 'nutrition',
    protein_100g: 'nutrition',
    fat: 'nutrition',
    fats: 'nutrition',
    fat_100g: 'nutrition',
    carbs: 'nutrition',
    carbohydrates: 'nutrition',
    carbs_100g: 'nutrition',
    
    // Загальні характеристики
    weight: 'general',
    country: 'general',
    country_of_origin: 'general',
    processing_type: 'general',
    
    // Зберігання
    shelf_life: 'storage',
    storage_conditions: 'storage',
    
    // Додайте інші маппінги атрибутів за необхідності
  };
  
  // Визначаємо одиниці виміру для атрибутів
  const attributeUnits: Record<string, string> = {
    weight: 'г',
    calories: 'ккал',
    calories_100g: 'ккал/100г',
    protein: 'г',
    proteins: 'г',
    protein_100g: 'г/100г',
    fat: 'г',
    fats: 'г',
    fat_100g: 'г/100г',
    carbs: 'г',
    carbohydrates: 'г',
    carbs_100g: 'г/100г',
    shelf_life: 'місяців',
  };
  
  // Визначаємо атрибути, які повинні бути числовими діапазонами
  const rangeAttributesList = [
    'weight', 'calories', 'calories_100g', 'protein', 'proteins', 
    'protein_100g', 'fat', 'fats', 'fat_100g', 'carbs', 'carbohydrates', 
    'carbs_100g', 'shelf_life'
  ];
  
  // Локалізовані назви для атрибутів
  const attributeNames: Record<string, string> = {
    weight: 'Вага',
    country: 'Країна походження',
    country_of_origin: 'Країна походження',
    processing_type: 'Тип обробки',
    calories: 'Калорійність',
    calories_100g: 'Калорійність',
    protein: 'Білки',
    proteins: 'Білки',
    protein_100g: 'Білки',
    fat: 'Жири',
    fats: 'Жири',
    fat_100g: 'Жири',
    carbs: 'Вуглеводи',
    carbohydrates: 'Вуглеводи',
    carbs_100g: 'Вуглеводи',
    shelf_life: 'Термін придатності',
    storage_conditions: 'Умови зберігання',
  };
  
  // Збираємо всі атрибути з продуктів
  products.value.forEach(product => {
    if (product.attributes) {
      Object.entries(product.attributes).forEach(([code, value]) => {
        // Якщо атрибут ще не зустрічався, створюємо його
        if (!attributes[code]) {
          // Визначаємо тип атрибуту
          let attrType: 'checkbox' | 'range' | 'select' = 'checkbox';
          if (rangeAttributesList.includes(code)) {
            attrType = 'range';
          }
          
          // Визначаємо локалізовану назву або форматуємо наявну
          const displayName = attributeNames[code] || code.charAt(0).toUpperCase() + code.slice(1).replace(/_/g, ' ');
          
          attributes[code] = {
            code,
            name: displayName,
            type: attrType,
            group: attributeGroups[code] || 'general', // За замовчуванням до загальних
            unit: attributeUnits[code] || '',
            options: [],
            min: attrType === 'range' ? Number.MAX_VALUE : undefined,
            max: attrType === 'range' ? Number.MIN_VALUE : undefined
          };
        }
        
        const attribute = attributes[code];
        
        // Обробка числових значень для діапазонів
        if (attribute.type === 'range' && typeof value === 'number') {
          // Оновлюємо min і max
          if (value < (attribute.min || Number.MAX_VALUE)) {
            attribute.min = value;
          }
          if (value > (attribute.max || Number.MIN_VALUE)) {
            attribute.max = value;
          }
        }
        
        // Додаємо опцію, якщо вона ще не зустрічалася
        // Для діапазонів не додаємо кожне значення як опцію
        if (attribute.type !== 'range') {
          const option = { id: value, value: String(value) };
          if (!attribute.options.some(opt => String(opt.id) === String(option.id))) {
            attribute.options.push(option);
          }
        }
      });
    }
  });
  
  // Сортуємо опції для кожного атрибута
  Object.values(attributes).forEach(attribute => {
    if (attribute.type !== 'range') {
      // Для нечислових атрибутів сортуємо за алфавітом
      attribute.options.sort((a, b) => String(a.value).localeCompare(String(b.value)));
    }
  });
  
  // Групуємо атрибути за категоріями і сортуємо всередині категорій
  const groupedAttributes: Record<string, ProductAttribute[]> = {
    nutrition: [],
    general: [],
    storage: []
  };
  
  Object.values(attributes).forEach(attr => {
    const group = attr.group || 'general';
    if (!groupedAttributes[group]) {
      groupedAttributes[group] = [];
    }
    groupedAttributes[group].push(attr);
  });
  
  // Сортуємо всередині груп
  Object.values(groupedAttributes).forEach(group => {
    group.sort((a, b) => a.name.localeCompare(b.name));
  });
  
  // Формуємо фінальний масив атрибутів, розділений за групами
  productAttributes.value = [
    ...groupedAttributes.general,
    ...groupedAttributes.nutrition,
    ...groupedAttributes.storage
  ];
  
  // Розгортаємо за замовчуванням загальні характеристики
  expandedFilters.value.general = true;
  
  // Оновлюємо expandedFilters для атрибутів
  productAttributes.value.forEach(attr => {
    if (!(attr.code in expandedFilters.value)) {
      expandedFilters.value[attr.code] = false;
    }
  });
  
  // Ініціалізуємо rangeAttributes для атрибутів типу range
  productAttributes.value
    .filter((attr: ProductAttribute) => attr.type === 'range')
    .forEach((attr: ProductAttribute) => {
      if (!rangeAttributes.value[attr.code]) {
        rangeAttributes.value[attr.code] = [null, null];
      }
    });
};

const getAttributeOptionLabel = (attribute: ProductAttribute, valueId: number | string): string => {
  const option = attribute.options.find(opt => opt.id === valueId);
  return option ? option.value : String(valueId);
};

// Очищення фільтра діапазону
const clearRangeFilter = (code: string) => {
  if (rangeAttributes.value[code]) {
    rangeAttributes.value[code] = [null, null];
    updateQueryParams();
    fetchProducts();
  }
};

// Групування атрибутів за групами
const groupedAttributes = computed(() => {
  const groups: Record<string, ProductAttribute[]> = {};
  
  productAttributes.value.forEach(attribute => {
    // Пропускаємо ціну і знижки, вони обробляються окремо
    if (attribute.code === 'price' || attribute.code === 'discount') {
      return;
    }
    
    // Використовуємо групу атрибута або 'other' якщо група не вказана
    const groupName = attribute.group || 'other';
    
    if (!groups[groupName]) {
      groups[groupName] = [];
    }
    
    groups[groupName].push(attribute);
    
    // Ініціалізуємо стан фільтра для групи, якщо його ще немає
    if (expandedFilters.value[groupName] === undefined) {
      expandedFilters.value[groupName] = false;
    }
    
    // Ініціалізуємо масив вибраних атрибутів, якщо його ще немає
    if (attribute.type === 'checkbox' && !selectedAttributes.value[attribute.code]) {
      selectedAttributes.value[attribute.code] = [];
    }
    
    // Ініціалізуємо діапазони атрибутів, якщо їх ще немає
    if (attribute.type === 'range' && !rangeAttributes.value[attribute.code]) {
      rangeAttributes.value[attribute.code] = [null, null];
    }
  });
  
  return groups;
});

// Перевірка наявності фільтра знижки
const hasDiscountFilter = computed(() => {
  return productAttributes.value.some(attr => attr.code === 'discount');
});

// Метод для отримання назви групи для відображення
const getGroupDisplayName = (groupName: string): string => {
  const groupDisplayNames: Record<string, string> = {
    'general': 'Загальні характеристики',
    'nutrition': 'Харчова цінність',
    'storage': 'Умови зберігання',
    'other': 'Інші характеристики'
  };
  
  return groupDisplayNames[groupName] || groupName;
};

// Метод для очищення пошукового запиту
const clearSearch = () => {
  searchQuery.value = '';
  updateQueryParams();
  fetchProducts();
};

// Додаємо обробник пошуку
const submitSearch = () => {
  searchQuery.value = searchInput.value;
  currentPage.value = 1;
  updateQueryParams();
  fetchProducts();
};
</script> 
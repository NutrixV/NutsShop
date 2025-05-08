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
          <div class="border-b pb-4">
            <button 
              @click="toggleFilter('categories')" 
              class="w-full flex justify-between items-center font-semibold text-lg mb-3 text-gray-900"
            >
              <span>Категорії</span>
              <svg 
                xmlns="http://www.w3.org/2000/svg" 
                class="h-5 w-5 transition-transform" 
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
              class="w-full flex justify-between items-center font-semibold text-lg mb-3 text-gray-900"
            >
              <span>Ціна</span>
              <svg 
                xmlns="http://www.w3.org/2000/svg" 
                class="h-5 w-5 transition-transform" 
                :class="{ 'transform rotate-180': expandedFilters.price }"
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <div v-if="expandedFilters.price" class="space-y-4 mt-2">
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
            </div>
          </div>
          
          <!-- Загальні характеристики -->
          <div class="border-b pb-4">
            <button 
              @click="toggleFilter('general')" 
              class="w-full flex justify-between items-center font-semibold text-lg mb-3 text-gray-900"
            >
              <span>Загальні характеристики</span>
              <svg 
                xmlns="http://www.w3.org/2000/svg" 
                class="h-5 w-5 transition-transform" 
                :class="{ 'transform rotate-180': expandedFilters.general }"
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <div v-if="expandedFilters.general" class="space-y-4 mt-2">
              <!-- Атрибути з групи "general" -->
              <div 
                v-for="attribute in productAttributes.filter((attr: ProductAttribute) => attr.group === 'general')" 
                :key="`general-${attribute.code}`"
                class="mb-4"
              >
                <h3 class="font-medium text-gray-700 mb-2">{{ attribute.name }}</h3>
                
                <!-- Для атрибутів типу "range" -->
                <div v-if="attribute.type === 'range'" class="space-y-3">
                  <div class="flex space-x-2">
                    <div class="w-1/2">
                      <input 
                        type="number" 
                        :placeholder="`Від ${attribute.min}`" 
                        v-model="rangeAttributes[attribute.code][0]" 
                        class="w-full px-3 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
                      />
                    </div>
                    <div class="w-1/2">
                      <input 
                        type="number" 
                        :placeholder="`До ${attribute.max}`" 
                        v-model="rangeAttributes[attribute.code][1]" 
                        class="w-full px-3 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
                      />
                    </div>
                  </div>
                  <div class="text-xs text-gray-500">
                    {{ attribute.unit ? `Діапазон: ${attribute.min} - ${attribute.max} ${attribute.unit}` : `Діапазон: ${attribute.min} - ${attribute.max}` }}
                  </div>
                </div>
                
                <!-- Для атрибутів типу "checkbox" -->
                <div v-else-if="attribute.type === 'checkbox'" class="space-y-2">
                  <div 
                    v-for="option in attribute.options" 
                    :key="`option-${attribute.code}-${option.id}`" 
                    class="flex items-center"
                  >
                    <input 
                      type="checkbox" 
                      :id="`option-${attribute.code}-${option.id}`" 
                      v-model="selectedAttributes[attribute.code]" 
                      :value="option.id"
                      class="w-4 h-4 text-amber-600 focus:ring-amber-500 rounded"
                    />
                    <label :for="`option-${attribute.code}-${option.id}`" class="ml-2 text-gray-700">
                      {{ option.value }}
                      <span v-if="option.count" class="text-gray-500 text-xs">({{ option.count }})</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Харчова цінність -->
          <div class="border-b pb-4">
            <button 
              @click="toggleFilter('nutrition')" 
              class="w-full flex justify-between items-center font-semibold text-lg mb-3 text-gray-900"
            >
              <span>Харчова цінність</span>
              <svg 
                xmlns="http://www.w3.org/2000/svg" 
                class="h-5 w-5 transition-transform" 
                :class="{ 'transform rotate-180': expandedFilters.nutrition }"
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <div v-if="expandedFilters.nutrition" class="space-y-4 mt-2">
              <!-- Атрибути з групи "nutrition" -->
              <div 
                v-for="attribute in productAttributes.filter((attr: ProductAttribute) => attr.group === 'nutrition')" 
                :key="`nutrition-${attribute.code}`"
                class="mb-4"
              >
                <h3 class="font-medium text-gray-700 mb-2">
                  {{ attribute.name }}
                  <span v-if="attribute.unit" class="text-xs text-gray-500">({{ attribute.unit }})</span>
                </h3>
                
                <!-- Для атрибутів типу "range" -->
                <div v-if="attribute.type === 'range'" class="space-y-3">
                  <div class="flex space-x-2">
                    <div class="w-1/2">
                      <input 
                        type="number" 
                        :placeholder="`Від ${attribute.min}`" 
                        v-model="rangeAttributes[attribute.code][0]" 
                        class="w-full px-3 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
                      />
                    </div>
                    <div class="w-1/2">
                      <input 
                        type="number" 
                        :placeholder="`До ${attribute.max}`" 
                        v-model="rangeAttributes[attribute.code][1]" 
                        class="w-full px-3 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
                      />
                    </div>
                  </div>
                  <div class="text-xs text-gray-500">
                    {{ attribute.unit ? `Діапазон: ${attribute.min} - ${attribute.max} ${attribute.unit}` : `Діапазон: ${attribute.min} - ${attribute.max}` }}
                  </div>
                </div>
                
                <!-- Для атрибутів типу "checkbox" -->
                <div v-else-if="attribute.type === 'checkbox'" class="space-y-2">
                  <div 
                    v-for="option in attribute.options" 
                    :key="`option-${attribute.code}-${option.id}`" 
                    class="flex items-center"
                  >
                    <input 
                      type="checkbox" 
                      :id="`option-${attribute.code}-${option.id}`" 
                      v-model="selectedAttributes[attribute.code]" 
                      :value="option.id"
                      class="w-4 h-4 text-amber-600 focus:ring-amber-500 rounded"
                    />
                    <label :for="`option-${attribute.code}-${option.id}`" class="ml-2 text-gray-700">
                      {{ option.value }}
                      <span v-if="option.count" class="text-gray-500 text-xs">({{ option.count }})</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Умови зберігання -->
          <div class="border-b pb-4">
            <button 
              @click="toggleFilter('storage')" 
              class="w-full flex justify-between items-center font-semibold text-lg mb-3 text-gray-900"
            >
              <span>Умови зберігання</span>
              <svg 
                xmlns="http://www.w3.org/2000/svg" 
                class="h-5 w-5 transition-transform" 
                :class="{ 'transform rotate-180': expandedFilters.storage }"
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <div v-if="expandedFilters.storage" class="space-y-4 mt-2">
              <!-- Атрибути з групи "storage" -->
              <div 
                v-for="attribute in productAttributes.filter((attr: ProductAttribute) => attr.group === 'storage')" 
                :key="`storage-${attribute.code}`"
                class="mb-4"
              >
                <h3 class="font-medium text-gray-700 mb-2">
                  {{ attribute.name }}
                  <span v-if="attribute.unit" class="text-xs text-gray-500">({{ attribute.unit }})</span>
                </h3>
                
                <!-- Для атрибутів типу "range" -->
                <div v-if="attribute.type === 'range'" class="space-y-3">
                  <div class="flex space-x-2">
                    <div class="w-1/2">
                      <input 
                        type="number" 
                        :placeholder="`Від ${attribute.min}`" 
                        v-model="rangeAttributes[attribute.code][0]" 
                        class="w-full px-3 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
                      />
                    </div>
                    <div class="w-1/2">
                      <input 
                        type="number" 
                        :placeholder="`До ${attribute.max}`" 
                        v-model="rangeAttributes[attribute.code][1]" 
                        class="w-full px-3 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
                      />
                    </div>
                  </div>
                  <div class="text-xs text-gray-500">
                    {{ attribute.unit ? `Діапазон: ${attribute.min} - ${attribute.max} ${attribute.unit}` : `Діапазон: ${attribute.min} - ${attribute.max}` }}
                  </div>
                </div>
                
                <!-- Для атрибутів типу "checkbox" -->
                <div v-else-if="attribute.type === 'checkbox'" class="space-y-2">
                  <div 
                    v-for="option in attribute.options" 
                    :key="`option-${attribute.code}-${option.id}`" 
                    class="flex items-center"
                  >
                    <input 
                      type="checkbox" 
                      :id="`option-${attribute.code}-${option.id}`" 
                      v-model="selectedAttributes[attribute.code]" 
                      :value="option.id"
                      class="w-4 h-4 text-amber-600 focus:ring-amber-500 rounded"
                    />
                    <label :for="`option-${attribute.code}-${option.id}`" class="ml-2 text-gray-700">
                      {{ option.value }}
                      <span v-if="option.count" class="text-gray-500 text-xs">({{ option.count }})</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Акційні товари -->
          <div class="border-b pb-4">
            <button 
              @click="toggleFilter('discount')" 
              class="w-full flex justify-between items-center font-semibold text-lg mb-3 text-gray-900"
            >
              <span>Знижки та акції</span>
              <svg 
                xmlns="http://www.w3.org/2000/svg" 
                class="h-5 w-5 transition-transform" 
                :class="{ 'transform rotate-180': expandedFilters.discount }"
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <div v-if="expandedFilters.discount" class="mt-2">
              <div class="flex items-center">
                <input 
                  type="checkbox" 
                  id="discount-only" 
                  v-model="discountOnly" 
                  class="w-4 h-4 text-amber-600 focus:ring-amber-500 rounded"
                />
                <label for="discount-only" class="ml-2 text-gray-700">
                  Тільки зі знижкою
                </label>
              </div>
            </div>
          </div>
          
          <!-- Кнопки фільтрації -->
          <div class="pt-4">
            <button 
              @click="applyFilters" 
              class="w-full bg-amber-600 hover:bg-amber-700 text-white py-2 px-4 rounded-md transition-colors duration-300 mb-2"
            >
              Застосувати фільтри
            </button>
            
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
const discountOnly = ref(false);
const sortOption = ref('name');
const loading = ref(false);
const currentPage = ref(1);
const pagination = ref<ApiPagination | null>(null);
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

// Рахуємо, чи є активні фільтри
const hasActiveFilters = computed(() => {
  const hasAttributeFilters = Object.values(selectedAttributes.value).some(values => values.length > 0);
  const hasRangeFilters = Object.values(rangeAttributes.value).some(([min, max]) => min !== null || max !== null);
  
  return selectedCategories.value.length > 0 || 
         priceFrom.value !== null || 
         priceTo.value !== null || 
         discountOnly.value || 
         hasAttributeFilters || 
         hasRangeFilters;
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
  
  // Скидаємо атрибути
  Object.keys(selectedAttributes.value).forEach(key => {
    selectedAttributes.value[key] = [];
  });
  
  // Скидаємо діапазони
  Object.keys(rangeAttributes.value).forEach(key => {
    rangeAttributes.value[key] = [null, null];
  });
  
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
  
  // Додаємо вибрані атрибути в URL
  Object.entries(selectedAttributes.value).forEach(([code, values]) => {
    if (values.length > 0) {
      query[`attr_${code}`] = values.join(',');
    }
  });
  
  // Додаємо діапазони в URL
  Object.entries(rangeAttributes.value).forEach(([code, [min, max]]) => {
    if (min !== null) {
      query[`${code}_from`] = min;
    }
    if (max !== null) {
      query[`${code}_to`] = max;
    }
  });
  
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
  
  // Завантажуємо атрибути з URL
  Object.entries(query).forEach(([key, value]) => {
    if (key.startsWith('attr_')) {
      const attrCode = key.replace('attr_', '');
      selectedAttributes.value[attrCode] = (value as string).split(',');
    }
    // Завантажуємо діапазони з URL
    else if (key.endsWith('_from')) {
      const attrCode = key.replace('_from', '');
      if (!rangeAttributes.value[attrCode]) {
        rangeAttributes.value[attrCode] = [null, null];
      }
      rangeAttributes.value[attrCode][0] = Number(value);
    }
    else if (key.endsWith('_to')) {
      const attrCode = key.replace('_to', '');
      if (!rangeAttributes.value[attrCode]) {
        rangeAttributes.value[attrCode] = [null, null];
      }
      rangeAttributes.value[attrCode][1] = Number(value);
    }
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
    
    // Додаємо ціновий діапазон
    if (priceFrom.value !== null) {
      url += `&price_from=${priceFrom.value}`;
    }
    
    if (priceTo.value !== null) {
      url += `&price_to=${priceTo.value}`;
    }
    
    // Додаємо фільтр по знижках
    if (discountOnly.value) {
      url += `&discount=1`;
    }
    
    // Додаємо атрибути
    Object.entries(selectedAttributes.value).forEach(([code, values]) => {
      if (values.length > 0) {
        // Форматуємо параметр як потрібно для вашого API
        url += `&attr_${code}=${values.join(',')}`;
      }
    });
    
    // Додаємо діапазони атрибутів
    Object.entries(rangeAttributes.value).forEach(([code, [min, max]]) => {
      if (min !== null) {
        url += `&${code}_from=${min}`;
      }
      if (max !== null) {
        url += `&${code}_to=${max}`;
      }
    });
    
    // Виконуємо запит
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
    
    // Оновлюємо доступні атрибути на основі завантажених продуктів
    extractProductAttributes();
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
</script> 
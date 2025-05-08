<template>
  <div class="bg-gray-50 py-8">
    <div class="container mx-auto px-4">
      <!-- Breadcrumbs -->
      <div class="mb-6">
        <nav class="flex items-center text-sm">
          <NuxtLink to="/" class="text-gray-500 hover:text-amber-600">Головна</NuxtLink>
          <span class="mx-2 text-gray-400">/</span>
          <NuxtLink to="/catalog" class="text-gray-500 hover:text-amber-600">Каталог</NuxtLink>
          <span class="mx-2 text-gray-400">/</span>
          <NuxtLink 
            v-if="product?.category" 
            :to="`/catalog?category=${product.category.slug}`" 
            class="text-gray-500 hover:text-amber-600"
          >
            {{ product?.category?.name }}
          </NuxtLink>
          <template v-if="product?.category">
            <span class="mx-2 text-gray-400">/</span>
          </template>
          <span class="text-gray-700 font-medium">{{ product?.name }}</span>
        </nav>
      </div>

      <!-- Основний блок -->
      <div v-if="loading" class="py-12 text-center">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-amber-600"></div>
        <p class="mt-4 text-gray-600">Завантаження товару...</p>
      </div>

      <div v-else-if="!product" class="bg-white p-8 rounded-lg shadow-sm text-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h1 class="text-xl font-semibold text-gray-800 mb-2">Товар не знайдено</h1>
        <p class="text-gray-600 mb-6">На жаль, товар, який ви шукаєте, не існує або був видалений</p>
        <NuxtLink to="/catalog" class="bg-amber-600 hover:bg-amber-700 text-white py-2 px-6 rounded-md transition-colors duration-300">
          Перейти до каталогу
        </NuxtLink>
      </div>
      
      <div v-else>
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
          <div class="flex flex-col md:flex-row">
            <!-- Галерея зображень -->
            <div class="w-full md:w-1/2 p-6">
              <div class="relative mb-4 bg-gray-100 rounded-lg overflow-hidden product-image-container">
                <img 
                  :src="currentImage || product.image || '/images/placeholder-product.jpg'" 
                  :alt="product.name" 
                  class="w-full h-auto max-h-[400px] mx-auto object-contain"
                />
                <div v-if="product.discount_percent" class="absolute top-4 left-4 bg-red-500 text-white text-sm font-bold px-2 py-1 rounded-md">
                  -{{ product.discount_percent }}%
                </div>
              </div>
              
              <!-- Мініатюри галереї -->
              <div v-if="product.gallery && product.gallery.length > 0" class="grid grid-cols-4 gap-2">
                <button 
                  v-for="(image, index) in allImages" 
                  :key="index" 
                  @click="currentImage = image" 
                  class="rounded-md overflow-hidden border-2 aspect-w-1 aspect-h-1"
                  :class="{ 'border-amber-500': currentImage === image, 'border-transparent': currentImage !== image }"
                >
                  <img 
                    :src="image" 
                    :alt="`${product.name} - зображення ${index + 1}`" 
                    class="w-full h-full object-cover"
                  />
                </button>
              </div>
            </div>
            
            <!-- Інформація про товар -->
            <div class="w-full md:w-1/2 p-6 border-t md:border-t-0 md:border-l">
              <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">{{ product.name }}</h1>
              
              <div class="flex items-center mb-4">
                <div class="flex items-center">
                  <span 
                    v-for="i in 5" 
                    :key="i" 
                    :class="[
                      'text-xl',
                      i <= Math.round(product.rating || 0) ? 'text-yellow-400' : 'text-gray-300'
                    ]"
                  >★</span>
                </div>
                <span class="ml-2 text-gray-600 text-sm">
                  {{ product.reviews_count || 0 }} відгуків
                </span>
                <span class="ml-4 text-green-600 text-sm font-medium" v-if="product.in_stock">
                  В наявності
                </span>
                <span class="ml-4 text-red-600 text-sm font-medium" v-else>
                  Немає в наявності
                </span>
              </div>
              
              <div class="mb-6">
                <div class="flex items-center">
                  <span v-if="product.old_price" class="text-gray-400 text-lg line-through mr-3">
                    {{ formatPrice(product.old_price) }} грн
                  </span>
                  <span class="text-gray-900 text-2xl font-bold">
                    {{ formatPrice(product.price) }} грн
                  </span>
                  <span v-if="product.discount_percent" class="ml-3 bg-red-100 text-red-800 text-xs font-medium py-1 px-2 rounded">
                    Економія: {{ formatPrice(product.old_price - product.price) }} грн
                  </span>
                </div>
                
                <div v-if="product.price_per_kg" class="text-gray-500 text-sm mt-1">
                  {{ formatPrice(product.price_per_kg) }} грн/кг
                </div>
              </div>
              
              <!-- Вибір варіантів (якщо є) -->
              <div v-if="product.variants && product.variants.length > 0" class="mb-6">
                <h3 class="text-gray-700 font-medium mb-2">Вага:</h3>
                <div class="flex flex-wrap gap-2">
                  <button
                    v-for="variant in product.variants"
                    :key="variant.id"
                    @click="selectedVariant = variant.id"
                    class="px-4 py-2 border rounded-md text-sm font-medium transition-colors duration-200"
                    :class="selectedVariant === variant.id 
                      ? 'bg-amber-600 text-white border-amber-600' 
                      : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                  >
                    {{ variant.name }}
                  </button>
                </div>
              </div>
              
              <!-- Кількість та кнопка додавання в кошик -->
              <div class="flex items-center space-x-4 mb-8">
                <div class="flex items-center border border-gray-300 rounded-md overflow-hidden h-12">
                  <button 
                    @click="quantity > 1 ? quantity-- : null"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 focus:outline-none h-full"
                  >-</button>
                  <input 
                    type="number" 
                    v-model="quantity" 
                    min="1" 
                    class="w-16 px-3 py-2 text-center focus:outline-none h-full border-x border-gray-300"
                  />
                  <button 
                    @click="quantity++"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 focus:outline-none h-full"
                  >+</button>
                </div>
                
                <button 
                  @click="addToCart"
                  :disabled="!product.in_stock"
                  class="flex-grow py-3 px-6 bg-amber-600 hover:bg-amber-700 disabled:bg-gray-400 text-white font-medium rounded-md transition-colors duration-300 flex items-center justify-center"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                  Додати в кошик
                </button>
              </div>
              
              <!-- Короткий опис -->
              <div class="mb-6">
                <h3 class="text-gray-700 font-medium mb-2">Про товар:</h3>
                <p class="text-gray-600">{{ product.description }}</p>
              </div>
              
              <!-- Характеристики -->
              <div v-if="product.features && product.features.length > 0" class="mb-6">
                <h3 class="text-gray-700 font-medium mb-2">Характеристики:</h3>
                <ul class="space-y-1">
                  <li v-for="(feature, index) in product.features" :key="index" class="flex text-sm">
                    <span class="text-gray-500 min-w-[120px]">{{ feature.name }}:</span>
                    <span class="text-gray-700 font-medium">{{ feature.value }}</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          
          <!-- Опис, характеристики та відгуки -->
          <div class="border-t">
            <div class="flex border-b">
              <button 
                v-for="(tab, index) in tabs" 
                :key="index"
                @click="activeTab = tab.id"
                class="px-6 py-4 font-medium text-sm focus:outline-none relative"
                :class="activeTab === tab.id ? 'text-amber-600' : 'text-gray-600 hover:text-amber-600'"
              >
                {{ tab.name }}
                <div
                  v-if="activeTab === tab.id"
                  class="absolute bottom-0 left-0 w-full h-0.5 bg-amber-600"
                ></div>
              </button>
            </div>
            
            <!-- Детальний опис -->
            <div v-if="activeTab === 'description'" class="p-6">
              <div v-if="product.long_description" class="prose prose-amber max-w-none" v-html="product.long_description"></div>
              <div v-else class="text-gray-600">
                <p>{{ product.description }}</p>
              </div>
            </div>
            
            <!-- Характеристики (якщо багато) -->
            <div v-if="activeTab === 'specifications'" class="p-6">
              <table class="w-full text-sm">
                <tbody>
                  <tr v-for="(spec, index) in product.specifications || []" :key="index" class="border-b">
                    <td class="py-3 px-4 text-gray-500">{{ spec.name }}</td>
                    <td class="py-3 px-4 text-gray-900 font-medium">{{ spec.value }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            
            <!-- Відгуки -->
            <div v-if="activeTab === 'reviews'" class="p-6">
              <div v-if="product.reviews && product.reviews.length > 0">
                <div v-for="(review, index) in product.reviews" :key="index" class="mb-6 border-b pb-6 last:border-b-0 last:pb-0">
                  <div class="flex justify-between mb-2">
                    <div>
                      <span class="font-medium text-gray-800">{{ review.author }}</span>
                      <span class="text-gray-500 text-sm ml-2">{{ formatDate(review.date) }}</span>
                    </div>
                    <div class="flex text-yellow-400">
                      <span v-for="i in 5" :key="i" :class="i <= review.rating ? 'text-yellow-400' : 'text-gray-300'">★</span>
                    </div>
                  </div>
                  <p class="text-gray-600">{{ review.text }}</p>
                </div>
              </div>
              <div v-else class="text-center py-8">
                <p class="text-gray-500 mb-4">Поки що немає відгуків на цей товар</p>
                <button class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-md transition-colors duration-300">
                  Написати відгук
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Рекомендовані товари -->
        <div class="mt-12">
          <h2 class="text-2xl font-semibold text-gray-900 mb-6">Вам також може сподобатися</h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <ProductCard 
              v-for="product in relatedProducts" 
              :key="product.id" 
              :product="product" 
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';

// Інтерфейси
interface ProductVariant {
  id: number;
  name: string;
  price: number;
}

interface ProductFeature {
  name: string;
  value: string;
}

interface ProductSpecification {
  name: string;
  value: string;
}

interface ProductReview {
  id: number;
  author: string;
  rating: number;
  text: string;
  date: string;
}

interface ProductCategory {
  id: number;
  name: string;
  slug: string;
}

interface Product {
  id: number;
  name: string;
  slug: string;
  description: string;
  long_description?: string;
  price: number;
  old_price?: number;
  discount_percent?: number;
  price_per_kg?: number;
  image: string;
  gallery?: string[];
  rating?: number;
  reviews_count?: number;
  in_stock: boolean;
  category?: ProductCategory;
  features?: ProductFeature[];
  specifications?: ProductSpecification[];
  reviews?: ProductReview[];
  variants?: ProductVariant[];
}

// Стан компонента
const route = useRoute();
const loading = ref(true);
const product = ref<Product | null>(null);
const relatedProducts = ref<Product[]>([]);
const quantity = ref(1);
const selectedVariant = ref<number | null>(null);
const activeTab = ref('description');
const currentImage = ref<string | null>(null);

// Вкладки
const tabs = [
  { id: 'description', name: 'Опис' },
  { id: 'specifications', name: 'Характеристики' },
  { id: 'reviews', name: 'Відгуки' }
];

// Всі зображення товару
const allImages = computed(() => {
  if (!product.value) return [];
  
  const images = [];
  if (product.value.image) {
    images.push(product.value.image);
  }
  
  if (product.value.gallery && product.value.gallery.length > 0) {
    images.push(...product.value.gallery);
  }
  
  return images;
});

// Методи
const formatPrice = (price: number): string => {
  return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
};

const formatDate = (dateString: string): string => {
  const date = new Date(dateString);
  return date.toLocaleDateString('uk-UA', { 
    day: '2-digit', 
    month: 'long', 
    year: 'numeric' 
  });
};

const addToCart = () => {
  // Тут буде логіка додавання в кошик
  console.log('Додаємо в кошик:', {
    productId: product.value?.id,
    variantId: selectedVariant.value,
    quantity: quantity.value
  });
};

// Завантаження товару
const fetchProduct = async () => {
  loading.value = true;
  
  try {
    // Отримуємо slug з URL
    const slug = route.params.slug as string;
    
    // В реальному проекті тут буде запит до API
    // const response = await fetch(`/api/products/${slug}`);
    // product.value = await response.json();
    
    // Тут використовуємо тестові дані
    // Імітуємо затримку запиту
    await new Promise(resolve => setTimeout(resolve, 500));
    
    // Тестові дані
    product.value = {
      id: 1,
      name: 'Мигдаль обсмажений',
      slug: 'mygdal-obsmazhenyy',
      description: 'Смачний обсмажений мигдаль без солі. Ідеальна закуска для перекусу.',
      long_description: `<p>Обсмажений мигдаль – це неймовірно смачна та корисна закуска, яка підходить для будь-якого випадку.</p>
        <p>Мигдаль багатий на:</p>
        <ul>
          <li>Білок</li>
          <li>Клітковину</li>
          <li>Вітамін E</li>
          <li>Магній</li>
          <li>Здорові жири</li>
        </ul>
        <p>Наш мигдаль обсмажений без додавання олії, за технологією сухого обсмажування, що дозволяє зберегти максимум корисних речовин.</p>`,
      price: 250,
      old_price: 280,
      discount_percent: 10,
      price_per_kg: 500,
      image: '/images/products/almonds.jpg',
      gallery: [
        '/images/products/almonds-1.jpg',
        '/images/products/almonds-2.jpg',
        '/images/products/almonds-3.jpg',
      ],
      rating: 4.7,
      reviews_count: 24,
      in_stock: true,
      category: {
        id: 1,
        name: 'Горіхи',
        slug: 'nuts'
      },
      features: [
        { name: 'Вага', value: '500 г' },
        { name: 'Країна', value: 'США' },
        { name: 'Обробка', value: 'Обсмажені' }
      ],
      specifications: [
        { name: 'Вага', value: '500 г' },
        { name: 'Країна походження', value: 'США' },
        { name: 'Тип обробки', value: 'Обсмажені без солі' },
        { name: 'Калорійність', value: '576 ккал / 100 г' },
        { name: 'Білки', value: '21.2 г / 100 г' },
        { name: 'Жири', value: '49.9 г / 100 г' },
        { name: 'Вуглеводи', value: '21.7 г / 100 г' },
        { name: 'Термін придатності', value: '6 місяців' },
        { name: 'Умови зберігання', value: 'Зберігати в сухому, прохолодному місці' },
      ],
      variants: [
        { id: 1, name: '100 г', price: 60 },
        { id: 2, name: '250 г', price: 140 },
        { id: 3, name: '500 г', price: 250 },
        { id: 4, name: '1 кг', price: 480 }
      ],
      reviews: [
        {
          id: 1,
          author: 'Ірина К.',
          rating: 5,
          text: 'Дуже смачний мигдаль! Свіжий, хрумкий, ідеально обсмажений. Замовляю вже не вперше, якість стабільно висока.',
          date: '2023-08-15'
        },
        {
          id: 2,
          author: 'Олександр М.',
          rating: 4,
          text: 'Хороший продукт, але деякі горішки трохи пересмажені. В цілому задоволений покупкою.',
          date: '2023-07-22'
        },
        {
          id: 3,
          author: 'Марія П.',
          rating: 5,
          text: 'Найкращий мигдаль, який я куштувала! Буду замовляти ще.',
          date: '2023-06-10'
        }
      ]
    };
    
    // Встановлюємо перше зображення як активне
    if (product.value && product.value.image) {
      currentImage.value = product.value.image;
    }
    
    // Завантажуємо пов'язані товари
    fetchRelatedProducts();
    
  } catch (error) {
    console.error('Помилка при завантаженні товару:', error);
    product.value = null;
  } finally {
    loading.value = false;
  }
};

const fetchRelatedProducts = async () => {
  try {
    // В реальному проекті тут буде запит до API
    // const response = await fetch(`/api/products/related/${product.value.id}`);
    // relatedProducts.value = await response.json();
    
    // Тестові дані
    relatedProducts.value = [
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
      },
      {
        id: 3,
        name: 'Кеш\'ю сирий',
        slug: 'keshyu-syryy',
        description: 'Сирий кеш\'ю без солі. Натуральний смак для справжніх цінителів.',
        price: 280,
        category: {
          id: 1,
          name: 'Горіхи',
          slug: 'nuts'
        },
        image: '/images/products/cashew.jpg',
        in_stock: true
      },
      {
        id: 4,
        name: 'Волоський горіх',
        slug: 'voloskyi-gorih',
        description: 'Волоські горіхи вищого ґатунку. Багаті омега-3 жирними кислотами.',
        price: 180,
        category: {
          id: 1,
          name: 'Горіхи',
          slug: 'nuts'
        },
        image: '/images/products/walnuts.jpg',
        in_stock: true
      },
      {
        id: 6,
        name: 'Фініки Medjool',
        slug: 'finiky-medjool',
        description: 'Преміальні фініки сорту Medjool. Великі та соковиті.',
        price: 350,
        category: {
          id: 2,
          name: 'Сухофрукти',
          slug: 'dried-fruits'
        },
        discount_percent: 15,
        old_price: 420,
        image: '/images/products/dates.jpg',
        in_stock: true
      }
    ];
    
  } catch (error) {
    console.error('Помилка при завантаженні пов\'язаних товарів:', error);
    relatedProducts.value = [];
  }
};

onMounted(() => {
  fetchProduct();
});
</script>

<style scoped>
.product-image-container {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 400px;
  padding: 1rem;
}

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
.aspect-w-1 {
  position: relative;
  padding-bottom: 100%; /* 1:1 Aspect Ratio */
}
.aspect-h-1 {
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 100%;
}
</style> 
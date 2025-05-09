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
            :to="`/catalog?category_id=${product.category.id}`" 
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
                  class="rounded-md overflow-hidden border-2 h-20"
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
                <span class="text-green-600 text-sm font-medium" v-if="product.in_stock">
                  В наявності
                </span>
                <span class="text-red-600 text-sm font-medium" v-else>
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
import { ref, watch } from 'vue';
import { computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useConfig } from '~/composables/useConfig';
import { useCart } from '~/composables/useCart';

// Отримуємо базовий URL API з конфігурації
const { apiBaseUrl } = useConfig();

// Отримуємо функції для роботи з кошиком
const { addToCart: addItemToCart } = useCart();

// Формуємо базову URL для зображень (без /api/)
const baseUrl = apiBaseUrl.replace('/api', '');

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
const router = useRouter();
const loading = ref(true);
const product = ref<Product | null>(null);
const relatedProducts = ref<Product[]>([]);
const quantity = ref(1);
const selectedVariant = ref<number | null>(null);
const activeTab = ref('description');
const currentImage = ref<string | null>(null);
const currentSlug = ref<string | null>(null);

// Вкладки
const tabs = [
  { id: 'description', name: 'Опис' },
  { id: 'specifications', name: 'Характеристики' }
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

const addToCart = async () => {
  if (!product.value || !product.value.id) return;
  
  const result = await addItemToCart(product.value.id, quantity.value);
  
  if (result) {
    // Товар успішно додано, кошик відкриється автоматично
  } else {
    // Помилка при додаванні товару (обробка помилки відбувається в useCart)
  }
};

// Функція налаштування продукту з даних API
const setupProduct = (data: any) => {
  // Перетворюємо дані з API в формат, який очікує наш компонент
  product.value = {
    id: data.entity_id,
    name: data.name,
    slug: data.sku.toLowerCase().replace(/\s+/g, '-'),
    description: data.short_description || data.description,
    long_description: data.description,
    price: Number(data.price),
    old_price: data.special_price ? Number(data.price) : undefined,
    discount_percent: data.special_price ? Math.round((1 - Number(data.special_price) / Number(data.price)) * 100) : undefined,
    image: data.image ? `${baseUrl}/storage/${data.image}` : '/images/placeholder-product.jpg',
    gallery: data.gallery ? data.gallery.map((img: string) => `${baseUrl}/storage/${img}`) : [],
    in_stock: data.is_in_stock,
    category: data.categories && data.categories.length > 0 ? {
      id: data.categories[0].category_id,
      name: data.categories[0].name,
      slug: data.categories[0].url_key
    } : undefined,
    features: [],
    specifications: []
  };
  
  // Додаємо характеристики товару
  if (data.weight_g) {
    product.value.specifications?.push({ name: 'Вага', value: `${data.weight_g} г` });
    product.value.features?.push({ name: 'Вага', value: `${data.weight_g} г` });
  }
  
  if (data.origin_country) {
    product.value.specifications?.push({ 
      name: 'Країна походження', 
      value: getCountryName(data.origin_country) 
    });
    product.value.features?.push({ 
      name: 'Країна', 
      value: getCountryName(data.origin_country) 
    });
  }
  
  if (data.roasted) {
    product.value.specifications?.push({ name: 'Тип обробки', value: 'Обсмажені' });
    product.value.features?.push({ name: 'Обробка', value: 'Обсмажені' });
  }
  
  if (data.salted) {
    product.value.specifications?.push({ name: 'Спеціальна обробка', value: 'Солоні' });
  }
  
  if (data.gluten_free) {
    product.value.specifications?.push({ name: 'Без глютену', value: 'Так' });
  }
  
  if (data.organic) {
    product.value.specifications?.push({ name: 'Органічний продукт', value: 'Так' });
  }
  
  if (data.cocoa_pct) {
    product.value.specifications?.push({ name: 'Відсоток какао', value: `${data.cocoa_pct}%` });
  }
  
  if (data.sweetness_level) {
    product.value.specifications?.push({ 
      name: 'Рівень солодкості', 
      value: getSweetnessLevelName(data.sweetness_level) 
    });
  }
  
  // Встановлюємо перше зображення як активне
  if (product.value && product.value.image) {
    currentImage.value = product.value.image;
  }
  
  // Завантажуємо пов'язані товари
  fetchRelatedProducts();
};

// Завантаження товару
const fetchProduct = async () => {
  loading.value = true;
  
  try {
    // Отримуємо slug з URL
    const slug = route.params.slug as string;
    
    // Скидаємо поточний продукт для оновлення даних
    product.value = null;
    currentSlug.value = slug;
    
    // Визначаємо, чи є slug числовим ID
    const isNumericId = !isNaN(Number(slug));
    let productId = isNumericId ? Number(slug) : null;
    
    // Якщо це не ID, шукаємо за SKU
    if (!isNumericId) {
      const allProductsResponse = await fetch(`${apiBaseUrl}/products?limit=50`);
      
      if (!allProductsResponse.ok) {
        throw new Error('Не вдалося отримати список товарів');
      }
      
      const allProductsData = await allProductsResponse.json();
      
      if (allProductsData.data && allProductsData.data.length > 0) {
        // Спочатку шукаємо точний збіг за SKU
        let foundProduct = allProductsData.data.find((p: any) => {
          // Порівнюємо нормалізовані значення SKU з slug
          const normalizedSku = p.sku.toLowerCase().trim().replace(/\s+/g, '-');
          return normalizedSku === slug;
        });
        
        // Якщо не знайдено, шукаємо за частковим збігом SKU
        if (!foundProduct) {
          foundProduct = allProductsData.data.find((p: any) => {
            const normalizedSku = p.sku.toLowerCase().trim().replace(/\s+/g, '-');
            return normalizedSku.includes(slug) || slug.includes(normalizedSku);
          });
        }
        
        // Якщо не знайдено за SKU, шукаємо частковий збіг за назвою
        if (!foundProduct) {
          foundProduct = allProductsData.data.find((p: any) => {
            const normalizedName = p.name.toLowerCase().trim().replace(/\s+/g, '-');
            return normalizedName.includes(slug) || slug.includes(normalizedName);
          });
        }
        
        // Якщо нічого не допомогло і slug містить "fistashky", знаходимо фісташки
        if (!foundProduct && slug.includes('fistashky')) {
          foundProduct = allProductsData.data.find((p: any) => 
            p.name.includes('Фісташк')
          );
        }
        
        if (foundProduct) {
          productId = foundProduct.entity_id;
        } else {
          // Якщо нічого не знайдено, використовуємо перший продукт
          productId = allProductsData.data[0].entity_id;
        }
      }
    }
    
    if (!productId) {
      throw new Error('Не вдалося визначити ідентифікатор товару');
    }
    
    // Отримуємо детальну інформацію про продукт за його id
    const response = await fetch(`${apiBaseUrl}/products/${productId}`);
    
    if (!response.ok) {
      throw new Error('Не вдалося завантажити товар');
    }
    
    const data = await response.json();
    
    setupProduct(data);
    
  } catch (error) {
    console.error('Помилка при завантаженні товару:', error);
    product.value = null;
  } finally {
    loading.value = false;
  }
};

// Додаємо допоміжні функції для роботи з атрибутами
const getCountryName = (code: string): string => {
  const countries: Record<string, string> = {
    'UA': 'Україна',
    'US': 'США',
    'TR': 'Туреччина',
    'IT': 'Італія',
    'ES': 'Іспанія',
    'FR': 'Франція',
    'GR': 'Греція',
    'CN': 'Китай',
    'IN': 'Індія',
    'ID': 'Індонезія',
    'BR': 'Бразилія',
    'CL': 'Чилі',
    'AU': 'Австралія',
    'NZ': 'Нова Зеландія'
  };
  
  return countries[code] || code;
};

const getSweetnessLevelName = (level: number): string => {
  const levels: Record<number, string> = {
    1: 'Не солодкий',
    2: 'Трохи солодкий',
    3: 'Помірно солодкий',
    4: 'Солодкий',
    5: 'Дуже солодкий'
  };
  
  return levels[level] || `Рівень ${level}`;
};

// Оновлюємо функцію fetchRelatedProducts, щоб отримувати випадкові продукти
const fetchRelatedProducts = async () => {
  try {
    // Запит випадкових товарів
    const params = new URLSearchParams();
    params.append('limit', '8'); // Збільшуємо ліміт, щоб мати можливість вибрати випадкові продукти
    
    if (product.value) {
      // Виключаємо поточний товар з результатів
      params.append('exclude_id', product.value.id.toString());
    }
    
    const response = await fetch(`${apiBaseUrl}/products?${params.toString()}`);
    
    if (!response.ok) {
      throw new Error('Не вдалося завантажити пов\'язані товари');
    }
    
    const data = await response.json();
    
    // Перемішуємо продукти для ще більшої випадковості
    const shuffledProducts = (data.data || [])
      .filter((p: any) => p.entity_id !== product.value?.id)
      .sort(() => 0.5 - Math.random());
    
    // Перетворюємо дані до потрібного формату (лише перші 4)
    relatedProducts.value = shuffledProducts.slice(0, 4).map((item: any) => ({
      id: item.entity_id,
      name: item.name,
      slug: item.sku.toLowerCase().replace(/\s+/g, '-'),
      description: item.short_description || item.description,
      price: Number(item.price),
      old_price: item.special_price ? Number(item.price) : undefined,
      discount_percent: item.special_price ? Math.round((1 - Number(item.special_price) / Number(item.price)) * 100) : undefined,
      image: item.image ? `${baseUrl}/storage/${item.image}` : '/images/placeholder-product.jpg',
      in_stock: item.is_in_stock,
      category: item.categories && item.categories.length > 0 ? {
        id: item.categories[0].category_id,
        name: item.categories[0].name,
        slug: item.categories[0].url_key
      } : undefined
    }));
    
  } catch (error) {
    console.error('Помилка при завантаженні пов\'язаних товарів:', error);
    relatedProducts.value = [];
  }
};

onMounted(() => {
  fetchProduct();
});

// Слідкуємо за змінами параметрів URL
watch(() => route.params.slug, (newSlug, oldSlug) => {
  if (newSlug !== oldSlug) {
    fetchProduct();
  }
}, { immediate: true }); // immediate: true забезпечує виконання при ініціалізації

// Додаємо обробник навігації для підтримки нормальної роботи при прямому переході на URL
router.beforeResolve((to: any, from: any, next: any) => {
  // Скидаємо стан при зміні маршруту на інший продукт
  if (to.name === 'product-slug' && from.name === 'product-slug' && to.params.slug !== from.params.slug) {
    product.value = null;
    currentSlug.value = null;
    relatedProducts.value = [];
  }
  next();
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

/* Видаляємо проблемні стилі з aspect ratio, які викликають проблеми з перекриттям */
/* Натомість використовуємо фіксовану висоту для мініатюр (h-20 в HTML) */
</style> 
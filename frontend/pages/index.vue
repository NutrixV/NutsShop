<template>
  <div>
    <!-- Hero секція -->
    <section class="relative bg-amber-50">
      <div class="container mx-auto px-4 py-12 md:py-24">
        <div class="flex flex-col md:flex-row items-center">
          <div class="w-full md:w-1/2 mb-8 md:mb-0 text-center md:text-left">
            <h1 class="text-3xl md:text-5xl font-bold text-gray-900 mb-4">Натуральні горіхи та сухофрукти</h1>
            <p class="text-lg text-gray-600 mb-8">Найкращі продукти для вашого здоров'я та гарного самопочуття за найкращими цінами</p>
            <NuxtLink to="/catalog" class="inline-block bg-amber-600 hover:bg-amber-700 text-white py-3 px-8 rounded-full font-medium text-lg transition-colors duration-300">
              Перейти до каталогу
            </NuxtLink>
          </div>
          <div class="w-full md:w-1/2">
            <img src="/images/hero-nuts.png" alt="Асортимент горіхів" class="rounded-lg shadow-lg w-full h-auto max-w-lg mx-auto">
          </div>
        </div>
      </div>
      <!-- Декоративний елемент -->
      <div class="absolute bottom-0 left-0 right-0 h-12 bg-white" style="clip-path: ellipse(50% 100% at 50% 100%);"></div>
    </section>

    <!-- Переваги -->
    <section class="py-12 md:py-20">
      <div class="container mx-auto px-4">
        <h2 class="text-2xl md:text-3xl font-semibold text-center mb-12">Чому обирають нас</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="p-6 bg-white rounded-xl shadow-sm text-center hover:shadow-lg transition-shadow duration-300">
            <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-amber-600" viewBox="0 0 20 20" fill="currentColor">
                <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z" />
              </svg>
            </div>
            <h3 class="text-lg font-medium mb-2">Швидка доставка</h3>
            <p class="text-gray-600">Доставка по всій країні протягом 1-3 робочих днів</p>
          </div>
          <div class="p-6 bg-white rounded-xl shadow-sm text-center hover:shadow-lg transition-shadow duration-300">
            <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-amber-600" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
            </div>
            <h3 class="text-lg font-medium mb-2">Гарантія якості</h3>
            <p class="text-gray-600">Всі наші товари проходять ретельний контроль якості</p>
          </div>
          <div class="p-6 bg-white rounded-xl shadow-sm text-center hover:shadow-lg transition-shadow duration-300">
            <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-amber-600" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
              </svg>
            </div>
            <h3 class="text-lg font-medium mb-2">Найкращі ціни</h3>
            <p class="text-gray-600">Ми постійно моніторимо ринок для найвигідніших пропозицій</p>
          </div>
          <div class="p-6 bg-white rounded-xl shadow-sm text-center hover:shadow-lg transition-shadow duration-300">
            <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-amber-600" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-2 0c0 .993-.241 1.929-.668 2.754l-1.524-1.525a3.997 3.997 0 00.078-2.183l1.562-1.562C15.802 8.249 16 9.1 16 10zm-5.165 3.913l1.58 1.58A5.98 5.98 0 0110 16a5.976 5.976 0 01-2.516-.552l1.562-1.562a4.006 4.006 0 001.789.027zm-4.677-2.796a4.002 4.002 0 01-.041-2.08l-.08.08-1.53-1.533A5.98 5.98 0 004 10c0 .954.223 1.856.619 2.657l1.54-1.54zm1.088-6.45A5.974 5.974 0 0110 4c.954 0 1.856.223 2.657.619l-1.54 1.54a4.002 4.002 0 00-2.346.033L7.246 4.668zM12 10a2 2 0 11-4 0 2 2 0 014 0z" clip-rule="evenodd" />
              </svg>
            </div>
            <h3 class="text-lg font-medium mb-2">Підтримка 24/7</h3>
            <p class="text-gray-600">Наша команда завжди готова відповісти на ваші запитання</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Популярні товари -->
    <section class="py-12 bg-gray-50">
      <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
          <h2 class="text-2xl md:text-3xl font-semibold">Популярні товари</h2>
          <NuxtLink to="/catalog" class="text-amber-600 hover:text-amber-800 font-medium">Переглянути всі</NuxtLink>
        </div>

        <div v-if="featuredProducts.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <ProductCard v-for="product in featuredProducts" :key="product.id" :product="product" />
        </div>
        <div v-else class="flex justify-center items-center h-40 bg-white rounded-lg shadow-sm">
          <p class="text-gray-500">Завантаження товарів...</p>
        </div>
      </div>
    </section>

    <!-- Категорії -->
    <section class="py-12 md:py-20">
      <div class="container mx-auto px-4">
        <h2 class="text-2xl md:text-3xl font-semibold text-center mb-12">Категорії товарів</h2>

        <div v-if="categories.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
          <NuxtLink 
            v-for="category in categories" 
            :key="category.id" 
            :to="`/catalog?category_id=${category.id}`"
            class="relative overflow-hidden rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 group h-64"
          >
            <img 
              v-if="category.image"
              :src="category.image" 
              :alt="category.name"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            />
            <img 
              v-else
              src="/images/placeholder-category.jpg" 
              :alt="category.name"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-6">
              <div>
                <h3 class="text-white text-xl font-bold mb-1">{{ category.name }}</h3>
                <p class="text-white/80 text-sm">Переглянути товари</p>
              </div>
            </div>
          </NuxtLink>
        </div>
        <div v-else class="flex justify-center items-center h-40 bg-white rounded-lg shadow-sm">
          <p class="text-gray-500">Завантаження категорій...</p>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useConfig } from '~/composables/useConfig';

// Отримуємо URL API
const { apiBaseUrl } = useConfig();
// Базовий URL для зображень
const baseUrl = apiBaseUrl.replace('/api', '');

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

// Ініціалізуємо порожні масиви для продуктів і категорій
const featuredProducts = ref<Product[]>([]);
const categories = ref<Category[]>([]);

// Завантаження популярних товарів
const fetchFeaturedProducts = async () => {
  try {
    const response = await fetch(`${apiBaseUrl}/products?featured=1&limit=4`);
    if (!response.ok) throw new Error('Не вдалося завантажити популярні товари');
    
    const data = await response.json();
    
    // Перетворюємо дані до потрібного формату
    featuredProducts.value = (data.data || []).map((item: any) => ({
      id: item.entity_id,
      name: item.name,
      slug: item.sku.toLowerCase().replace(/\s+/g, '-'),
      description: item.short_description || item.description,
      price: Number(item.price),
      image: item.image ? `${baseUrl}/storage/${item.image}` : '/images/placeholder-product.jpg',
    }));
  } catch (error) {
    console.error('Помилка при завантаженні популярних товарів:', error);
    // Використовуємо тестові дані якщо не вдалося завантажити дані з API
    featuredProducts.value = [
      {
        id: 1,
        name: 'Мигдаль обсмажений',
        slug: 'mygdal-obsmazhenyy',
        description: 'Смачний обсмажений мигдаль. Ідеальна закуска для перекусу.',
        price: 250,
        image: '/images/products/almonds.jpg'
      },
      {
        id: 2,
        name: 'Фісташки солоні',
        slug: 'fistashky-soloni',
        description: 'Хрусткі солоні фісташки. Ідеально підходять до пива або вина.',
        price: 320,
        image: '/images/products/pistachios.jpg'
      },
      {
        id: 3,
        name: 'Кеш\'ю сирий',
        slug: 'keshyu-syryy',
        description: 'Сирий кеш\'ю без солі. Натуральний смак для справжніх цінителів.',
        price: 280,
        image: '/images/products/cashew.jpg'
      },
      {
        id: 4,
        name: 'Волоський горіх',
        slug: 'voloskyi-gorih',
        description: 'Волоські горіхи вищого ґатунку. Багаті омега-3 жирними кислотами.',
        price: 180,
        image: '/images/products/walnuts.jpg'
      }
    ];
  }
};

// Завантаження категорій
const fetchCategories = async () => {
  try {
    const response = await fetch(`${apiBaseUrl}/categories?active=1`);
    if (!response.ok) throw new Error('Не вдалося завантажити категорії');
    
    const data = await response.json();
    
    // Перетворюємо дані до потрібного формату
    categories.value = data.map((item: any) => ({
      id: item.category_id,
      name: item.name,
      slug: item.url_key,
      image: item.image ? `${baseUrl}/storage/${item.image}` : '/images/placeholder-category.jpg',
    }));
  } catch (error) {
    console.error('Помилка при завантаженні категорій:', error);
    // Використовуємо тестові дані якщо не вдалося завантажити дані з API
    categories.value = [
      {
        id: 1,
        name: 'Горіхи',
        slug: 'nuts',
        image: '/images/categories/nuts.jpg'
      },
      {
        id: 2,
        name: 'Сухофрукти',
        slug: 'dried-fruits',
        image: '/images/categories/dried-fruits.jpg'
      },
      {
        id: 3,
        name: 'Насіння',
        slug: 'seeds',
        image: '/images/categories/seeds.jpg'
      },
      {
        id: 4,
        name: 'Корисні солодощі',
        slug: 'sweets',
        image: '/images/categories/sweets.jpg'
      },
      {
        id: 5,
        name: 'Подарункові набори',
        slug: 'gift-sets',
        image: '/images/categories/gift-sets.jpg'
      },
      {
        id: 6,
        name: 'Суперфуди',
        slug: 'superfoods',
        image: '/images/categories/superfoods.jpg'
      }
    ];
  }
};

// Ініціалізація - завантажуємо дані при монтуванні компонента
onMounted(() => {
  fetchFeaturedProducts();
  fetchCategories();
});
</script>

<style scoped>
/* Видаляємо старі стилі для aspect ratio */
/* Замість них додаємо нові стилі для секції категорій */
.group {
  display: block;
  position: relative;
}

.group img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
</style> 
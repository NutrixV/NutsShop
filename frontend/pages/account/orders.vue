<template>
  <div class="bg-gray-50 min-h-screen py-12">
    <div class="container mx-auto px-4">
      <div class="max-w-4xl mx-auto">
        <div class="flex items-center mb-6">
          <NuxtLink to="/account" class="text-amber-600 hover:text-amber-800 mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Назад до кабінету
          </NuxtLink>
        </div>
        
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Мої замовлення</h1>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
          <!-- Завантаження -->
          <div v-if="loading" class="p-6 text-center">
            <div class="inline-block animate-spin h-8 w-8 border-4 border-amber-600 border-t-transparent rounded-full"></div>
            <p class="mt-2 text-gray-600">Завантаження замовлень...</p>
          </div>
          
          <!-- Порожній список -->
          <div v-else-if="orders.length === 0" class="p-12 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <h2 class="text-xl font-semibold text-gray-700 mb-2">У вас ще немає замовлень</h2>
            <p class="text-gray-600 mb-6">Перегляньте наш каталог і зробіть перше замовлення</p>
            <NuxtLink to="/catalog" class="inline-block bg-amber-600 text-white px-4 py-2 rounded-md hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
              Перейти до каталогу
            </NuxtLink>
          </div>
          
          <!-- Список замовлень -->
          <div v-else>
            <div v-for="order in orders" :key="order.id" class="border-b border-gray-200 last:border-b-0">
              <div class="p-6">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                  <div>
                    <div class="flex items-center mb-2">
                      <h2 class="text-lg font-medium mr-3">Замовлення #{{ order.id }}</h2>
                      <span class="px-2 py-1 text-xs rounded-full" 
                        :class="{
                          'bg-green-100 text-green-800': order.status === 'completed',
                          'bg-blue-100 text-blue-800': order.status === 'processing',
                          'bg-yellow-100 text-yellow-800': order.status === 'pending',
                          'bg-red-100 text-red-800': order.status === 'cancelled'
                        }">
                        {{ getStatusText(order.status) }}
                      </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-1">Дата: {{ formatDate(order.created_at) }}</p>
                    <p class="text-sm text-gray-600">Товарів: {{ order.items_count || 0 }} шт.</p>
                  </div>
                  
                  <div class="mt-4 md:mt-0 flex flex-col items-start md:items-end">
                    <p class="text-xl font-semibold text-gray-900 mb-2">{{ formatPrice(order.total) }} грн</p>
                    <button 
                      class="text-amber-600 hover:text-amber-800 text-sm font-medium flex items-center"
                      @click="toggleOrderDetails(order.id)"
                    >
                      <span>{{ expandedOrder === order.id ? 'Приховати деталі' : 'Показати деталі' }}</span>
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" 
                        :class="{ 'transform rotate-180': expandedOrder === order.id }"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                      </svg>
                    </button>
                  </div>
                </div>
                
                <div v-if="expandedOrder === order.id" class="mt-6 border-t border-gray-200 pt-4">
                  <h3 class="text-base font-medium mb-3">Деталі замовлення</h3>
                  
                  <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                      <thead class="bg-gray-50">
                        <tr>
                          <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Товар
                          </th>
                          <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Кількість
                          </th>
                          <th scope="col" class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ціна
                          </th>
                          <th scope="col" class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Сума
                          </th>
                        </tr>
                      </thead>
                      <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="item in order.items" :key="item.id">
                          <td class="px-3 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                              <div class="h-10 w-10 flex-shrink-0">
                                <img :src="item.image" :alt="item.product_name" class="h-10 w-10 rounded-md object-cover">
                              </div>
                              <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ item.product_name }}</div>
                              </div>
                            </div>
                          </td>
                          <td class="px-3 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            {{ item.quantity }}
                          </td>
                          <td class="px-3 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                            {{ formatPrice(item.price) }} грн
                          </td>
                          <td class="px-3 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                            {{ formatPrice(item.price * item.quantity) }} грн
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  
                  <div class="mt-4 text-right">
                    <div class="inline-block">
                      <div class="flex justify-between mt-2">
                        <span class="text-sm text-gray-600 mr-8">Cума:</span>
                        <span class="text-sm text-gray-900">{{ formatPrice(order.subtotal || order.total) }} грн</span>
                      </div>
                      <div class="flex justify-between mt-2" v-if="order.shipping_cost">
                        <span class="text-sm text-gray-600 mr-8">Доставка:</span>
                        <span class="text-sm text-gray-900">{{ formatPrice(order.shipping_cost) }} грн</span>
                      </div>
                      <div class="flex justify-between mt-2 font-medium">
                        <span class="text-base text-gray-900 mr-8">Всього:</span>
                        <span class="text-base text-gray-900">{{ formatPrice(order.total) }} грн</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useApi } from '~/composables/useApi';
import { useConfig } from '~/composables/useConfig';

interface OrderItem {
  id: string;
  product_name: string;
  image: string;
  price: number;
  quantity: number;
}

interface Order {
  id: string;
  status: 'pending' | 'processing' | 'completed' | 'cancelled';
  created_at: string;
  total: number;
  subtotal?: number;
  shipping_cost?: number;
  items_count?: number;
  items: OrderItem[];
}

const router = useRouter();
const route = useRoute();
const loading = ref(true);
const orders = ref<Order[]>([]);
const expandedOrder = ref<string | null>(null);
const { apiBaseUrl } = useConfig();

// Форматування ціни
const formatPrice = (price: number): string => {
  return parseFloat(price.toString()).toFixed(2);
};

// Форматування дати
const formatDate = (dateString: string): string => {
  const date = new Date(dateString);
  return date.toLocaleDateString('uk-UA', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  });
};

// Переведення статусу
const getStatusText = (status: Order['status']): string => {
  const statusMap = {
    pending: 'Очікує оплати',
    processing: 'В обробці',
    completed: 'Виконано',
    cancelled: 'Скасовано'
  };
  return statusMap[status] || status;
};

// Переключення деталей замовлення
const toggleOrderDetails = (orderId: string) => {
  if (expandedOrder.value === orderId) {
    expandedOrder.value = null;
  } else {
    expandedOrder.value = orderId;
  }
};

// Завантаження даних користувача та замовлень
onMounted(() => {
  // Перевірка автентифікації
  const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
  if (!isLoggedIn) {
    // Якщо користувач не авторизований, перенаправляємо на сторінку логіну
    router.push('/auth/login');
    return;
  }

  // Завантаження замовлень користувача з API
  loadUserOrders();
  
  // Перевіряємо, чи є параметр order в URL (перехід з кабінету користувача за "Детальніше")
  if (route.query.order) {
    expandedOrder.value = route.query.order as string;
  }
});

// Функція для завантаження замовлень користувача
const loadUserOrders = async () => {
  loading.value = true;
  
  try {
    const { get } = useApi();
    const result = await get('/orders');
    
    if (result.success && result.data.success) {
      // Тимчасовий масив для зберігання замовлень перед завантаженням додаткової інформації
      const tempOrders = result.data.data.map((order: any) => ({
        id: order.increment_id,
        status: order.status,
        created_at: order.created_at,
        total: order.grand_total,
        subtotal: order.subtotal,
        shipping_cost: 0, // У даній версії додатку доставка безкоштовна
        items_count: order.items?.length || 0,
        items: order.items?.map((item: any) => ({
          id: item.item_id,
          product_id: item.product_id,
          product_name: item.name,
          image: '/images/products/placeholder-product.jpg', // Плейсхолдер за замовчуванням
          price: item.price,
          quantity: item.qty_ordered
        })) || []
      }));

      // Для кожного замовлення завантажуємо інформацію про продукти
      for (const order of tempOrders) {
        for (const item of order.items) {
          // Отримуємо інформацію про продукт за ID
          try {
            const productResponse = await get(`/products/${item.product_id}`);
            if (productResponse.success && productResponse.data) {
              // Оновлюємо зображення, якщо є
              if (productResponse.data.image) {
                // Видаляємо '/api' з URL для правильного доступу до зображень
                const baseUrl = apiBaseUrl.replace('/api', '');
                item.image = `${baseUrl}/storage/${productResponse.data.image}`;
              }
            }
          } catch (error) {
            console.error(`Помилка при завантаженні деталей продукту ${item.product_id}:`, error);
          }
        }
      }

      // Встановлюємо замовлення з оновленими зображеннями
      orders.value = tempOrders;
    } else {
      console.error('Помилка при завантаженні замовлень:', result.data.message);
    }
  } catch (error) {
    console.error('Помилка при завантаженні замовлень:', error);
  } finally {
    loading.value = false;
  }
};
</script>
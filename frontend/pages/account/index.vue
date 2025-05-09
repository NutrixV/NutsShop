<template>
  <div class="bg-gray-50 min-h-screen py-12">
    <div class="container mx-auto px-4">
      <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Особистий кабінет</h1>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
          <div class="py-4 px-6 bg-amber-600">
            <h2 class="text-xl font-bold text-white">Інформація про користувача</h2>
          </div>
          
          <div class="p-6" v-if="customer">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <h3 class="text-gray-500 text-sm font-medium mb-1">Ім'я та прізвище</h3>
                <p class="text-gray-900">{{ customer.first_name }} {{ customer.last_name }}</p>
              </div>
              
              <div>
                <h3 class="text-gray-500 text-sm font-medium mb-1">Email</h3>
                <p class="text-gray-900">{{ customer.email }}</p>
              </div>
            </div>
            
            <div class="mt-6">
              <button 
                class="bg-amber-600 text-white py-2 px-4 rounded-md hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500"
              >
                Редагувати профіль
              </button>
            </div>
          </div>
          
          <div class="p-6" v-else>
            <p class="text-gray-600">Завантаження інформації...</p>
          </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
          <div class="py-4 px-6 bg-amber-600">
            <h2 class="text-xl font-bold text-white">Мої замовлення</h2>
          </div>
          
          <div class="p-6">
            <div class="mb-4" v-if="orders.length > 0">
              <div class="border-b border-gray-200 pb-2 mb-4" v-for="order in orders" :key="order.id">
                <div class="flex justify-between items-center mb-2">
                  <h3 class="text-lg font-medium">Замовлення #{{ order.id }}</h3>
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
                <div class="text-sm text-gray-600 mb-2">
                  Дата: {{ formatDate(order.created_at) }}
                </div>
                <div class="text-sm text-gray-800 mb-2">
                  Сума: {{ formatPrice(order.total) }} грн
                </div>
                <div class="mt-2">
                  <NuxtLink 
                    :to="`/account/orders?order=${order.id}`" 
                    class="text-amber-600 hover:text-amber-800 text-sm font-medium"
                  >
                    Детальніше
                  </NuxtLink>
                </div>
              </div>
            </div>
            
            <div v-else class="text-center py-4">
              <p class="text-gray-600 mb-4">У вас ще немає замовлень</p>
              <NuxtLink to="/catalog" class="text-amber-600 hover:text-amber-800 font-medium">
                Перейти до каталогу
              </NuxtLink>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useApi } from '~/composables/useApi';

interface Customer {
  first_name: string;
  last_name: string;
  email: string;
}

interface Order {
  id: string;
  status: 'pending' | 'processing' | 'completed' | 'cancelled';
  created_at: string;
  total: number;
  items_count: number;
}

const router = useRouter();
const customer = ref<Customer | null>(null);
const orders = ref<Order[]>([]);

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

// Завантаження даних користувача та замовлень
onMounted(() => {
  // Перевірка автентифікації
  const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
  if (!isLoggedIn) {
    // Якщо користувач не авторизований, перенаправляємо на сторінку логіну
    router.push('/auth/login');
    return;
  }

  // Завантаження даних користувача
  const customerData = localStorage.getItem('customer');
  if (customerData) {
    try {
      customer.value = JSON.parse(customerData) as Customer;
    } catch (error) {
      console.error('Error parsing customer data:', error);
    }
  }

  // Завантаження замовлень користувача з API
  loadUserOrders();
});

// Функція для завантаження замовлень користувача
const loadUserOrders = async () => {
  try {
    const { get } = useApi();
    const result = await get('/api/orders');
    
    if (result.success && result.data.success) {
      // Перетворюємо дані з API в формат, який очікує компонент
      orders.value = result.data.data.map((order: any) => ({
        id: order.increment_id,
        status: order.status,
        created_at: order.created_at,
        total: order.grand_total,
        items_count: order.items?.length || 0
      }));
    } else {
      console.error('Помилка при завантаженні замовлень:', result.data.message);
    }
  } catch (error) {
    console.error('Помилка при завантаженні замовлень:', error);
  }
};
</script> 
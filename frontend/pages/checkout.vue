<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Оформлення замовлення</h1>
    
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-amber-600"></div>
      <p class="mt-4 text-gray-600">Обробка вашого замовлення...</p>
    </div>
    
    <div v-else-if="orderCreated" class="bg-white rounded-lg shadow-md p-8 text-center">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-green-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
      </svg>
      <h2 class="text-2xl font-semibold text-gray-800 mb-2">Замовлення успішно оформлено!</h2>
      <p class="text-gray-600 mb-6">Номер вашого замовлення: <span class="font-bold">{{ orderId }}</span></p>
      <NuxtLink to="/" class="inline-block bg-amber-600 hover:bg-amber-700 text-white py-3 px-8 rounded-md font-medium transition-colors duration-300">
        Повернутися на головну
      </NuxtLink>
    </div>
    
    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Форма оформлення замовлення -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
          <h2 class="text-xl font-semibold mb-4">Інформація для доставки</h2>
          
          <form @submit.prevent="processCheckout">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <div>
                <label for="firstName" class="block text-sm font-medium text-gray-700 mb-1">Ім'я *</label>
                <input 
                  type="text" 
                  id="firstName" 
                  v-model="form.firstName"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500"
                  :class="{'border-red-500': errors.firstName}"
                  required
                />
                <p v-if="errors.firstName" class="mt-1 text-sm text-red-500">{{ errors.firstName }}</p>
              </div>
              
              <div>
                <label for="lastName" class="block text-sm font-medium text-gray-700 mb-1">Прізвище *</label>
                <input 
                  type="text" 
                  id="lastName" 
                  v-model="form.lastName"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500"
                  :class="{'border-red-500': errors.lastName}"
                  required
                />
                <p v-if="errors.lastName" class="mt-1 text-sm text-red-500">{{ errors.lastName }}</p>
              </div>
            </div>
            
            <div class="mb-4">
              <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
              <input 
                type="email" 
                id="email" 
                v-model="form.email"
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500"
                :class="{'border-red-500': errors.email}"
                required
              />
              <p v-if="errors.email" class="mt-1 text-sm text-red-500">{{ errors.email }}</p>
            </div>
            
            <div class="mb-4">
              <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Телефон *</label>
              <input 
                type="tel" 
                id="phone" 
                v-model="form.phone"
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500"
                :class="{'border-red-500': errors.phone}"
                required
              />
              <p v-if="errors.phone" class="mt-1 text-sm text-red-500">{{ errors.phone }}</p>
            </div>
            
            <div class="mb-4">
              <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Адреса *</label>
              <input 
                type="text" 
                id="address" 
                v-model="form.address"
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500"
                :class="{'border-red-500': errors.address}"
                required
              />
              <p v-if="errors.address" class="mt-1 text-sm text-red-500">{{ errors.address }}</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
              <div>
                <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Місто *</label>
                <input 
                  type="text" 
                  id="city" 
                  v-model="form.city"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500"
                  :class="{'border-red-500': errors.city}"
                  required
                />
                <p v-if="errors.city" class="mt-1 text-sm text-red-500">{{ errors.city }}</p>
              </div>
              
              <div>
                <label for="region" class="block text-sm font-medium text-gray-700 mb-1">Область *</label>
                <input 
                  type="text" 
                  id="region" 
                  v-model="form.region"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500"
                  :class="{'border-red-500': errors.region}"
                  required
                />
                <p v-if="errors.region" class="mt-1 text-sm text-red-500">{{ errors.region }}</p>
              </div>
              
              <div>
                <label for="postcode" class="block text-sm font-medium text-gray-700 mb-1">Поштовий індекс *</label>
                <input 
                  type="text" 
                  id="postcode" 
                  v-model="form.postcode"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500"
                  :class="{'border-red-500': errors.postcode}"
                  required
                />
                <p v-if="errors.postcode" class="mt-1 text-sm text-red-500">{{ errors.postcode }}</p>
              </div>
            </div>
            
            <div class="mb-4">
              <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Примітки до замовлення</label>
              <textarea 
                id="notes" 
                v-model="form.notes"
                rows="3" 
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500"
              ></textarea>
            </div>
          
          <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h2 class="text-xl font-semibold mb-4">Спосіб оплати</h2>
            <div class="space-y-4">
              <div class="flex items-center">
                <input 
                  type="radio" 
                  id="payment1" 
                  name="payment" 
                  value="cash" 
                  v-model="form.paymentMethod"
                  class="h-4 w-4 text-amber-600 focus:ring-amber-500" 
                />
                <label for="payment1" class="ml-3">Післяплата</label>
              </div>
              
              <div class="flex items-center">
                <input 
                  type="radio" 
                  id="payment2" 
                  name="payment" 
                  value="card" 
                  v-model="form.paymentMethod"
                  class="h-4 w-4 text-amber-600 focus:ring-amber-500"
                />
                <label for="payment2" class="ml-3">Оплата карткою (онлайн)</label>
              </div>
              <p v-if="errors.paymentMethod" class="mt-1 text-sm text-red-500">{{ errors.paymentMethod }}</p>
            </div>
          </div>
          
          <!-- Загальна помилка -->
          <div v-if="errorMessage" class="mt-4 p-3 bg-red-100 border border-red-200 text-red-700 rounded-md">
            {{ errorMessage }}
          </div>
          
          <div class="mt-6 lg:hidden">
            <button 
              type="submit" 
              class="w-full bg-amber-600 text-white rounded-md py-3 px-4 hover:bg-amber-700 transition-colors duration-300 font-medium"
              :disabled="loading || items.length === 0"
            >
              Підтвердити замовлення
            </button>
          </div>
        </form>
        </div>
      </div>
      
      <!-- Підсумок замовлення -->
      <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
          <h2 class="text-xl font-semibold mb-4">Ваше замовлення</h2>
          
          <div class="divide-y divide-gray-200">
            <div v-if="items.length === 0" class="py-4 text-gray-500 text-center">
              Кошик порожній
            </div>
            
            <div v-else v-for="item in items" :key="item.item_id" class="py-4 flex justify-between">
              <div>
                <p class="font-medium">{{ item.name }}</p>
                <p class="text-sm text-gray-500">Кількість: {{ item.qty }}</p>
              </div>
              <p class="font-medium">{{ formatPrice(item.row_total) }} грн</p>
            </div>
            
            <div class="py-4 flex justify-between">
              <p>Всього</p>
              <p class="font-bold">{{ formatPrice(subtotal) }} грн</p>
            </div>
          </div>
          
          <button 
            @click="processCheckout"
            class="mt-6 w-full bg-amber-600 text-white rounded-md py-3 px-4 hover:bg-amber-700 transition-colors duration-300 font-medium hidden lg:block"
            :disabled="loading || items.length === 0"
          >
            Підтвердити замовлення
          </button>
          
          <p class="mt-4 text-xs text-gray-500 text-center">
            Натискаючи на кнопку, ви погоджуєтеся з нашими <NuxtLink to="/terms" class="text-amber-600 hover:underline">Умовами користування</NuxtLink>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useCart } from '~/composables/useCart';
import { useApi } from '~/composables/useApi';

const { items, subtotal, cartId, formatPrice, loadCart } = useCart();

// Статус оформлення замовлення
const loading = ref(false);
const orderCreated = ref(false);
const orderId = ref('');
const errorMessage = ref('');

// Форма даних
const form = ref({
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
  address: '',
  city: '',
  region: '',
  postcode: '',
  notes: '',
  paymentMethod: 'cash'
});

// Помилки валідації
const errors = ref({
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
  address: '',
  city: '',
  region: '',
  postcode: '',
  paymentMethod: ''
});

// Перевірка авторизації та заповнення форми даними користувача
const checkAuthAndFillForm = () => {
  // Перевіряємо чи користувач авторизований
  const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
  
  if (isLoggedIn) {
    const customerData = localStorage.getItem('customer');
    if (customerData) {
      try {
        const customer = JSON.parse(customerData);
        
        // Заповнюємо форму даними авторизованого користувача
        form.value.firstName = customer.first_name || '';
        form.value.lastName = customer.last_name || '';
        form.value.email = customer.email || '';
        
        // Якщо у користувача є адреса, заповнюємо і її
        if (customer.default_address) {
          form.value.phone = customer.default_address.telephone || '';
          form.value.address = customer.default_address.street || '';
          form.value.city = customer.default_address.city || '';
          form.value.region = customer.default_address.region || '';
          form.value.postcode = customer.default_address.postcode || '';
        }
      } catch (e) {
        console.error('Error parsing customer data:', e);
      }
    }
  }
};

// Очищення помилок валідації
const clearErrors = () => {
  for (const key in errors.value) {
    errors.value[key as keyof typeof errors.value] = '';
  }
  errorMessage.value = '';
};

// Валідація форми
const validateForm = () => {
  let isValid = true;
  clearErrors();
  
  if (!form.value.firstName.trim()) {
    errors.value.firstName = "Ім'я обов'язкове";
    isValid = false;
  }
  
  if (!form.value.lastName.trim()) {
    errors.value.lastName = "Прізвище обов'язкове";
    isValid = false;
  }
  
  if (!form.value.email.trim()) {
    errors.value.email = "Email обов'язковий";
    isValid = false;
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email)) {
    errors.value.email = "Некоректний формат Email";
    isValid = false;
  }
  
  if (!form.value.phone.trim()) {
    errors.value.phone = "Телефон обов'язковий";
    isValid = false;
  }
  
  if (!form.value.address.trim()) {
    errors.value.address = "Адреса обов'язкова";
    isValid = false;
  }
  
  if (!form.value.city.trim()) {
    errors.value.city = "Місто обов'язкове";
    isValid = false;
  }
  
  if (!form.value.region.trim()) {
    errors.value.region = "Область обов'язкова";
    isValid = false;
  }
  
  if (!form.value.postcode.trim()) {
    errors.value.postcode = "Поштовий індекс обов'язковий";
    isValid = false;
  }
  
  if (!form.value.paymentMethod) {
    errors.value.paymentMethod = "Оберіть спосіб оплати";
    isValid = false;
  }
  
  return isValid;
};

// Обробка оформлення замовлення
const processCheckout = async () => {
  if (items.value.length === 0) {
    errorMessage.value = 'Ваш кошик порожній. Додайте товари перед оформленням замовлення.';
    return;
  }
  
  if (!validateForm()) {
    return;
  }
  
  loading.value = true;
  
  try {
    const { post } = useApi();
    
    const response = await post('/orders', {
      first_name: form.value.firstName,
      last_name: form.value.lastName,
      email: form.value.email,
      phone: form.value.phone,
      address: form.value.address,
      city: form.value.city,
      region: form.value.region,
      postcode: form.value.postcode,
      notes: form.value.notes,
      payment_method: form.value.paymentMethod,
      cart_id: String(cartId.value)
    });
    
    if (response.success) {
      // Замовлення успішно створено
      orderCreated.value = true;
      orderId.value = response.data.data.increment_id;
      
      // Оновлюємо кошик
      await loadCart();
    } else {
      // Помилка створення замовлення
      if (response.status === 401) {
        errorMessage.value = 'Сервер повернув помилку авторизації. Це може бути тимчасова проблема, будь ласка, спробуйте знову.';
      } else {
        errorMessage.value = response.data.message || 'Виникла помилка при оформленні замовлення. Спробуйте пізніше.';
      }
      
      // Якщо є помилки валідації, показуємо їх
      if (response.data.errors) {
        const validationErrors = response.data.errors;
        
        Object.keys(validationErrors).forEach(field => {
          // Маппінг полів з бекенду до фронтенду
          const fieldMap: Record<string, keyof typeof errors.value> = {
            'first_name': 'firstName',
            'last_name': 'lastName',
            'payment_method': 'paymentMethod'
          };
          
          const frontendField = fieldMap[field] as keyof typeof errors.value || field as keyof typeof errors.value;
          if (frontendField in errors.value) {
            errors.value[frontendField] = validationErrors[field][0];
          }
        });
      }
    }
  } catch (error: any) {
    console.error('Checkout error:', error);
    if (error.response && error.response.status === 401) {
      errorMessage.value = 'Сервер повернув помилку авторизації. Це може бути тимчасова проблема, будь ласка, спробуйте знову.';
    } else {
      errorMessage.value = 'Сталася помилка при спробі оформити замовлення. Спробуйте пізніше.';
    }
  } finally {
    loading.value = false;
  }
};

// При монтуванні компонента
onMounted(() => {
  // Завантажуємо кошик
  loadCart();
  
  // Перевіряємо авторизацію та заповнюємо форму
  checkAuthAndFillForm();
});
</script> 
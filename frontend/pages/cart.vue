<template>
  <div class="bg-gray-50 py-8">
    <div class="container mx-auto px-4">
      <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">Кошик</h1>
      
      <!-- Пустий кошик -->
      <div v-if="!cartItems.length" class="bg-white rounded-lg shadow-sm p-8 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <h2 class="text-xl font-semibold text-gray-800 mb-2">Ваш кошик порожній</h2>
        <p class="text-gray-600 mb-6">Перегляньте наш каталог, щоб додати товари до кошика</p>
        <NuxtLink to="/catalog" class="inline-block bg-amber-600 hover:bg-amber-700 text-white py-3 px-8 rounded-md font-medium transition-colors duration-300">
          Перейти до каталогу
        </NuxtLink>
      </div>
      
      <!-- Кошик з товарами -->
      <div v-else>
        <div class="flex flex-col lg:flex-row gap-8">
          <!-- Список товарів у кошику -->
          <div class="lg:w-2/3">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-4">
              <!-- Заголовки таблиці (приховані на мобільних) -->
              <div class="hidden md:flex bg-gray-50 text-gray-600 text-sm font-medium p-4">
                <div class="w-8"></div>
                <div class="w-20"></div>
                <div class="flex-1">Товар</div>
                <div class="w-32 text-center">Ціна</div>
                <div class="w-28 text-center">Кількість</div>
                <div class="w-32 text-center">Всього</div>
                <div class="w-8"></div>
              </div>
              
              <!-- Товари -->
              <div v-for="(item, index) in cartItems" :key="item.id" class="border-t first:border-t-0">
                <div class="p-4 flex flex-wrap md:flex-nowrap items-center">
                  <!-- Кнопка видалення (на мобільних вгорі) -->
                  <button 
                    @click="removeFromCart(item.id)" 
                    class="md:hidden absolute top-4 right-4 text-gray-400 hover:text-red-500"
                    aria-label="Видалити товар"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                  
                  <!-- Чекбокс для товару -->
                  <div class="w-8 flex items-center justify-center">
                    <input 
                      type="checkbox" 
                      :id="`item-${item.id}`"
                      v-model="item.selected"
                      class="rounded text-amber-600 focus:ring-amber-500 h-4 w-4"
                    >
                  </div>
                  
                  <!-- Зображення -->
                  <div class="w-20 mr-4">
                    <div class="product-image-container">
                      <img 
                        :src="item.image || '/images/placeholder-product.jpg'" 
                        :alt="item.name" 
                        class="w-full h-full object-cover"
                      >
                    </div>
                  </div>
                  
                  <!-- Інформація про товар -->
                  <div class="flex-1 min-w-[200px] mb-4 md:mb-0">
                    <h3 class="text-gray-900 font-medium">{{ item.name }}</h3>
                    <div class="text-sm text-gray-500 mt-1">
                      <span v-if="item.variant">{{ item.variant }}</span>
                    </div>
                  </div>
                  
                  <!-- Ціна -->
                  <div class="w-full md:w-32 flex justify-between md:justify-center mb-4 md:mb-0">
                    <span class="md:hidden text-gray-500">Ціна:</span>
                    <span class="text-gray-900">{{ formatPrice(item.price) }} грн</span>
                  </div>
                  
                  <!-- Кількість -->
                  <div class="w-full md:w-28 flex justify-between md:justify-center items-center mb-4 md:mb-0">
                    <span class="md:hidden text-gray-500">Кількість:</span>
                    <div class="flex items-center border border-gray-300 rounded-md">
                      <button 
                        @click="decreaseQuantity(item)" 
                        class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 focus:outline-none"
                        :disabled="item.quantity <= 1"
                      >-</button>
                      <input 
                        type="number" 
                        v-model="item.quantity" 
                        min="1" 
                        class="w-12 px-2 py-1 text-center focus:outline-none border-x border-gray-300"
                        @change="updateCartItem(item)"
                      >
                      <button 
                        @click="increaseQuantity(item)" 
                        class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 focus:outline-none"
                      >+</button>
                    </div>
                  </div>
                  
                  <!-- Загальна сума -->
                  <div class="w-full md:w-32 flex justify-between md:justify-center font-medium mb-4 md:mb-0">
                    <span class="md:hidden text-gray-500">Всього:</span>
                    <span class="text-gray-900">{{ formatPrice(item.price * item.quantity) }} грн</span>
                  </div>
                  
                  <!-- Кнопка видалення (на десктопі) -->
                  <div class="hidden md:block w-8 text-center">
                    <button 
                      @click="removeFromCart(item.id)" 
                      class="text-gray-400 hover:text-red-500"
                      aria-label="Видалити товар"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Дії з кошиком -->
            <div class="flex flex-wrap md:flex-nowrap justify-between gap-4 mb-6">
              <NuxtLink 
                to="/catalog" 
                class="inline-flex items-center text-amber-600 hover:text-amber-800 font-medium transition-colors duration-300"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Продовжити покупки
              </NuxtLink>
              
              <div class="flex gap-3">
                <button 
                  @click="clearCart" 
                  class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors duration-300"
                >
                  Очистити кошик
                </button>
                <button 
                  @click="updateCart" 
                  class="px-4 py-2 bg-amber-600 text-white rounded-md hover:bg-amber-700 transition-colors duration-300"
                >
                  Оновити кошик
                </button>
              </div>
            </div>
          </div>
          
          <!-- Підсумок замовлення -->
          <div class="lg:w-1/3">
            <div class="bg-white rounded-lg shadow-sm p-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-4">Підсумок замовлення</h2>
              
              <div class="space-y-4 mb-6">
                <div class="flex justify-between">
                  <span class="text-gray-600">Сума товарів:</span>
                  <span class="text-gray-900 font-medium">{{ formatPrice(subtotal) }} грн</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Знижка:</span>
                  <span class="text-red-600 font-medium">-{{ formatPrice(discount) }} грн</span>
                </div>
                <div class="flex justify-between border-t pt-4">
                  <span class="text-gray-900 font-bold">Разом до сплати:</span>
                  <span class="text-gray-900 font-bold">{{ formatPrice(total) }} грн</span>
                </div>
              </div>
              
              <!-- Промокод -->
              <div class="mb-6">
                <label for="promo-code" class="block text-gray-700 font-medium mb-2">Промокод:</label>
                <div class="flex">
                  <input 
                    type="text" 
                    id="promo-code" 
                    v-model="promoCode"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-l-md focus:ring-amber-500 focus:border-amber-500"
                    placeholder="Введіть промокод"
                  >
                  <button 
                    @click="applyPromoCode" 
                    class="px-4 py-2 bg-gray-800 text-white rounded-r-md hover:bg-gray-900 transition-colors duration-300"
                  >
                    Застосувати
                  </button>
                </div>
              </div>
              
              <!-- Кнопка оформлення замовлення -->
              <button 
                @click="checkout" 
                class="w-full py-3 px-6 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-md shadow-sm transition-colors duration-300"
              >
                Оформити замовлення
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
import { computed, onMounted } from 'vue';

// Інтерфейси
interface CartItem {
  id: number;
  name: string;
  slug: string;
  price: number;
  image?: string;
  quantity: number;
  variant?: string;
  selected: boolean;
}

// Стан компонента
const cartItems = ref<CartItem[]>([]);
const promoCode = ref('');
const promoDiscount = ref(0);
const loading = ref(false);

// Підрахунок суми
const subtotal = computed(() => {
  return cartItems.value
    .filter(item => item.selected)
    .reduce((sum, item) => sum + (item.price * item.quantity), 0);
});

const discount = computed(() => {
  return promoDiscount.value;
});

const total = computed(() => {
  return subtotal.value - discount.value;
});

// Методи
const formatPrice = (price: number): string => {
  return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
};

const loadCart = () => {
  // В реальному проекті тут буде завантаження даних з API або localStorage
  // Для прикладу, використовуємо тестові дані
  cartItems.value = [
    {
      id: 1,
      name: 'Мигдаль обсмажений',
      slug: 'mygdal-obsmazhenyy',
      price: 250,
      image: '/images/products/almonds.jpg',
      quantity: 2,
      variant: '500 г',
      selected: true
    },
    {
      id: 2,
      name: 'Фісташки солоні',
      slug: 'fistashky-soloni',
      price: 320,
      image: '/images/products/pistachios.jpg',
      quantity: 1,
      variant: '250 г',
      selected: true
    },
    {
      id: 3,
      name: 'Кеш\'ю сирий',
      slug: 'keshyu-syryy',
      price: 280,
      image: '/images/products/cashew.jpg',
      quantity: 1,
      variant: '250 г',
      selected: true
    }
  ];
};

const increaseQuantity = (item: CartItem) => {
  item.quantity++;
  updateCartItem(item);
};

const decreaseQuantity = (item: CartItem) => {
  if (item.quantity > 1) {
    item.quantity--;
    updateCartItem(item);
  }
};

const updateCartItem = (item: CartItem) => {
  // В реальному проекті тут буде оновлення через API або localStorage
  console.log('Оновлено товар:', item);
};

const removeFromCart = (itemId: number) => {
  if (confirm('Ви впевнені, що хочете видалити цей товар з кошика?')) {
    cartItems.value = cartItems.value.filter(item => item.id !== itemId);
    // В реальному проекті тут буде видалення через API або localStorage
    console.log('Видалено товар з ID:', itemId);
  }
};

const clearCart = () => {
  if (confirm('Ви впевнені, що хочете очистити кошик?')) {
    cartItems.value = [];
    // В реальному проекті тут буде очищення через API або localStorage
    console.log('Кошик очищено');
  }
};

const updateCart = () => {
  loading.value = true;
  
  // Імітація запиту до сервера
  setTimeout(() => {
    // В реальному проекті тут буде оновлення через API
    console.log('Кошик оновлено');
    loading.value = false;
    alert('Кошик успішно оновлено');
  }, 500);
};

const applyPromoCode = () => {
  if (!promoCode.value.trim()) {
    alert('Будь ласка, введіть промокод');
    return;
  }
  
  loading.value = true;
  
  // Імітація запиту до сервера
  setTimeout(() => {
    // В реальному проекті тут буде перевірка промокоду через API
    if (promoCode.value.toUpperCase() === 'NUTS20') {
      promoDiscount.value = Math.round(subtotal.value * 0.2);
      alert('Промокод успішно застосовано! Знижка 20%');
    } else {
      promoDiscount.value = 0;
      alert('Недійсний промокод');
    }
    loading.value = false;
  }, 500);
};

const checkout = () => {
  if (cartItems.value.length === 0) {
    alert('Ваш кошик порожній');
    return;
  }
  
  if (cartItems.value.filter(item => item.selected).length === 0) {
    alert('Будь ласка, виберіть товари для замовлення');
    return;
  }
  
  // В реальному проекті тут буде перехід на сторінку оформлення замовлення
  console.log('Перехід до оформлення замовлення');
  // Використовуйте router.push або window.location для переходу на сторінку оформлення замовлення
};

// Ініціалізація
onMounted(() => {
  loadCart();
});
</script>

<style scoped>
.product-image-container {
  position: relative;
  width: 80px;
  height: 80px;
  overflow: hidden;
  border-radius: 0.25rem;
}
</style> 
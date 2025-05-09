<template>
  <div class="bg-gray-50 py-8">
    <div class="container mx-auto px-4">
      <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">Кошик</h1>
      
      <!-- Індикатор завантаження -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-amber-600"></div>
        <p class="mt-4 text-gray-600">Завантаження кошика...</p>
      </div>
      
      <!-- Пустий кошик -->
      <div v-else-if="!items.length" class="bg-white rounded-lg shadow-sm p-8 text-center">
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
      <div v-else class="flex flex-col lg:flex-row gap-8">
        <!-- Список товарів (ліворуч на десктопі) -->
        <div class="lg:w-2/3">
          <div class="bg-white rounded-lg shadow-sm">
            <!-- Заголовок кошика на десктопі -->
            <div class="hidden md:flex py-4 px-6 border-b text-sm text-gray-600 font-medium">
              <div class="w-8"></div>
              <div class="flex-1 min-w-[200px]">Товар</div>
              <div class="w-32 text-center">Ціна</div>
              <div class="w-28 text-center">Кількість</div>
              <div class="w-32 text-center">Всього</div>
              <div class="w-8"></div>
            </div>
            
            <!-- Список товарів -->
            <div>
              <div v-for="item in items" :key="item.item_id" class="border-t first:border-t-0">
                <div class="p-4 flex flex-wrap md:flex-nowrap items-center relative">
                  <!-- Кнопка видалення (на мобільних справа) -->
                  <button 
                    @click="handleRemoveItem(item.item_id)" 
                    class="md:hidden absolute top-4 right-4 text-gray-400 hover:text-red-500"
                    aria-label="Видалити товар"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                  
                  <!-- Чекбокс для товару (замінено на відступ) -->
                  <div class="w-8 flex items-center justify-center">
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
                  <div class="flex-1 min-w-[200px] mb-4 md:mb-0 pr-8 md:pr-0">
                    <h3 class="text-gray-900 font-medium">{{ item.name }}</h3>
                    <div class="text-sm text-gray-500 mt-1">
                      <span>{{ item.sku }}</span>
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
                        :disabled="item.qty <= 1"
                      >-</button>
                      <input 
                        type="number" 
                        v-model="item.qty" 
                        min="1" 
                        class="w-12 px-2 py-1 text-center focus:outline-none border-x border-gray-300"
                        @change="handleUpdateItem(item)"
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
                    <span class="text-gray-900">{{ formatPrice(item.row_total) }} грн</span>
                  </div>
                  
                  <!-- Кнопка видалення (на десктопі) -->
                  <div class="hidden md:block w-8 text-center">
                    <button 
                      @click="handleRemoveItem(item.item_id)" 
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
            <div class="flex flex-wrap md:flex-nowrap justify-between gap-4 p-6 border-t">
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
                  @click="handleClearCart" 
                  class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors duration-300"
                >
                  Очистити кошик
                </button>
                <button 
                  @click="loadCart" 
                  class="px-4 py-2 bg-amber-600 text-white rounded-md hover:bg-amber-700 transition-colors duration-300"
                >
                  Оновити кошик
                </button>
              </div>
            </div>
          </div>
        </div>
          
        <!-- Підсумок замовлення (праворуч на десктопі) -->
        <div class="lg:w-1/3">
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Підсумок замовлення</h2>
            
            <div class="space-y-4 mb-6">
              <div class="flex justify-between">
                <span class="text-gray-600">Сума товарів:</span>
                <span class="text-gray-900 font-medium">{{ formatPrice(subtotal) }} грн</span>
              </div>
              <div class="flex justify-between border-t pt-4">
                <span class="text-gray-900 font-bold">Разом до сплати:</span>
                <span class="text-gray-900 font-bold">{{ formatPrice(subtotal) }} грн</span>
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
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useCart } from '~/composables/useCart';

// Отримуємо дані та методи кошика з composable
const { 
  items, 
  loading, 
  subtotal, 
  grandTotal, 
  formatPrice, 
  loadCart, 
  updateCartItem, 
  removeCartItem, 
  clearCart 
} = useCart();

// Методи управління елементами кошика
const increaseQuantity = async (item: any) => {
  item.qty++;
  await handleUpdateItem(item);
};

const decreaseQuantity = async (item: any) => {
  if (item.qty > 1) {
    item.qty--;
    await handleUpdateItem(item);
  }
};

const handleUpdateItem = async (item: any) => {
  await updateCartItem(item.item_id, item.qty);
};

const handleRemoveItem = async (itemId: number) => {
  if (confirm('Ви впевнені, що хочете видалити цей товар з кошика?')) {
    await removeCartItem(itemId);
  }
};

const handleClearCart = async () => {
  if (confirm('Ви впевнені, що хочете очистити кошик?')) {
    await clearCart();
  }
};

const checkout = () => {
  // Перенаправлення на сторінку оформлення замовлення
  window.location.href = '/checkout';
};

// При монтуванні компонента оновлюємо кошик
onMounted(() => {
  loadCart();
});
</script>

<style scoped>
.product-image-container {
  width: 100%;
  aspect-ratio: 1/1;
  overflow: hidden;
  border-radius: 0.375rem;
}

/* Стилі для контейнера товару */
.border-t {
  position: relative;
}

/* Стилі для мобільної версії */
@media (max-width: 768px) {
  .border-t {
    padding-top: 0.5rem;
  }
}
</style> 
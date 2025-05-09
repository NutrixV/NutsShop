<template>
  <div class="mini-cart relative">
    <!-- Кнопка кошика -->
    <button 
      @click="toggleCart" 
      class="flex items-center p-2 relative"
      aria-label="Кошик"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
      </svg>
      <span 
        v-if="localItemsCount > 0" 
        class="absolute top-0 right-0 bg-amber-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs"
      >
        {{ localItemsCount }}
      </span>
    </button>
    
    <!-- Випадаючий міні-кошик -->
    <div 
      v-if="isOpen" 
      ref="cartDropdown"
      class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg z-50 p-4"
    >
      <!-- Заголовок міні-кошика -->
      <div class="flex justify-between items-center mb-3">
        <h3 class="text-lg font-medium text-gray-900">Кошик</h3>
        <button @click="isOpen = false" class="text-gray-400 hover:text-gray-500">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      
      <!-- Порожній кошик -->
      <div v-if="items.length === 0" class="text-center py-6">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <p class="text-gray-500">Ваш кошик порожній</p>
      </div>
      
      <!-- Товари в кошику -->
      <div v-else>
        <div class="max-h-60 overflow-y-auto">
          <div 
            v-for="item in items" 
            :key="item.item_id" 
            class="flex items-start py-3 border-b last:border-b-0"
          >
            <!-- Зображення товару -->
            <div class="w-16 h-16 flex-shrink-0 rounded-md overflow-hidden">
              <img 
                :src="item.image || '/images/placeholder-product.jpg'" 
                :alt="item.name" 
                class="w-full h-full object-cover"
              >
            </div>
            
            <!-- Інформація про товар -->
            <div class="ml-3 flex-1">
              <h4 class="text-gray-900 font-medium text-sm">{{ item.name }}</h4>
              <div class="flex justify-between items-center mt-1">
                <div class="text-gray-500 text-sm">
                  {{ item.qty }} x {{ formatPrice(item.price) }} грн
                </div>
                <button 
                  @click="removeItem(item.item_id)" 
                  class="text-gray-400 hover:text-red-500"
                  aria-label="Видалити товар"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Підсумок міні-кошика -->
        <div class="pt-3 border-t mt-3">
          <div class="flex justify-between mb-3">
            <span class="text-gray-600">Всього:</span>
            <span class="text-gray-900 font-medium">{{ formatPrice(subtotal) }} грн</span>
          </div>
          
          <!-- Кнопки міні-кошика -->
          <div class="grid grid-cols-2 gap-2">
            <NuxtLink 
              to="/cart" 
              class="bg-gray-100 text-gray-800 rounded-md py-2 px-4 text-center text-sm hover:bg-gray-200 transition-colors duration-300"
              @click="isOpen = false"
            >
              Переглянути кошик
            </NuxtLink>
            <NuxtLink 
              to="/checkout" 
              class="bg-amber-600 text-white rounded-md py-2 px-4 text-center text-sm hover:bg-amber-700 transition-colors duration-300"
              @click="isOpen = false"
            >
              Оформити замовлення
            </NuxtLink>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { useCart, cartUpdateCounter, cartEventBus } from '~/composables/useCart';

// Стан компонента
const isOpen = ref(false);
const cartDropdown = ref<HTMLElement | null>(null);
const localItemsCount = ref(0);

// Отримуємо дані та методи кошика з composable
const { items, itemsCount, subtotal, formatPrice, removeCartItem, loadCart, cart } = useCart();

// Функція примусового оновлення лічильника
const updateLocalCount = () => {
  if (cart.value) {
    // Оновлюємо лічильник, тільки якщо значення змінилося
    if (localItemsCount.value !== cart.value.items_count) {
      localItemsCount.value = cart.value.items_count;
    }
  }
};

// Оновлюємо локальний лічильник при зміні лічильника оновлень кошика
watch(cartUpdateCounter, () => {
  // Викликаємо оновлення відразу
  updateLocalCount();
}, { immediate: true });

// Методи
const toggleCart = () => {
  isOpen.value = !isOpen.value;
  
  if (isOpen.value) {
    loadCart().then(() => {
      updateLocalCount();
      // Додаємо слухач подій після оновлення кошика
      setTimeout(() => {
        document.addEventListener('click', handleClickOutside);
      }, 0);
    });
  }
};

const handleClickOutside = (event: MouseEvent) => {
  const target = event.target as HTMLElement;
  if (cartDropdown.value && !(cartDropdown.value === target || cartDropdown.value.contains(target))) {
    // Якщо клік був поза випадаючим меню
    isOpen.value = false;
    document.removeEventListener('click', handleClickOutside);
  }
};

const removeItem = async (itemId: number) => {
  await removeCartItem(itemId);
  updateLocalCount();
};

// Глобальні обробники подій для очищення
let cartInterval: number | null = null;
let countUpdateInterval: number | null = null;

// Оновлюємо дані кошика при монтуванні та при відкритті мінікарти
onMounted(() => {
  // Завантажуємо кошик при монтуванні
  loadCart().then(() => {
    updateLocalCount();
  });
  
  // Оновлюємо кошик кожні 30 секунд
  cartInterval = window.setInterval(() => {
    loadCart().then(() => {
      updateLocalCount();
    });
  }, 30000);
  
  // Додатково перевіряємо лічильник частіше
  countUpdateInterval = window.setInterval(() => {
    updateLocalCount();
  }, 500);
  
  // Очищення при розмонтуванні через додавання обробника на вікно
  window.addEventListener('beforeunload', () => {
    if (cartInterval) {
      window.clearInterval(cartInterval);
    }
    if (countUpdateInterval) {
      window.clearInterval(countUpdateInterval);
    }
    document.removeEventListener('click', handleClickOutside);
  });
});

// Спостерігаємо за відкриттям кошика, оновлюємо дані при відкритті
watch(isOpen, (newVal) => {
  if (newVal) {
    loadCart().then(() => {
      updateLocalCount();
    });
  } else {
    document.removeEventListener('click', handleClickOutside);
  }
});

// Спостерігаємо за подією додавання товару до кошика
watch(() => cartEventBus.itemAdded.value, () => {
  // Якщо товар додано і кошик не відкритий - відкриваємо його
  if (!isOpen.value) {
    // Відкриваємо кошик
    isOpen.value = true;
    
    // Оновлюємо дані кошика
    loadCart().then(() => {
      updateLocalCount();
      // Додаємо слухач подій після оновлення кошика
      setTimeout(() => {
        document.addEventListener('click', handleClickOutside);
      }, 0);
    });
  }
});
</script>

<style scoped>
.mini-cart {
  position: relative;
}
</style> 
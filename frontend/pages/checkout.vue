<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Оформлення замовлення</h1>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Форма оформлення замовлення -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
          <h2 class="text-xl font-semibold mb-4">Інформація для доставки</h2>
          
          <form>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <div>
                <label for="firstName" class="block text-sm font-medium text-gray-700 mb-1">Ім'я</label>
                <input 
                  type="text" 
                  id="firstName" 
                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500"
                />
              </div>
              
              <div>
                <label for="lastName" class="block text-sm font-medium text-gray-700 mb-1">Прізвище</label>
                <input 
                  type="text" 
                  id="lastName" 
                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500"
                />
              </div>
            </div>
            
            <div class="mb-4">
              <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input 
                type="email" 
                id="email" 
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500"
              />
            </div>
            
            <div class="mb-4">
              <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Телефон</label>
              <input 
                type="tel" 
                id="phone" 
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500"
              />
            </div>
            
            <div class="mb-4">
              <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Адреса</label>
              <input 
                type="text" 
                id="address" 
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500"
              />
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
              <div>
                <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Місто</label>
                <input 
                  type="text" 
                  id="city" 
                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500"
                />
              </div>
              
              <div>
                <label for="region" class="block text-sm font-medium text-gray-700 mb-1">Область</label>
                <input 
                  type="text" 
                  id="region" 
                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500"
                />
              </div>
              
              <div>
                <label for="postcode" class="block text-sm font-medium text-gray-700 mb-1">Поштовий індекс</label>
                <input 
                  type="text" 
                  id="postcode" 
                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500"
                />
              </div>
            </div>
            
            <div class="mb-4">
              <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Примітки до замовлення</label>
              <textarea 
                id="notes" 
                rows="3" 
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500"
              ></textarea>
            </div>
          </form>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-semibold mb-4">Спосіб оплати</h2>
          <div class="space-y-4">
            <div class="flex items-center">
              <input type="radio" id="payment1" name="payment" value="cash" class="h-4 w-4 text-amber-600 focus:ring-amber-500" checked>
              <label for="payment1" class="ml-3">Післяплата</label>
            </div>
            
            <div class="flex items-center">
              <input type="radio" id="payment2" name="payment" value="card" class="h-4 w-4 text-amber-600 focus:ring-amber-500">
              <label for="payment2" class="ml-3">Оплата карткою (онлайн)</label>
            </div>
          </div>
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
            class="mt-6 w-full bg-amber-600 text-white rounded-md py-3 px-4 hover:bg-amber-700 transition-colors duration-300 font-medium"
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
import { useCart } from '~/composables/useCart';

const { items, subtotal, formatPrice } = useCart();
</script> 
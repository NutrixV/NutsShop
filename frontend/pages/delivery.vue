<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Доставка та оплата</h1>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
      <h2 class="text-2xl font-semibold mb-4">Способи доставки</h2>
      
      <div class="mb-6 border-b pb-6">
        <h3 class="text-xl font-medium mb-3 flex items-center">
          <span class="bg-amber-500 text-white p-2 rounded-full mr-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
              <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7h4a1 1 0 011 1v6.05A2.5 2.5 0 0116.95 16H16a1 1 0 01-1-1v-8z" />
            </svg>
          </span>
          Кур'єрська доставка
        </h3>
        <p class="mb-2">Доставка здійснюється протягом 1-3 робочих днів по всій Україні.</p>
        <p class="text-lg font-medium">Вартість: 100 грн</p>
        <p class="text-sm text-gray-600">Безкоштовна доставка при замовленні від 1000 грн</p>
      </div>
      
      <div class="mb-6 border-b pb-6">
        <h3 class="text-xl font-medium mb-3 flex items-center">
          <span class="bg-amber-500 text-white p-2 rounded-full mr-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
            </svg>
          </span>
          Нова Пошта
        </h3>
        <p class="mb-2">Доставка у відділення або поштомат "Нова Пошта" по всій Україні.</p>
        <p class="text-lg font-medium">Вартість: згідно тарифів "Нова Пошта"</p>
        <p class="text-sm text-gray-600">Орієнтовний термін доставки: 1-2 робочих дні</p>
      </div>
      
      <div class="mb-6">
        <h3 class="text-xl font-medium mb-3 flex items-center">
          <span class="bg-amber-500 text-white p-2 rounded-full mr-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
            </svg>
          </span>
          Самовивіз
        </h3>
        <p class="mb-2">Ви можете забрати своє замовлення безкоштовно з нашого магазину в місті Київ.</p>
        <p class="text-lg font-medium">Адреса: м. Київ, вул. Хрещатик, 10</p>
        <p class="text-sm text-gray-600">Графік роботи: Пн-Пт: 9:00-20:00, Сб-Нд: 10:00-18:00</p>
      </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
      <h2 class="text-2xl font-semibold mb-4">Способи оплати</h2>
      
      <div class="mb-6 border-b pb-6">
        <h3 class="text-xl font-medium mb-3 flex items-center">
          <span class="bg-amber-500 text-white p-2 rounded-full mr-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
              <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd" />
            </svg>
          </span>
          Оплата карткою онлайн
        </h3>
        <p>Безпечна оплата через захищений платіжний шлюз. Підтримуються картки Visa та MasterCard.</p>
      </div>
      
      <div class="mb-6 border-b pb-6">
        <h3 class="text-xl font-medium mb-3 flex items-center">
          <span class="bg-amber-500 text-white p-2 rounded-full mr-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
            </svg>
          </span>
          Оплата при отриманні
        </h3>
        <p>Ви можете оплатити замовлення готівкою або карткою при отриманні.</p>
      </div>
      
      <div>
        <h3 class="text-xl font-medium mb-3 flex items-center">
          <span class="bg-amber-500 text-white p-2 rounded-full mr-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5 5a3 3 0 015-2.236A3 3 0 0114.83 6H16a2 2 0 110 4h-5V9a1 1 0 10-2 0v1H4a2 2 0 110-4h1.17C5.06 5.687 5 5.35 5 5zm4 1V5a1 1 0 10-1 1h1zm3 0a1 1 0 10-1-1v1h1z" clip-rule="evenodd" />
              <path d="M9 11H3v5a2 2 0 002 2h4v-7zM11 18h4a2 2 0 002-2v-5h-6v7z" />
            </svg>
          </span>
          Безготівковий розрахунок
        </h3>
        <p>Для юридичних осіб доступна оплата за безготівковим розрахунком. Рахунок-фактуру буде надіслано на вашу електронну пошту.</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Page title and meta would be set in the layout or parent component
</script> 
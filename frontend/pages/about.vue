<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Про нас</h1>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
      <h2 class="text-2xl font-semibold mb-4">Наша історія</h2>
      <p class="mb-4">
        Ласкаво просимо до NutsShop - вашого надійного джерела найякісніших горіхів та солодощів. 
        Наша компанія була заснована з пристрасті до здорового та смачного харчування.
      </p>
      <p class="mb-4">
        Починаючи з невеликого сімейного бізнесу у 2015 році, ми виросли в провідного постачальника 
        преміальних горіхів, сухофруктів та натуральних солодощів в Україні.
      </p>
      
      <h2 class="text-2xl font-semibold mb-4 mt-8">Наша місія</h2>
      <p class="mb-4">
        Ми прагнемо забезпечити наших клієнтів найсвіжішими та найякіснішими продуктами, 
        дотримуючись принципів сталого розвитку та екологічної відповідальності.
      </p>
      
      <h2 class="text-2xl font-semibold mb-4 mt-8">Чому обирають нас</h2>
      <ul class="list-disc pl-5 mb-4">
        <li class="mb-2">Висока якість продукції від перевірених постачальників</li>
        <li class="mb-2">Широкий асортимент горіхів, сухофруктів та натуральних солодощів</li>
        <li class="mb-2">Ретельний контроль якості на всіх етапах виробництва</li>
        <li class="mb-2">Швидка доставка по всій Україні</li>
        <li class="mb-2">Професійна підтримка клієнтів</li>
      </ul>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
      <h2 class="text-2xl font-semibold mb-4">Наша команда</h2>
      <p class="mb-4">
        За нашим успіхом стоїть команда відданих професіоналів, які поділяють нашу пристрасть 
        до здорового харчування та якісного обслуговування клієнтів.
      </p>
      
      <!-- Team members section can be expanded later -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <div class="text-center">
          <div class="w-32 h-32 bg-gray-200 rounded-full mx-auto mb-4"></div>
          <h3 class="font-semibold">Іван Петренко</h3>
          <p class="text-gray-600">Засновник і CEO</p>
        </div>
        <div class="text-center">
          <div class="w-32 h-32 bg-gray-200 rounded-full mx-auto mb-4"></div>
          <h3 class="font-semibold">Олена Коваленко</h3>
          <p class="text-gray-600">Директор з продажів</p>
        </div>
        <div class="text-center">
          <div class="w-32 h-32 bg-gray-200 rounded-full mx-auto mb-4"></div>
          <h3 class="font-semibold">Максим Шевченко</h3>
          <p class="text-gray-600">Головний технолог</p>
        </div>
      </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6 mt-8">
      <h2 class="text-2xl font-semibold mb-4">Наші контакти</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="flex items-start">
          <span class="bg-amber-500 text-white p-2 rounded-full mr-3 mt-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
            </svg>
          </span>
          <div>
            <h3 class="text-lg font-medium">Адреса</h3>
            <p>вул. Горіхова, 123</p>
            <p>м. Київ, 01001</p>
          </div>
        </div>
        
        <div class="flex items-start">
          <span class="bg-amber-500 text-white p-2 rounded-full mr-3 mt-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
            </svg>
          </span>
          <div>
            <h3 class="text-lg font-medium">Телефон</h3>
            <p>0 800 123 456</p>
          </div>
        </div>
        
        <div class="flex items-start">
          <span class="bg-amber-500 text-white p-2 rounded-full mr-3 mt-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
              <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
            </svg>
          </span>
          <div>
            <h3 class="text-lg font-medium">Email</h3>
            <p>info@nutsshop.com</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Page title and meta would be set in the layout or parent component
</script> 
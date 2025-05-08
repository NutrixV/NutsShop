<template>
  <div class="bg-gray-50 min-h-screen py-12">
    <div class="container mx-auto px-4">
      <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <div class="py-4 px-6 bg-amber-600">
          <h1 class="text-2xl font-bold text-white text-center">Вхід до акаунту</h1>
        </div>
        
        <div class="py-8 px-6">
          <form class="space-y-6" @submit.prevent="handleLogin">
            <!-- Email -->
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
              <div class="mt-1">
                <input 
                  id="email" 
                  v-model="email" 
                  type="email" 
                  required 
                  autocomplete="email" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                  placeholder="Введіть email"
                >
              </div>
            </div>
            
            <!-- Пароль -->
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700">Пароль</label>
              <div class="mt-1">
                <input 
                  id="password" 
                  v-model="password" 
                  type="password" 
                  required 
                  autocomplete="current-password" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                  placeholder="Введіть пароль"
                >
              </div>
            </div>
            
            <!-- Запам'ятати мене -->
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <input 
                  id="remember-me" 
                  v-model="rememberMe" 
                  type="checkbox" 
                  class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded"
                >
                <label for="remember-me" class="ml-2 block text-sm text-gray-700">Запам'ятати мене</label>
              </div>
              <div class="text-sm">
                <a href="#" class="font-medium text-amber-600 hover:text-amber-500">Забули пароль?</a>
              </div>
            </div>
            
            <!-- Повідомлення про помилку -->
            <div v-if="errorMessage" class="text-red-600 text-sm text-center">
              {{ errorMessage }}
            </div>
            
            <!-- Кнопка входу -->
            <div>
              <button 
                type="submit" 
                class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500"
                :disabled="loading"
              >
                <span v-if="loading">
                  <svg class="animate-spin h-5 w-5 text-white inline mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Обробка...
                </span>
                <span v-else>Увійти</span>
              </button>
            </div>
          </form>
          
          <!-- Розділювач -->
          <div class="mt-6">
            <div class="relative">
              <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
              </div>
              <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white text-gray-500">Або</span>
              </div>
            </div>
          </div>
          
          <!-- Соціальні мережі -->
          <div class="mt-6 grid grid-cols-2 gap-3">
            <div>
              <a href="#" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 0C4.477 0 0 4.477 0 10c0 4.42 2.865 8.166 6.839 9.489.5.092.682-.217.682-.482 0-.237-.008-.866-.013-1.7-2.782.603-3.369-1.342-3.369-1.342-.454-1.155-1.11-1.462-1.11-1.462-.908-.62.069-.608.069-.608 1.003.07 1.531 1.03 1.531 1.03.891 1.529 2.341 1.087 2.91.833.092-.647.349-1.086.635-1.337-2.22-.253-4.555-1.11-4.555-4.943 0-1.091.39-1.984 1.029-2.683-.103-.253-.446-1.27.098-2.647 0 0 .84-.269 2.75 1.025A9.578 9.578 0 0110 2.836c.85.004 1.705.114 2.504.336 1.909-1.294 2.747-1.025 2.747-1.025.546 1.377.203 2.394.1 2.647.64.699 1.028 1.592 1.028 2.683 0 3.842-2.339 4.687-4.566 4.933.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .267.18.578.688.48C17.14 18.163 20 14.42 20 10c0-5.523-4.477-10-10-10z" clip-rule="evenodd" />
                </svg>
              </a>
            </div>
            <div>
              <a href="#" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                <svg class="h-5 w-5 text-[#4267B2]" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M20 10c0-5.523-4.477-10-10-10S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10z" clip-rule="evenodd" />
                </svg>
              </a>
            </div>
          </div>
          
          <!-- Реєстрація нового акаунту -->
          <div class="mt-6 text-center">
            <p class="text-sm text-gray-700">
              Немає акаунту?
              <NuxtLink to="/auth/register" class="font-medium text-amber-600 hover:text-amber-500">
                Зареєструватися
              </NuxtLink>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

// Форма даних
const email = ref('');
const password = ref('');
const rememberMe = ref(false);
const loading = ref(false);
const errorMessage = ref('');

// Обробник входу
const handleLogin = async () => {
  loading.value = true;
  errorMessage.value = '';
  
  try {
    // Здесь будет реализация API для входа
    // Имитация задержки API запроса
    await new Promise(resolve => setTimeout(resolve, 1500));
    
    // Для тестирования можно использовать проверку с определенным email и паролем
    if (email.value === 'test@example.com' && password.value === 'password') {
      // Перенаправление на главную страницу после успешного входа
      router.push('/');
    } else {
      errorMessage.value = 'Неправильний email або пароль';
    }
  } catch (error) {
    errorMessage.value = 'Сталася помилка при вході. Спробуйте пізніше.';
    console.error('Login error:', error);
  } finally {
    loading.value = false;
  }
};
</script> 
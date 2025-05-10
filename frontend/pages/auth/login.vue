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
import { useApi } from '~/composables/useApi';

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
    const { post } = useApi();
    
    // Запит до API для логіну
    const result = await post('/customers/login', {
      email: email.value,
      password: password.value,
      remember_me: rememberMe.value
    });

    if (!result.success) {
      errorMessage.value = result.data.message || 'Помилка при вході. Спробуйте пізніше.';
      return;
    }

    if (result.data.success) {
      // Зберегти інформацію про користувача у локальному сховищі
      localStorage.setItem('customer', JSON.stringify(result.data.data.customer));
      localStorage.setItem('isLoggedIn', 'true');
      
      // Генеруємо подію для оновлення хедера
      window.dispatchEvent(new Event('checkAuth'));
      
      // Перенаправлення на головну сторінку
      router.push('/');
    } else {
      errorMessage.value = result.data.message || 'Неправильний email або пароль';
    }
  } catch (error) {
    errorMessage.value = 'Сталася помилка при вході. Спробуйте пізніше.';
    console.error('Login error:', error);
  } finally {
    loading.value = false;
  }
};
</script> 
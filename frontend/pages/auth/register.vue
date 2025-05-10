<template>
  <div class="bg-gray-50 min-h-screen py-12">
    <div class="container mx-auto px-4">
      <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <div class="py-4 px-6 bg-amber-600">
          <h1 class="text-2xl font-bold text-white text-center">Реєстрація</h1>
        </div>
        
        <div class="py-8 px-6">
          <form class="space-y-6" @submit.prevent="handleRegister">
            <!-- Ім'я та прізвище -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label for="first-name" class="block text-sm font-medium text-gray-700">Ім'я</label>
                <div class="mt-1">
                  <input 
                    id="first-name" 
                    v-model="firstName" 
                    type="text" 
                    required 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                    placeholder="Ім'я"
                  >
                </div>
              </div>
              <div>
                <label for="last-name" class="block text-sm font-medium text-gray-700">Прізвище</label>
                <div class="mt-1">
                  <input 
                    id="last-name" 
                    v-model="lastName" 
                    type="text" 
                    required 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                    placeholder="Прізвище"
                  >
                </div>
              </div>
            </div>
            
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
                  placeholder="ваш@пошта.com"
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
                  autocomplete="new-password" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                  placeholder="Мінімум 8 символів"
                >
              </div>
              <p class="mt-1 text-xs text-gray-500">Пароль повинен містити не менше 8 символів, включаючи цифри та спеціальні символи</p>
            </div>
            
            <!-- Підтвердження пароля -->
            <div>
              <label for="password-confirm" class="block text-sm font-medium text-gray-700">Підтвердження пароля</label>
              <div class="mt-1">
                <input 
                  id="password-confirm" 
                  v-model="passwordConfirm" 
                  type="password" 
                  required 
                  autocomplete="new-password" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                  placeholder="Повторіть пароль"
                >
              </div>
            </div>
            
            <!-- Згода з умовами -->
            <div class="flex items-start">
              <div class="flex items-center h-5">
                <input 
                  id="terms" 
                  v-model="acceptTerms" 
                  type="checkbox" 
                  required
                  class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded"
                >
              </div>
              <div class="ml-3 text-sm">
                <label for="terms" class="text-gray-700">
                  Я погоджуюсь з 
                  <NuxtLink to="/terms" class="text-amber-600 hover:text-amber-500">умовами використання</NuxtLink>
                  та 
                  <NuxtLink to="/privacy" class="text-amber-600 hover:text-amber-500">політикою конфіденційності</NuxtLink>
                </label>
              </div>
            </div>
            
            <!-- Повідомлення про помилку -->
            <div v-if="errorMessage" class="text-red-600 text-sm text-center">
              {{ errorMessage }}
            </div>
            
            <!-- Кнопка реєстрації -->
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
                <span v-else>Зареєструватися</span>
              </button>
            </div>
          </form>
          
          <!-- Вже є акаунт -->
          <div class="mt-6 text-center">
            <p class="text-sm text-gray-700">
              Вже є акаунт?
              <NuxtLink to="/auth/login" class="font-medium text-amber-600 hover:text-amber-500">
                Увійти
              </NuxtLink>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useApi } from '~/composables/useApi';

const router = useRouter();

// Форма даних
const firstName = ref('');
const lastName = ref('');
const email = ref('');
const password = ref('');
const passwordConfirm = ref('');
const acceptTerms = ref(false);
const loading = ref(false);
const errorMessage = ref('');

// Валідатори
const isPasswordValid = computed(() => {
  const minLength = 8;
  return password.value.length >= minLength;
});

const isPasswordMatch = computed(() => {
  return password.value === passwordConfirm.value;
});

// Обробник реєстрації
const handleRegister = async () => {
  loading.value = true;
  errorMessage.value = '';
  
  try {
    // Перевірка паролю
    if (!isPasswordValid.value) {
      errorMessage.value = 'Пароль повинен містити не менше 8 символів';
      loading.value = false;
      return;
    }
    
    // Перевірка збігу паролів
    if (!isPasswordMatch.value) {
      errorMessage.value = 'Паролі не збігаються';
      loading.value = false;
      return;
    }
    
    try {
      const { post } = useApi();
      
      // Реєстрація через API
      const result = await post('/customers/register', {
        first_name: firstName.value,
        last_name: lastName.value,
        email: email.value,
        password: password.value
      });

      if (!result.success) {
        errorMessage.value = result.data.message || 'Помилка при реєстрації. Спробуйте пізніше.';
        if (result.data.errors && result.data.errors.email) {
          errorMessage.value = 'Користувач з таким email вже існує';
        }
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
        errorMessage.value = result.data.message || 'Сталася помилка при реєстрації. Спробуйте пізніше.';
      }
    } catch (error) {
      errorMessage.value = 'Сталася помилка при реєстрації. Спробуйте пізніше.';
      console.error('Registration error:', error);
    }
  } catch (error) {
    errorMessage.value = 'Сталася помилка при реєстрації. Спробуйте пізніше.';
    console.error('Registration error:', error);
  } finally {
    loading.value = false;
  }
};
</script> 
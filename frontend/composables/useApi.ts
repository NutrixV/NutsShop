import { useRuntimeConfig } from '#app';
import { useConfig } from '~/composables/useConfig';

export function useApi() {
  const { apiBaseUrl } = useConfig();

  /**
   * Отримання CSRF токену для захищених запитів
   */
  async function getCsrfToken() {
    try {
      const response = await fetch(`${apiBaseUrl}/csrf-token`, {
        method: 'GET',
        headers: {
          'Accept': 'application/json'
        },
        credentials: 'include'
      });
      
      const data = await response.json();
      return data.csrf_token;
    } catch (error) {
      console.error('Error fetching CSRF token:', error);
      throw error;
    }
  }

  /**
   * Додавання авторизаційних заголовків
   */
  function getAuthHeaders() {
    const headers: Record<string, string> = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };
    
    // Додаємо CSRF токен, якщо він є
    const csrfToken = localStorage.getItem('csrf_token');
    if (csrfToken) {
      headers['X-CSRF-TOKEN'] = csrfToken;
    }
    
    // Додаємо токен авторизації, якщо він є
    const authToken = localStorage.getItem('auth_token');
    if (authToken) {
      headers['Authorization'] = `Bearer ${authToken}`;
    }
    
    return headers;
  }

  /**
   * Створення POST запиту з CSRF токеном
   */
  async function post(endpoint: string, body: any) {
    try {
      // Отримуємо CSRF токен
      const csrfToken = await getCsrfToken();
      if (csrfToken) {
        localStorage.setItem('csrf_token', csrfToken);
      }
      
      // Формуємо заголовки
      const headers = getAuthHeaders();
      if (csrfToken) {
        headers['X-CSRF-TOKEN'] = csrfToken;
      }
      
      // Виконуємо запит
      const response = await fetch(`${apiBaseUrl}${endpoint}`, {
        method: 'POST',
        headers,
        credentials: 'include',
        body: JSON.stringify(body)
      });

      const data = await response.json();
      
      return {
        success: response.ok,
        data,
        status: response.status
      };
    } catch (error) {
      console.error(`Error in API POST request to ${endpoint}:`, error);
      throw error;
    }
  }

  /**
   * Створення GET запиту
   */
  async function get(endpoint: string) {
    try {
      // Формуємо заголовки
      const headers = getAuthHeaders();
      
      const response = await fetch(`${apiBaseUrl}${endpoint}`, {
        method: 'GET',
        headers,
        credentials: 'include'
      });

      const data = await response.json();
      
      return {
        success: response.ok,
        data,
        status: response.status
      };
    } catch (error) {
      console.error(`Error in API GET request to ${endpoint}:`, error);
      throw error;
    }
  }

  return {
    getCsrfToken,
    post,
    get
  };
} 
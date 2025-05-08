import { useRuntimeConfig } from '#app';

export function useApi() {
  const config = useRuntimeConfig();
  const apiBaseUrl = config.public.apiBaseUrl;

  /**
   * Отримання CSRF токену для захищених запитів
   */
  async function getCsrfToken() {
    try {
      const response = await fetch(`${apiBaseUrl}/api/csrf-token`, {
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
   * Створення POST запиту з CSRF токеном
   */
  async function post(endpoint: string, body: any) {
    try {
      // Отримуємо CSRF токен
      const csrfToken = await getCsrfToken();
      
      // Виконуємо запит
      const response = await fetch(`${apiBaseUrl}${endpoint}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
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
      const response = await fetch(`${apiBaseUrl}${endpoint}`, {
        method: 'GET',
        headers: {
          'Accept': 'application/json'
        },
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
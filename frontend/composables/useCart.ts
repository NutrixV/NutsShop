import { ref, computed, watch } from 'vue';
import { useConfig } from './useConfig';

// Types
export interface CartItem {
  item_id: number;
  product_id: number;
  sku: string;
  name: string;
  price: number;
  qty: number;
  row_total: number;
  product: {
    entity_id: number;
    name: string;
    sku: string;
    price: number;
    image?: string; // We'll add image in the mapper
  };
  image?: string; // For UI convenience
}

export interface Cart {
  entity_id: string;
  items_count: number;
  items: CartItem[];
  subtotal: number;
  grand_total: number;
  currency: string;
}

// Створюємо глобальний об'єкт для відстеження подій кошика
export const cartEventBus = {
  itemAdded: ref(0),
};

// Создаем глобальную переменную для отслеживания обновлений корзины
export const cartUpdateCounter = ref(0);

export function useCart() {
  const { apiBaseUrl } = useConfig();
  const cart = ref<Cart | null>(null);
  const loading = ref(false);
  const error = ref<string | null>(null);
  
  // Computed properties
  const itemsCount = computed(() => cart.value?.items_count || 0);
  const subtotal = computed(() => cart.value?.subtotal || 0);
  const grandTotal = computed(() => cart.value?.grand_total || 0);
  const items = computed(() => cart.value?.items || []);
  
  // Format price for display
  const formatPrice = (price: number): string => {
    return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
  };

  // Функция, которая сразу инкрементирует счетчик обновлений
  const notifyCartUpdated = () => {
    cartUpdateCounter.value += 1;
  };

  // Отримуємо CSRF токен для відправки запитів
  const getCsrfToken = async () => {
    try {
      const response = await fetch(`${apiBaseUrl}/csrf-token`, {
        credentials: 'include'
      });
      
      if (response.ok) {
        const data = await response.json();
        return data.csrf_token;
      }
      
      return null;
    } catch (error) {
      return null;
    }
  };

  // Функция, которая сразу обновляет корзину на основе полученных данных
  const updateCartFromResponse = (data: any) => {
    if (!data) return;
    
    // Map product images to cart items for convenience
    const mappedItems = data.items.map((item: CartItem) => {
      let imagePath = '/images/placeholder-product.jpg';
      
      if (item.product?.image) {
        // Используем полный путь к изображению с бэкенда
        imagePath = item.product.image;
      }
      
      const mappedItem = { 
        ...item,
        image: imagePath
      };
      return mappedItem;
    });
    
    cart.value = {
      ...data,
      items: mappedItems,
    };
    
    // Сразу уведомляем о обновлении
    notifyCartUpdated();
  };

  /**
   * Loads the cart from the API
   */
  const loadCart = async () => {
    // Якщо вже йде завантаження, не запускаємо нове
    if (loading.value) {
      return;
    }
    
    loading.value = true;
    error.value = null;
    
    try {
      const response = await fetch(`${apiBaseUrl}/cart`, {
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        }
      });
      
      if (!response.ok) {
        throw new Error(`Error loading cart: ${response.status}`);
      }
      
      const data = await response.json();
      
      // Обновляем корзину с полученными данными
      updateCartFromResponse(data);
    } catch (err: any) {
      error.value = err.message || 'Failed to load cart';
    } finally {
      loading.value = false;
    }
  };
  
  /**
   * Adds an item to the cart
   */
  const addToCart = async (productId: number, qty: number) => {
    loading.value = true;
    error.value = null;
    
    try {
      // Получаем CSRF токен для Laravel
      const csrfToken = await getCsrfToken();
      
      const requestBody = {
        product_id: productId,
        qty: qty
      };
      
      const response = await fetch(`${apiBaseUrl}/cart/items`, {
        method: 'POST',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken || '',
        },
        body: JSON.stringify(requestBody),
      });
      
      if (!response.ok) {
        throw new Error(`Error adding to cart: ${response.status}`);
      }
      
      const data = await response.json();
      
      // Обновляем корзину с полученными данными
      updateCartFromResponse(data);
      
      // Сповіщаємо, що додано товар до кошика
      cartEventBus.itemAdded.value += 1;
      
      return true;
    } catch (err: any) {
      error.value = err.message || 'Failed to add item to cart';
      return false;
    } finally {
      loading.value = false;
    }
  };
  
  /**
   * Updates an item in the cart
   */
  const updateCartItem = async (itemId: number, qty: number) => {
    loading.value = true;
    error.value = null;
    
    try {
      // Получаем CSRF токен для Laravel
      const csrfToken = await getCsrfToken();
      
      const response = await fetch(`${apiBaseUrl}/cart/items/${itemId}`, {
        method: 'PUT',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken || '',
        },
        body: JSON.stringify({
          qty: qty
        }),
      });
      
      if (!response.ok) {
        throw new Error(`Error updating cart item: ${response.status}`);
      }
      
      const data = await response.json();
      
      // Обновляем корзину с полученными данными
      updateCartFromResponse(data);
      
      return true;
    } catch (err: any) {
      error.value = err.message || 'Failed to update cart item';
      return false;
    } finally {
      loading.value = false;
    }
  };
  
  /**
   * Removes an item from the cart
   */
  const removeCartItem = async (itemId: number) => {
    loading.value = true;
    error.value = null;
    
    try {
      // Получаем CSRF токен для Laravel
      const csrfToken = await getCsrfToken();
      
      const response = await fetch(`${apiBaseUrl}/cart/items/${itemId}`, {
        method: 'DELETE',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken || '',
        },
      });
      
      if (!response.ok) {
        throw new Error(`Error removing cart item: ${response.status}`);
      }
      
      const data = await response.json();
      
      // Обновляем корзину с полученными данными
      updateCartFromResponse(data);
      
      return true;
    } catch (err: any) {
      error.value = err.message || 'Failed to remove cart item';
      return false;
    } finally {
      loading.value = false;
    }
  };
  
  /**
   * Clears the cart by removing all items one by one
   */
  const clearCart = async () => {
    if (!cart.value || !cart.value.items || cart.value.items.length === 0) {
      return true;
    }
    
    loading.value = true;
    error.value = null;
    
    try {
      // Make a copy of items to avoid mutation during iteration
      const itemsToRemove = [...cart.value.items];
      
      for (const item of itemsToRemove) {
        await removeCartItem(item.item_id);
      }
      
      // Оновлюємо кошик після очищення
      await loadCart();
      return true;
    } catch (err: any) {
      error.value = err.message || 'Failed to clear cart';
      return false;
    } finally {
      loading.value = false;
    }
  };
  
  // Initialize cart on composable creation
  loadCart();
  
  return {
    cart,
    loading,
    error,
    itemsCount,
    subtotal,
    grandTotal,
    items,
    formatPrice,
    loadCart,
    addToCart,
    updateCartItem,
    removeCartItem,
    clearCart,
    cartUpdateCounter
  };
} 
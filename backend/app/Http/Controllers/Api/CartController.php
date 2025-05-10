<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CatalogProduct;
use App\Models\Quote;
use App\Models\QuoteItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Відображає поточний кошик.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        // Отримуємо ID кошика з cookie
        $cartId = $request->cookie('cart_id');
        $cart = null;
        $created = false;
        
        // Якщо немає ID кошика або кошик не існує, створюємо новий
        if (!$cartId || !($cart = Quote::with('items.product')->where('entity_id', $cartId)->where('is_active', true)->first())) {
            // Створюємо новий кошик
            $cart = new Quote();
            $cart->is_active = true;
            $cart->items_count = 0;
            $cart->subtotal = 0;
            $cart->grand_total = 0;
            $cart->currency = 'USD';
            $cart->save();
            
            // Зберігаємо ID у змінній для подальшого використання
            $cartId = $cart->entity_id;
            $created = true;
        }
        
        // Створюємо відповідь з cookie, який не змінюється
        $response = response()->json($this->formatCartResponse($cart));
        
        // Якщо кошик був створений, додаємо cookie
        if ($created) {
            // Встановлюємо cookie з правильними параметрами для різних середовищ
            $cookieParams = [
                'name' => 'cart_id',
                'value' => $cartId,
                'minutes' => 60 * 24 * 30, // 30 днів
                'path' => '/',
                'domain' => null,
                'secure' => true,
                'httpOnly' => true,
                'sameSite' => 'lax'
            ];
            
            // Для Render.com додаємо спеціальні параметри
            if (env('APP_ENV') === 'production' && strpos(env('APP_URL', ''), 'render.com') !== false) {
                // На Render використовуємо специфічні налаштування для кросдоменних cookie
                $cookieParams['sameSite'] = 'none';
                // Обов'язково secure для SameSite=None
                $cookieParams['secure'] = true;
            }
            
            $response->cookie(
                $cookieParams['name'],
                $cookieParams['value'],
                $cookieParams['minutes'],
                $cookieParams['path'],
                $cookieParams['domain'],
                $cookieParams['secure'],
                $cookieParams['httpOnly'],
                false, // raw
                $cookieParams['sameSite']
            );
        }
        
        return $response;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Додає товар до кошика.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function addItem(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:catalog_product,entity_id',
            'qty' => 'required|numeric|min:1',
        ]);
        
        // Отримуємо ID кошика з cookie
        $cartId = $request->cookie('cart_id');
        $cart = null;
        
        // Якщо немає ID кошика або кошик не існує, створюємо новий
        if (!$cartId || !($cart = Quote::where('entity_id', $cartId)->where('is_active', true)->first())) {
            // Створюємо новий кошик
            $cart = new Quote();
            $cart->is_active = true;
            $cart->save();
            
            // Зберігаємо ID у змінній для подальшого використання
            $cartId = $cart->entity_id;
        }
        
        // Відтепер ми завжди маємо або існуючий кошик, або новий кошик
        
        // Отримуємо дані товару
        $product = CatalogProduct::findOrFail($request->input('product_id'));
        
        // Перевіряємо, чи вже є такий товар у кошику
        $item = QuoteItem::where('quote_id', $cart->entity_id)
            ->where('product_id', $product->entity_id)
            ->first();
        
        if ($item) {
            // Оновлюємо кількість, якщо товар вже є в кошику
            $item->qty += $request->input('qty');
            $item->row_total = $item->price * $item->qty;
            $item->save();
        } else {
            // Додаємо новий товар до кошика
            $item = new QuoteItem();
            $item->quote_id = $cart->entity_id;
            $item->product_id = $product->entity_id;
            $item->sku = $product->sku;
            $item->name = $product->name;
            $item->price = $product->price;
            $item->qty = $request->input('qty');
            $item->row_total = $product->price * $request->input('qty');
            $item->save();
        }
        
        // Оновлюємо суми кошика
        $this->updateCartTotals($cart);
        
        // Оновлюємо кошик для відправки свіжих даних
        $cart = Quote::with('items.product')->findOrFail($cart->entity_id);
        
        // Створюємо відповідь з cookie, який не змінюється
        $response = response()->json($this->formatCartResponse($cart));
        
        // Встановлюємо cookie з правильними параметрами для різних середовищ
        $cookieParams = [
            'name' => 'cart_id',
            'value' => $cartId,
            'minutes' => 60 * 24 * 30, // 30 днів
            'path' => '/',
            'domain' => null,
            'secure' => true,
            'httpOnly' => true,
            'sameSite' => 'lax'
        ];
        
        // Для Render.com додаємо спеціальні параметри
        if (env('APP_ENV') === 'production' && strpos(env('APP_URL', ''), 'render.com') !== false) {
            // На Render використовуємо специфічні налаштування для кросдоменних cookie
            $cookieParams['sameSite'] = 'none';
            // Обов'язково secure для SameSite=None
            $cookieParams['secure'] = true;
        }
        
        $response->cookie(
            $cookieParams['name'],
            $cookieParams['value'],
            $cookieParams['minutes'],
            $cookieParams['path'],
            $cookieParams['domain'],
            $cookieParams['secure'],
            $cookieParams['httpOnly'],
            false, // raw
            $cookieParams['sameSite']
        );
        
        return $response;
    }

    /**
     * Оновлює кількість товару в кошику.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function updateItem(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'qty' => 'required|numeric|min:1',
        ]);
        
        // Отримуємо ID кошика з cookie
        $cartId = $request->cookie('cart_id');
        
        if (!$cartId) {
            return response()->json(['error' => 'Cart not found'], 404);
        }
        
        // Отримуємо кошик
        $cart = Quote::where('entity_id', $cartId)->where('is_active', true)->firstOrFail();
        
        // Отримуємо товар з кошика
        $item = QuoteItem::where('item_id', $id)
            ->where('quote_id', $cart->entity_id)
            ->firstOrFail();
        
        // Оновлюємо кількість
        $item->qty = $request->input('qty');
        $item->row_total = $item->price * $item->qty;
        $item->save();
        
        // Оновлюємо суми кошика
        $this->updateCartTotals($cart);
        
        return response()->json($this->formatCartResponse($cart));
    }

    /**
     * Видаляє товар з кошика.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function removeItem(Request $request, string $id): JsonResponse
    {
        // Отримуємо ID кошика з cookie
        $cartId = $request->cookie('cart_id');
        
        if (!$cartId) {
            return response()->json(['error' => 'Cart not found'], 404);
        }
        
        // Отримуємо кошик
        $cart = Quote::where('entity_id', $cartId)->where('is_active', true)->firstOrFail();
        
        // Видаляємо товар з кошика
        QuoteItem::where('item_id', $id)
            ->where('quote_id', $cart->entity_id)
            ->delete();
        
        // Оновлюємо суми кошика
        $this->updateCartTotals($cart);
        
        return response()->json($this->formatCartResponse($cart));
    }
    
    /**
     * Оновлює суми кошика на основі доданих товарів.
     *
     * @param Quote $cart
     * @return void
     */
    private function updateCartTotals(Quote $cart): void
    {
        // Розраховуємо загальну суму на основі товарів у кошику
        $items = QuoteItem::where('quote_id', $cart->entity_id)->get();
        
        $subtotal = 0;
        $itemsCount = 0;
        
        foreach ($items as $item) {
            $subtotal += $item->row_total;
            $itemsCount += $item->qty;
        }
        
        $cart->subtotal = $subtotal;
        $cart->grand_total = $subtotal; // Наразі без податків і знижок
        $cart->items_count = $itemsCount; // Оновлюємо кількість товарів у кошику
        $cart->save();
    }
    
    /**
     * Форматує дані кошика для відповіді API.
     *
     * @param Quote $cart
     * @return array<string, mixed>
     */
    private function formatCartResponse(Quote $cart): array
    {
        // Завантажуємо товари у кошику
        $cart->load('items.product');
        
        $items = [];
        foreach ($cart->items as $item) {
            // Отримуємо зображення товару якщо воно є
            $productImage = null;
            
            // Якщо є зображення в продукті, використовуємо його
            if ($item->product && $item->product->image) {
                // Повна URL до зображення, включаючи базовий шлях
                $productImage = url('/storage/' . $item->product->image);
            } 
            // Якщо немає, пробуємо використати стандартне зображення
            else if ($item->product) {
                $productImage = url('/images/placeholder-product.jpg');
            }
            
            $items[] = [
                'item_id' => $item->item_id,
                'product_id' => $item->product_id,
                'sku' => $item->sku,
                'name' => $item->name,
                'price' => $item->price,
                'qty' => $item->qty,
                'row_total' => $item->row_total,
                'product' => [
                    'entity_id' => $item->product->entity_id,
                    'name' => $item->product->name,
                    'sku' => $item->product->sku,
                    'price' => $item->product->price,
                    'image' => $productImage
                ]
            ];
        }
        
        // Використовуємо збережене в базі значення items_count, а не кількість айтемів
        return [
            'entity_id' => $cart->entity_id,
            'items_count' => $cart->items_count,
            'items' => $items,
            'subtotal' => $cart->subtotal,
            'grand_total' => $cart->grand_total,
            'currency' => $cart->currency,
        ];
    }
}

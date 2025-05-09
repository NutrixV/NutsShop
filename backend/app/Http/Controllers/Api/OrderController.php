<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerEntity;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\CartItem;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders for the authenticated customer.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $customer = $request->session()->get('customer');
        
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Користувач не авторизований'
            ], 401);
        }
        
        $orders = SalesOrder::where('customer_id', $customer->entity_id)
            ->orderBy('created_at', 'desc')
            ->with('items')
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Store a newly created order.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'postcode' => 'required|string|max:20',
            'payment_method' => 'required|string|in:cash,card',
            'cart_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Помилка валідації даних',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            // Get cart data
            $cartId = $request->cart_id;
            
            // Шукаємо кошик за ID, незалежно від типу даних
            $cart = \App\Models\Quote::where('entity_id', $cartId)->first();
            
            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Кошик не знайдено'
                ], 404);
            }
            
            $cartItems = \App\Models\QuoteItem::where('quote_id', $cart->entity_id)->get();
            
            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Кошик порожній'
                ], 400);
            }
            
            // Check if customer is authenticated
            $customer = $request->session()->get('customer');
            $customerId = null;
            
            if ($customer) {
                $customerId = $customer->entity_id;
            }
            
            // Create new order
            $order = new SalesOrder();
            $order->increment_id = SalesOrder::generateIncrementId();
            $order->customer_id = $customerId;
            $order->status = 'pending';
            $order->subtotal = $cart->subtotal;
            $order->grand_total = $cart->grand_total;
            $order->currency = 'UAH';
            
            // Save shipping address directly as a JSON string instead of json_encode
            $shippingAddress = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'region' => $request->region,
                'postcode' => $request->postcode,
                'notes' => $request->notes ?? ''
            ];
            
            // We'll store shipping_address directly in the DB attribute
            // The model has casts defined for this field, so it will be handled correctly
            $order->shipping_address = $shippingAddress;
            $order->payment_method = $request->payment_method;
            $order->save();
            
            // Create order items
            foreach ($cartItems as $item) {
                $orderItem = new SalesOrderItem();
                $orderItem->order_id = $order->entity_id;
                $orderItem->product_id = $item->product_id;
                $orderItem->sku = $item->sku;
                $orderItem->name = $item->name;
                $orderItem->price = $item->price;
                $orderItem->qty_ordered = $item->qty;
                $orderItem->row_total = $item->row_total;
                $orderItem->save();
            }
            
            // Clear the cart after order is placed
            \App\Models\QuoteItem::where('quote_id', $cart->entity_id)->delete();
            $cart->subtotal = 0;
            $cart->grand_total = 0;
            $cart->items_count = 0;
            $cart->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Замовлення успішно створено',
                'data' => [
                    'order_id' => $order->entity_id,
                    'increment_id' => $order->increment_id
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Сталася помилка при створенні замовлення'
            ], 500);
        }
    }

    /**
     * Display the specified order.
     *
     * @param string $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id, Request $request)
    {
        $customer = $request->session()->get('customer');
        
        $order = SalesOrder::with('items')->find($id);
        
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Замовлення не знайдено'
            ], 404);
        }
        
        // Якщо замовлення належить авторизованому користувачу або замовлення без авторизації
        if (($customer && $order->customer_id == $customer->entity_id) || !$order->customer_id) {
            return response()->json([
                'success' => true,
                'data' => $order
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Доступ заборонено'
        ], 403);
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
}

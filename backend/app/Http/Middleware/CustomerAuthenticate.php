<?php

namespace App\Http\Middleware;

use App\Models\CustomerEntity;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Перевірка авторизації на основі наявності клієнта в сесії
        $customer = $request->session()->get('customer');
        
        if (!$customer) {
            // Спробуємо отримати токен з різних джерел
            $token = null;
            
            // 1. Перевірка заголовка Authorization: Bearer token
            $authHeader = $request->header('Authorization');
            if ($authHeader && strpos($authHeader, 'Bearer ') === 0) {
                $token = substr($authHeader, 7);
            }
            
            // 2. Альтернативно перевірка параметра запиту api_token
            if (!$token && $request->has('api_token')) {
                $token = $request->query('api_token');
            }
            
            // 3. Перевірка заголовка x-api-token
            if (!$token && $request->header('x-api-token')) {
                $token = $request->header('x-api-token');
            }
            
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Користувач не авторизований'
                ], 401);
            }
            
            // Пошук користувача за токеном
            $customer = CustomerEntity::where('api_token', $token)->first();
            
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Недійсний токен авторизації'
                ], 401);
            }
            
            // Додаємо клієнта в сесію для подальшого використання
            $request->session()->put('customer', $customer);
        }
        
        return $next($request);
    }
} 
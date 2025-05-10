<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware для обробки CORS на Render.com
 */
class RenderCorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Отримуємо відповідь спочатку
        $response = $next($request);
        
        // Додаємо підтримку X-API-Token лише якщо це Render.com
        if (env('APP_ENV') === 'production' && str_contains(env('APP_URL', ''), 'render.com')) {
            // Безпечно додаємо заголовок, тільки якщо відповідь успішна
            if ($response instanceof Response) {
                // Перевіряємо, чи відповідь містить вже CORS заголовки
                $hasHeader = $response->headers->has('Access-Control-Allow-Headers');
                
                if ($hasHeader) {
                    // Додаємо наш заголовок до існуючих
                    $existingHeaders = $response->headers->get('Access-Control-Allow-Headers');
                    if (!str_contains($existingHeaders, 'X-API-Token')) {
                        $response->headers->set(
                            'Access-Control-Allow-Headers', 
                            $existingHeaders . ', X-API-Token'
                        );
                    }
                } else {
                    // Якщо заголовків немає, встановлюємо їх
                    $response->headers->set(
                        'Access-Control-Allow-Headers', 
                        'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN, x-csrf-token, X-API-Token'
                    );
                }
            }
        }
        
        return $response;
    }
} 
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerEntity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CustomerController extends Controller
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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
     * Register a new customer.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:64',
            'last_name' => 'required|string|max:64',
            'email' => 'required|string|email|max:255|unique:customer_entity',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $customer = CustomerEntity::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
        ]);

        // Зберігаємо клієнта в сесії після реєстрації
        $request->session()->put('customer', $customer);

        return response()->json([
            'success' => true,
            'message' => 'Customer registered successfully',
            'data' => [
                'customer' => [
                    'id' => $customer->entity_id,
                    'email' => $customer->email,
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                ]
            ]
        ], 201);
    }

    /**
     * Login customer.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $customer = CustomerEntity::where('email', $request->email)->first();

        if (!$customer || !Hash::check($request->password, $customer->password_hash)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Зберігаємо клієнта в сесії
        $request->session()->put('customer', $customer);
        
        // Генеруємо API токен для зовнішньої авторизації
        $token = Str::random(60);
        $customer->api_token = $token;
        $customer->save();

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'customer' => [
                    'id' => $customer->entity_id,
                    'email' => $customer->email,
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                ],
                'remember_me' => $request->remember_me ?? false,
                'token' => $token // Додано токен для авторизації
            ]
        ]);
    }

    /**
     * Get authenticated customer profile
     */
    public function profile(Request $request)
    {
        $customer = $request->session()->get('customer');
        
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Користувач не авторизований'
            ], 401);
        }
        
        // Оновлюємо дані з бази, щоб отримати найсвіжіші дані
        $customer = CustomerEntity::with('addresses')->find($customer->entity_id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'customer' => [
                    'id' => $customer->entity_id,
                    'email' => $customer->email,
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'addresses' => $customer->addresses
                ]
            ]
        ]);
    }
}

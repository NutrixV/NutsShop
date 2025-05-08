<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Перевіряємо чи вже існує адмін з такою email-адресою
        if (!User::where('email', 'admin@nutsshop.com')->exists()) {
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@nutsshop.com',
                'password' => Hash::make('admin123'), // Зауважте: це тестовий пароль для розробки!
                'is_admin' => true
            ]);
        }
    }
} 
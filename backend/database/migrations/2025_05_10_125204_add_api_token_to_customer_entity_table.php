<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Перевіряємо, чи існує вже колонка api_token
        if (!Schema::hasColumn('customer_entity', 'api_token')) {
            Schema::table('customer_entity', function (Blueprint $table) {
                $table->string('api_token', 80)->unique()->nullable()->default(null)->after('last_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Перевіряємо, чи існує колонка перед видаленням
        if (Schema::hasColumn('customer_entity', 'api_token')) {
            Schema::table('customer_entity', function (Blueprint $table) {
                $table->dropColumn('api_token');
            });
        }
    }
}; 
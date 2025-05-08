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
        Schema::table('catalog_product', function (Blueprint $table) {
            $table->string('image')->nullable()->after('expiry_date');
            $table->string('image_alt')->nullable()->after('image');
            $table->json('gallery')->nullable()->after('image_alt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catalog_product', function (Blueprint $table) {
            $table->dropColumn(['image', 'image_alt', 'gallery']);
        });
    }
};

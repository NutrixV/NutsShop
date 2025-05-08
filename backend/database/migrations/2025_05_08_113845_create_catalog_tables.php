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
        // Catalog category table
        Schema::create('catalog_category', function (Blueprint $table) {
            $table->id('category_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('name', 255);
            $table->string('url_key', 255)->unique()->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('position')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('parent_id')
                ->references('category_id')
                ->on('catalog_category')
                ->onDelete('set null');
        });

        // Catalog product table
        Schema::create('catalog_product', function (Blueprint $table) {
            $table->id('entity_id');
            $table->string('sku', 64)->unique();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->decimal('price', 12, 2);
            $table->char('base_currency', 3)->default('UAH');
            $table->decimal('weight', 8, 3)->nullable();
            $table->decimal('qty', 12, 2)->default(0);
            $table->boolean('is_in_stock')->default(true);
            $table->unsignedTinyInteger('visibility')->default(4); // 1 = Not Visible, 4 = Catalog & Search
            $table->unsignedTinyInteger('status')->default(1); // 1 = Enabled, 2 = Disabled

            // Домен-специфічні атрибути
            $table->string('nut_type', 50)->nullable();
            $table->unsignedTinyInteger('sweetness_level')->nullable(); // 0-10
            $table->decimal('cocoa_pct', 5, 2)->nullable();
            $table->boolean('salted')->default(false);
            $table->boolean('roasted')->default(false);
            $table->boolean('gluten_free')->default(false);
            $table->boolean('organic')->default(false);
            $table->char('origin_country', 2)->nullable();
            $table->unsignedInteger('weight_g')->nullable();
            $table->date('expiry_date')->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        // Catalog category product relation table
        Schema::create('catalog_category_product', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedInteger('position')->default(0);

            $table->primary(['category_id', 'product_id']);

            $table->foreign('category_id')
                ->references('category_id')
                ->on('catalog_category')
                ->onDelete('cascade');

            $table->foreign('product_id')
                ->references('entity_id')
                ->on('catalog_product')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalog_category_product');
        Schema::dropIfExists('catalog_product');
        Schema::dropIfExists('catalog_category');
    }
};

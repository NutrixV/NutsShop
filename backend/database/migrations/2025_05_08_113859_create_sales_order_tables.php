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
        // Sales Order table
        Schema::create('sales_order', function (Blueprint $table) {
            $table->id('entity_id');
            $table->string('increment_id', 32)->unique()->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('status', 32)->default('pending');
            $table->decimal('subtotal', 12, 2)->nullable();
            $table->decimal('grand_total', 12, 2)->nullable();
            $table->char('currency', 3)->default('UAH');
            $table->json('shipping_address')->nullable();
            $table->string('payment_method', 20)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('customer_id')
                ->references('entity_id')
                ->on('customer_entity')
                ->onDelete('set null');
        });

        // Sales Order Item table
        Schema::create('sales_order_item', function (Blueprint $table) {
            $table->id('item_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('sku', 64)->nullable();
            $table->string('name', 255)->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->decimal('qty_ordered', 12, 2)->nullable();
            // В PostgreSQL немає вбудованої GENERATED ALWAYS AS, використаємо тригери окремо
            $table->decimal('row_total', 12, 2)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('order_id')
                ->references('entity_id')
                ->on('sales_order')
                ->onDelete('cascade');

            $table->foreign('product_id')
                ->references('entity_id')
                ->on('catalog_product')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_order_item');
        Schema::dropIfExists('sales_order');
    }
};

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
        // Quote (Cart) table
        Schema::create('quote', function (Blueprint $table) {
            $table->id('entity_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->decimal('grand_total', 12, 2)->default(0.00);
            $table->char('currency', 3)->default('UAH');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('customer_id')
                ->references('entity_id')
                ->on('customer_entity')
                ->onDelete('set null');
        });

        // Quote Item table
        Schema::create('quote_item', function (Blueprint $table) {
            $table->id('item_id');
            $table->unsignedBigInteger('quote_id');
            $table->unsignedBigInteger('product_id');
            $table->string('sku', 64)->nullable();
            $table->string('name', 255)->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->decimal('qty', 12, 2)->default(1);
            // В PostgreSQL немає вбудованої GENERATED ALWAYS AS, використаємо тригери окремо
            $table->decimal('row_total', 12, 2)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->unique(['quote_id', 'product_id'], 'uq_quote_product');

            $table->foreign('quote_id')
                ->references('entity_id')
                ->on('quote')
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
        Schema::dropIfExists('quote_item');
        Schema::dropIfExists('quote');
    }
};

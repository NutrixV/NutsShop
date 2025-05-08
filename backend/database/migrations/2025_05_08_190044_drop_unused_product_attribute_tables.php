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
        // Видаляємо таблицю значень атрибутів (catalog_product_attribute_value)
        Schema::dropIfExists('catalog_product_attribute_value');
        
        // Видаляємо таблицю опцій атрибутів (catalog_product_attribute_option)
        Schema::dropIfExists('catalog_product_attribute_option');
        
        // Видаляємо таблицю атрибутів (catalog_product_attribute)
        Schema::dropIfExists('catalog_product_attribute');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Відновлення таблиці catalog_product_attribute
        Schema::create('catalog_product_attribute', function (Blueprint $table) {
            $table->id('attribute_id');
            $table->string('attribute_code');
            $table->string('frontend_label');
            $table->string('frontend_input', 50)->default('text');
            $table->string('attribute_group', 50)->nullable();
            $table->integer('position')->default(0);
            $table->boolean('is_required')->default(false);
            $table->boolean('is_filterable')->default(false);
            $table->boolean('is_searchable')->default(false);
            $table->timestamps();
            
            $table->unique('attribute_code');
        });
        
        // Відновлення таблиці catalog_product_attribute_option
        Schema::create('catalog_product_attribute_option', function (Blueprint $table) {
            $table->id('option_id');
            $table->bigInteger('attribute_id');
            $table->string('value');
            $table->integer('position')->default(0);
            $table->timestamps();
            
            $table->foreign('attribute_id')
                  ->references('attribute_id')
                  ->on('catalog_product_attribute')
                  ->onDelete('cascade');
        });
        
        // Відновлення таблиці catalog_product_attribute_value
        Schema::create('catalog_product_attribute_value', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('attribute_id');
            $table->bigInteger('product_id');
            $table->string('value');
            $table->timestamps();
            
            $table->unique(['attribute_id', 'product_id']);
            
            $table->foreign('attribute_id')
                  ->references('attribute_id')
                  ->on('catalog_product_attribute')
                  ->onDelete('cascade');
                  
            $table->foreign('product_id')
                  ->references('entity_id')
                  ->on('catalog_product')
                  ->onDelete('cascade');
        });
    }
};

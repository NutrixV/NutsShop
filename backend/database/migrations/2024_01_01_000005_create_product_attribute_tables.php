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
        // Attributes Table
        Schema::create('catalog_product_attribute', function (Blueprint $table) {
            $table->id('attribute_id');
            $table->string('attribute_code', 255)->unique();
            $table->string('frontend_label', 255);
            $table->string('frontend_input', 50)->default('text'); // text, textarea, select, multiselect, date, boolean, etc.
            $table->string('attribute_group', 50)->nullable(); // For grouping attributes
            $table->integer('position')->default(0);
            $table->boolean('is_required')->default(false);
            $table->boolean('is_filterable')->default(false);
            $table->boolean('is_searchable')->default(false);
            $table->timestamps();
        });

        // Attribute Options Table (for select/multiselect attributes)
        Schema::create('catalog_product_attribute_option', function (Blueprint $table) {
            $table->id('option_id');
            $table->unsignedBigInteger('attribute_id');
            $table->string('value', 255);
            $table->integer('position')->default(0);
            $table->timestamps();

            $table->foreign('attribute_id')
                  ->references('attribute_id')
                  ->on('catalog_product_attribute')
                  ->onDelete('cascade');
        });

        // Attribute Values Table (actual product attributes)
        Schema::create('catalog_product_attribute_value', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attribute_id');
            $table->unsignedBigInteger('product_id');
            $table->string('value', 255);
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalog_product_attribute_value');
        Schema::dropIfExists('catalog_product_attribute_option');
        Schema::dropIfExists('catalog_product_attribute');
    }
}; 
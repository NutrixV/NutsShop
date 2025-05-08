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
        // Customer entity table
        Schema::create('customer_entity', function (Blueprint $table) {
            $table->id('entity_id');
            $table->string('email', 255)->unique();
            $table->string('password_hash', 255);
            $table->string('first_name', 64)->nullable();
            $table->string('last_name', 64)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        // Customer address table
        Schema::create('customer_address', function (Blueprint $table) {
            $table->id('address_id');
            $table->unsignedBigInteger('customer_id');
            $table->string('telephone', 32)->nullable();
            $table->string('street_line1', 255)->nullable();
            $table->string('street_line2', 255)->nullable();
            $table->string('city', 150)->nullable();
            $table->string('region', 150)->nullable();
            $table->string('postcode', 20)->nullable();
            $table->char('country_id', 2)->nullable(); // ISO 3166-1 alpha-2
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('customer_id')
                ->references('entity_id')
                ->on('customer_entity')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_address');
        Schema::dropIfExists('customer_entity');
    }
};

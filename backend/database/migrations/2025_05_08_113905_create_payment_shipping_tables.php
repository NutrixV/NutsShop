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
        // Sales Payment table
        Schema::create('sales_payment', function (Blueprint $table) {
            $table->id('payment_id');
            $table->unsignedBigInteger('order_id');
            $table->string('method', 32)->nullable();
            $table->string('transaction_id', 128)->nullable();
            $table->decimal('amount_paid', 12, 2)->nullable();
            $table->string('status', 32)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->unique('order_id');

            $table->foreign('order_id')
                ->references('entity_id')
                ->on('sales_order')
                ->onDelete('cascade');
        });

        // Sales Shipping Address table
        Schema::create('sales_shipping_address', function (Blueprint $table) {
            $table->id('address_id');
            $table->unsignedBigInteger('order_id');
            $table->string('recipient_name', 255)->nullable();
            $table->string('telephone', 32)->nullable();
            $table->string('street_line1', 255)->nullable();
            $table->string('street_line2', 255)->nullable();
            $table->string('city', 150)->nullable();
            $table->string('region', 150)->nullable();
            $table->string('postcode', 20)->nullable();
            $table->char('country_id', 2)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->unique('order_id');

            $table->foreign('order_id')
                ->references('entity_id')
                ->on('sales_order')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_shipping_address');
        Schema::dropIfExists('sales_payment');
    }
};

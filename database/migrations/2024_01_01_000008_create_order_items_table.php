<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            
            $table->string('product_name');
            $table->string('product_sku');
            $table->decimal('price', 10, 2); // Original price
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('final_price', 10, 2); // Price after discount
            $table->integer('quantity');
            $table->decimal('subtotal', 10, 2); // final_price * quantity
            
            // Is this a free item from promotion?
            $table->boolean('is_free')->default(false);
            
            // Which promotion applied to this item
            $table->foreignId('promotion_id')->nullable()->constrained()->onDelete('set null');
            
            // Additional promotion details
            $table->json('promotion_details')->nullable();
            
            // Product attributes at time of order
            $table->json('attributes')->nullable();
            
            $table->timestamps();
            
            $table->index('order_id');
            $table->index('product_id');
            $table->index('promotion_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

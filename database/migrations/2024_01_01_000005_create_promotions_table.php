<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            
            // Promotion type: buy_x_get_y_free, step_discount, fixed_price_bundle
            $table->string('type');
            
            // Status: draft, active, paused, expired
            $table->string('status')->default('draft');
            
            // Date range
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            
            // Conditions (stored as JSON for flexibility)
            // For buy_x_get_y_free: buy_quantity, get_quantity, apply_to_type, apply_to_product_ids, etc.
            // For step_discount: discount_tiers [position => percentage]
            // For fixed_price_bundle: bundle_quantity, bundle_price, eligible_product_ids, etc.
            $table->json('conditions')->nullable();
            
            // Benefits (stored as JSON for flexibility)
            $table->json('benefits')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['type', 'status']);
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};

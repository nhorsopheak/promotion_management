<?php

namespace Tests\Feature;

use App\DTOs\CartItem;
use App\Enums\PromotionStatus;
use App\Enums\PromotionType;
use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use App\Services\Promotions\PromotionEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PercentageDiscountPromotionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_percentage_discount_applies_correctly_to_all_products()
    {
        // Get products
        $tshirt = Product::where('sku', 'CLO-001')->first(); // $19.99
        $jeans = Product::where('sku', 'CLO-002')->first();  // $49.99
        $sneakers = Product::where('sku', 'CLO-003')->first(); // $79.99

        // Create cart items
        $cartItems = collect([
            new CartItem($tshirt, 1, $tshirt->price),
            new CartItem($jeans, 1, $jeans->price),
            new CartItem($sneakers, 1, $sneakers->price),
        ]);
 
        // Create promotion
        $promotion = Promotion::create([
            'code' => '10PERCENT',
            'name' => '10% Off All Products',
            'type' => PromotionType::PERCENTAGE_DISCOUNT->value,
            'status' => PromotionStatus::ACTIVE->value,
            'conditions' => [
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'apply_to_type' => 'all',
            ],
        ]);

        // Apply promotion
        $engine = app(PromotionEngine::class);
        $result = $engine->applyPromotion($cartItems, $promotion);

        // Assertions
        $this->assertTrue($result->isApplied());
        $totalAmount = $tshirt->price + $jeans->price + $sneakers->price; // 149.97
        $expectedDiscount = $totalAmount * 0.10; // 14.997 ≈ 15.00
        $this->assertEquals(15.00, $result->discountAmount);
        $this->assertCount(3, $result->affectedItems); // All 3 products
    }

    public function test_fixed_amount_discount_applies_correctly()
    {
        // Get product
        $tshirt = Product::where('sku', 'CLO-001')->first(); // $19.99

        // Create cart items
        $cartItems = collect([
            new CartItem($tshirt, 2, $tshirt->price), // 2 x $19.99 = $39.98
        ]);

        // Create promotion
        $promotion = Promotion::create([
            'code' => '5OFF',
            'name' => '$5 Off',
            'type' => PromotionType::PERCENTAGE_DISCOUNT->value,
            'status' => PromotionStatus::ACTIVE->value,
            'conditions' => [
                'discount_type' => 'fixed_amount',
                'discount_value' => 5.00,
                'apply_to_type' => 'all',
            ],
        ]);

        // Apply promotion
        $engine = app(PromotionEngine::class);
        $result = $engine->applyPromotion($cartItems, $promotion);

        // Assertions
        $this->assertTrue($result->isApplied());
        $this->assertEquals(5.00, $result->discountAmount);
        $this->assertCount(1, $result->affectedItems);
    }

    public function test_discount_applies_only_to_specific_products()
    {
        // Get products
        $tshirt = Product::where('sku', 'CLO-001')->first(); // $19.99
        $jeans = Product::where('sku', 'CLO-002')->first();  // $49.99

        // Create cart items
        $cartItems = collect([
            new CartItem($tshirt, 1, $tshirt->price),
            new CartItem($jeans, 1, $jeans->price),
        ]);

        // Create promotion that only applies to t-shirt
        $promotion = Promotion::create([
            'code' => 'TSHIRT10',
            'name' => '10% Off T-Shirts',
            'type' => PromotionType::PERCENTAGE_DISCOUNT->value,
            'status' => PromotionStatus::ACTIVE->value,
            'conditions' => [
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'apply_to_type' => 'specific_products',
                'eligible_product_ids' => [$tshirt->id],
            ],
        ]);

        // Apply promotion
        $engine = app(PromotionEngine::class);
        $result = $engine->applyPromotion($cartItems, $promotion);

        // Assertions
        $this->assertTrue($result->isApplied());
        $this->assertEquals(2.00, $result->discountAmount); // 10% of $19.99 ≈ $2.00
        $this->assertCount(1, $result->affectedItems);
        $this->assertEquals($tshirt->id, $result->affectedItems[0]);
    }

    public function test_discount_does_not_apply_when_no_eligible_items()
    {
        // Get products
        $tshirt = Product::where('sku', 'CLO-001')->first(); // $19.99
        $electronics = Product::where('category_id', Category::where('name', 'Electronics')->first()->id)->first();

        // Create cart with only electronics
        $cartItems = collect([
            new CartItem($electronics, 1, $electronics->price),
        ]);

        // Create promotion that only applies to clothing
        $clothingCategory = Category::where('name', 'Clothing')->first();
        $promotion = Promotion::create([
            'code' => 'CLOTHING10',
            'name' => '10% Off Clothing',
            'type' => PromotionType::PERCENTAGE_DISCOUNT->value,
            'status' => PromotionStatus::ACTIVE->value,
            'conditions' => [
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'apply_to_type' => 'specific_categories',
                'eligible_category_ids' => [$clothingCategory->id],
            ],
        ]);

        // Apply promotion
        $engine = app(PromotionEngine::class);
        $result = $engine->applyPromotion($cartItems, $promotion);

        // Assertions
        $this->assertFalse($result->isApplied());
        $this->assertEquals(0, $result->discountAmount);
        $this->assertCount(0, $result->affectedItems);
    }
}

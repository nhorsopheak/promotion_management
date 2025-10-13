<?php

namespace Tests\Feature;

use App\DTOs\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use App\Services\Promotions\PromotionEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuyXGetYFreePromotionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_buy_2_get_1_free_applies_correctly()
    {
        // Get products
        $tshirt = Product::where('sku', 'CLO-001')->first();
        $jeans = Product::where('sku', 'CLO-002')->first();
        $sneakers = Product::where('sku', 'CLO-003')->first();

        // Create cart items
        $cartItems = collect([
            new CartItem($tshirt, 1, $tshirt->price),
            new CartItem($jeans, 1, $jeans->price),
            new CartItem($sneakers, 1, $sneakers->price),
        ]);

        // Get promotion
        $promotion = Promotion::where('code', 'BUY2GET1')->first();

        // Apply promotion
        $engine = app(PromotionEngine::class);
        $result = $engine->applyPromotion($cartItems, $promotion);

        // Assertions
        $this->assertTrue($result->isApplied());
        $this->assertEquals(19.99, $result->discountAmount); // T-shirt is cheapest
        $this->assertCount(1, $result->freeItems);
        $this->assertEquals($tshirt->id, $result->freeItems[0]['product_id']);
    }

    public function test_buy_3_get_2_free_applies_correctly()
    {
        // Get 5 products
        $products = Product::take(5)->get();

        // Create cart items
        $cartItems = collect();
        foreach ($products as $product) {
            $cartItems->push(new CartItem($product, 1, $product->price));
        }

        // Get promotion
        $promotion = Promotion::where('code', 'BUY3GET2')->first();

        // Apply promotion
        $engine = app(PromotionEngine::class);
        $result = $engine->applyPromotion($cartItems, $promotion);

        // Assertions
        $this->assertTrue($result->isApplied());
        $this->assertCount(2, $result->freeItems); // 2 free items
        $this->assertGreaterThan(0, $result->discountAmount);
    }

    public function test_promotion_not_applied_when_insufficient_items()
    {
        // Get only 1 product
        $tshirt = Product::where('sku', 'CLO-001')->first();

        // Create cart with only 1 item
        $cartItems = collect([
            new CartItem($tshirt, 1, $tshirt->price),
        ]);

        // Get promotion (requires 2 items)
        $promotion = Promotion::where('code', 'BUY2GET1')->first();

        // Apply promotion
        $engine = app(PromotionEngine::class);
        $result = $engine->applyPromotion($cartItems, $promotion);

        // Assertions
        $this->assertFalse($result->isApplied());
        $this->assertEquals(0, $result->discountAmount);
    }

    public function test_promotion_applies_to_eligible_category_only()
    {
        // Get clothing products (eligible)
        $tshirt = Product::where('sku', 'CLO-001')->first();
        $jeans = Product::where('sku', 'CLO-002')->first();
        
        // Get electronics product (not eligible for BUY2GET1)
        $laptop = Product::where('sku', 'ELEC-001')->first();

        // Create cart with mixed categories
        $cartItems = collect([
            new CartItem($tshirt, 1, $tshirt->price),
            new CartItem($jeans, 1, $jeans->price),
            new CartItem($laptop, 1, $laptop->price),
        ]);

        // Get promotion (only for clothing)
        $promotion = Promotion::where('code', 'BUY2GET1')->first();

        // Apply promotion
        $engine = app(PromotionEngine::class);
        $result = $engine->applyPromotion($cartItems, $promotion);

        // Assertions
        $this->assertTrue($result->isApplied());
        $this->assertEquals(19.99, $result->discountAmount); // Only clothing items eligible
    }

    public function test_multiple_sets_qualification()
    {
        // Get clothing products
        $tshirt = Product::where('sku', 'CLO-001')->first();
        $jeans = Product::where('sku', 'CLO-002')->first();
        $sneakers = Product::where('sku', 'CLO-003')->first();

        // Create cart with 6 items (3 sets of buy 2)
        $cartItems = collect([
            new CartItem($tshirt, 2, $tshirt->price),
            new CartItem($jeans, 2, $jeans->price),
            new CartItem($sneakers, 2, $sneakers->price),
        ]);

        // Get promotion
        $promotion = Promotion::where('code', 'BUY2GET1')->first();

        // Apply promotion
        $engine = app(PromotionEngine::class);
        $result = $engine->applyPromotion($cartItems, $promotion);

        // Assertions
        $this->assertTrue($result->isApplied());
        $this->assertEquals(3, $result->metadata['sets_qualified']);
        $this->assertEquals(3, $result->metadata['total_free_items']);
        
        // With 6 items total, 3 free items should be given
        // The strategy applies to cheapest items first
        // 2 T-shirts ($19.99 each) + 1 Jeans ($49.99) = $89.97 total discount
        $expectedDiscount = (2 * 19.99) + 49.99; // 2 T-shirts + 1 Jeans
        $this->assertEquals($expectedDiscount, $result->discountAmount);
        
        // Free items are grouped by product, so we get 2 entries (T-shirt with qty 2, Jeans with qty 1)
        $this->assertGreaterThanOrEqual(2, count($result->freeItems));
    }
}

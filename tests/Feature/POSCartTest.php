<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class POSCartTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_pos_cart_integration_with_promotions()
    {
        $cartService = app(CartService::class);

        // Add products to cart
        $tshirt = Product::where('sku', 'CLO-001')->first();
        $jeans = Product::where('sku', 'CLO-002')->first();
        $sneakers = Product::where('sku', 'CLO-003')->first();

        $cartService->addItem($tshirt, 1);
        $cartService->addItem($jeans, 1);
        $cartService->addItem($sneakers, 1);

        // Get cart with promotions applied
        $cartData = $cartService->getCartWithPromotions();

        // Verify cart structure
        $this->assertArrayHasKey('items', $cartData);
        $this->assertArrayHasKey('subtotal', $cartData);
        $this->assertArrayHasKey('discount', $cartData);
        $this->assertArrayHasKey('total', $cartData);
        $this->assertArrayHasKey('promotions', $cartData);
        $this->assertArrayHasKey('free_items', $cartData);

        // Verify items are in cart
        $this->assertCount(3, $cartData['items']);

        // Verify promotion was applied
        $this->assertGreaterThan(0, $cartData['discount']);
        $this->assertLessThan($cartData['subtotal'], $cartData['total']); // Total should be less due to discount

        // Verify promotion details
        $this->assertNotEmpty($cartData['promotions']);
        $appliedPromotion = collect($cartData['promotions'])->first(fn($p) => $p['applied']);
        $this->assertNotNull($appliedPromotion);
        $this->assertEquals('Buy 2 Get 1 Free - Clothing', $appliedPromotion['promotion_name']);
    }

    public function test_cart_quantity_updates()
    {
        $cartService = app(CartService::class);
        $tshirt = Product::where('sku', 'CLO-001')->first();

        // Add item
        $cartService->addItem($tshirt, 1);
        $cartData = $cartService->getCartWithPromotions();
        $this->assertEquals(1, $cartData['items'][0]['quantity']);

        // Update quantity
        $cartService->updateQuantity($tshirt, 3);
        $cartData = $cartService->getCartWithPromotions();
        $this->assertEquals(3, $cartData['items'][0]['quantity']);
    }

    public function test_cart_item_removal()
    {
        $cartService = app(CartService::class);
        $tshirt = Product::where('sku', 'CLO-001')->first();
        $jeans = Product::where('sku', 'CLO-002')->first();

        // Add items
        $cartService->addItem($tshirt, 1);
        $cartService->addItem($jeans, 1);
        $cartData = $cartService->getCartWithPromotions();
        $this->assertCount(2, $cartData['items']);

        // Remove item
        $cartService->removeItem($tshirt);
        $cartData = $cartService->getCartWithPromotions();
        $this->assertCount(1, $cartData['items']);
        $this->assertEquals($jeans->id, $cartData['items'][0]['product_id']);
    }

    public function test_cart_clearing()
    {
        $cartService = app(CartService::class);
        $tshirt = Product::where('sku', 'CLO-001')->first();

        // Add item and verify
        $cartService->addItem($tshirt, 1);
        $cartData = $cartService->getCartWithPromotions();
        $this->assertCount(1, $cartData['items']);

        // Clear cart and verify
        $cartService->clear();
        $cartData = $cartService->getCartWithPromotions();
        $this->assertCount(0, $cartData['items']);
        $this->assertEquals(0, $cartData['subtotal']);
        $this->assertEquals(0, $cartData['discount']);
        $this->assertEquals(0, $cartData['total']);
    }
}

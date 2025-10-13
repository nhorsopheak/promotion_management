<?php

namespace App\Services;

use App\DTOs\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Services\Promotions\PromotionEngine;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected PromotionEngine $promotionEngine;
    protected string $sessionKey = 'shopping_cart';

    public function __construct(PromotionEngine $promotionEngine)
    {
        $this->promotionEngine = $promotionEngine;
    }

    /**
     * Get cart items from session
     */
    public function getCartItems(): Collection
    {
        $cartData = Session::get($this->sessionKey, []);
        $items = collect();

        foreach ($cartData as $productId => $data) {
            $product = Product::find($productId);
            
            if ($product && $product->is_active) {
                $items->push(new CartItem(
                    product: $product,
                    quantity: $data['quantity'],
                    price: $product->price,
                ));
            }
        }

        return $items;
    }

    /**
     * Add item to cart
     */
    public function addItem(Product $product, int $quantity = 1): void
    {
        if (!$product->is_active) {
            throw new \Exception('Product is not available');
        }

        if ($product->track_inventory && $product->stock_quantity < $quantity) {
            throw new \Exception('Insufficient stock');
        }

        $cart = Session::get($this->sessionKey, []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $quantity;
        } else {
            $cart[$product->id] = [
                'quantity' => $quantity,
                'added_at' => now()->toDateTimeString(),
            ];
        }

        Session::put($this->sessionKey, $cart);
    }

    /**
     * Update item quantity
     */
    public function updateQuantity(Product $product, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->removeItem($product);
            return;
        }

        if ($product->track_inventory && $product->stock_quantity < $quantity) {
            throw new \Exception('Insufficient stock');
        }

        $cart = Session::get($this->sessionKey, []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] = $quantity;
            Session::put($this->sessionKey, $cart);
        }
    }

    /**
     * Remove item from cart
     */
    public function removeItem(Product $product): void
    {
        $cart = Session::get($this->sessionKey, []);
        unset($cart[$product->id]);
        Session::put($this->sessionKey, $cart);
    }

    /**
     * Clear cart
     */
    public function clear(): void
    {
        Session::forget($this->sessionKey);
    }

    /**
     * Get cart with promotions applied
     */
    public function getCartWithPromotions(?User $user = null): array
    {
        $cartItems = $this->getCartItems();

        if ($cartItems->isEmpty()) {
            return [
                'items' => [],
                'subtotal' => 0,
                'discount' => 0,
                'total' => 0,
                'promotions' => [],
                'free_items' => [],
            ];
        }

        // Apply promotions
        $promotionResults = $this->promotionEngine->applyPromotions($cartItems, $user);

        // Calculate totals
        $subtotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);
        $discount = $this->promotionEngine->calculateTotalDiscount($promotionResults);
        $total = max(0, $subtotal - $discount);

        return [
            'items' => $cartItems->map(fn($item) => $item->toArray())->toArray(),
            'subtotal' => round($subtotal, 2),
            'discount' => round($discount, 2),
            'total' => round($total, 2),
            'promotions' => $promotionResults->map(fn($result) => $result->toArray())->toArray(),
            'free_items' => $this->promotionEngine->getAllFreeItems($promotionResults),
        ];
    }

    /**
     * Get cart item count
     */
    public function getItemCount(): int
    {
        return $this->getCartItems()->sum('quantity');
    }

    /**
     * Check if cart is empty
     */
    public function isEmpty(): bool
    {
        return $this->getCartItems()->isEmpty();
    }

    /**
     * Get cart subtotal (before promotions)
     */
    public function getSubtotal(): float
    {
        return $this->getCartItems()->sum(fn($item) => $item->price * $item->quantity);
    }
}

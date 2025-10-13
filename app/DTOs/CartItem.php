<?php

namespace App\DTOs;

use App\Models\Product;

class CartItem
{
    public function __construct(
        public Product $product,
        public int $quantity,
        public float $price,
        public float $discountAmount = 0,
        public ?int $promotionId = null,
        public bool $isFree = false,
        public array $promotionDetails = [],
    ) {}

    public function getFinalPrice(): float
    {
        return max(0, $this->price - $this->discountAmount);
    }

    public function getSubtotal(): float
    {
        return $this->getFinalPrice() * $this->quantity;
    }

    public function getTotalDiscount(): float
    {
        return $this->discountAmount * $this->quantity;
    }

    public function toArray(): array
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_sku' => $this->product->sku,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'discount_amount' => $this->discountAmount,
            'final_price' => $this->getFinalPrice(),
            'subtotal' => $this->getSubtotal(),
            'is_free' => $this->isFree,
            'promotion_id' => $this->promotionId,
            'promotion_details' => $this->promotionDetails,
        ];
    }
}

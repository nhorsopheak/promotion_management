<?php

namespace App\DTOs;

use App\Models\Promotion;

class PromotionResult
{
    public function __construct(
        public Promotion $promotion,
        public bool $applied,
        public float $discountAmount = 0,
        public array $affectedItems = [],
        public array $freeItems = [],
        public ?string $message = null,
        public array $metadata = [],
    ) {}

    public function isApplied(): bool
    {
        return $this->applied;
    }

    public function hasDiscount(): bool
    {
        return $this->discountAmount > 0;
    }

    public function hasFreeItems(): bool
    {
        return !empty($this->freeItems);
    }

    public function toArray(): array
    {
        return [
            'promotion_id' => $this->promotion->id,
            'promotion_code' => $this->promotion->code,
            'promotion_name' => $this->promotion->name,
            'promotion_type' => $this->promotion->type,
            'applied' => $this->applied,
            'discount_amount' => $this->discountAmount,
            'affected_items' => $this->affectedItems,
            'free_items' => $this->freeItems,
            'message' => $this->message,
            'metadata' => $this->metadata,
        ];
    }
}

<?php

namespace App\Services\Promotions\Contracts;

use App\DTOs\PromotionResult;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Support\Collection;

interface PromotionStrategyInterface
{
    /**
     * Check if the promotion conditions are met
     */
    public function isEligible(Collection $cartItems, ?User $user = null): bool;

    /**
     * Apply the promotion to the cart items
     */
    public function apply(Collection $cartItems, Promotion $promotion, ?User $user = null): PromotionResult;

    /**
     * Get the promotion type this strategy handles
     */
    public function getType(): string;

    /**
     * Validate promotion configuration
     */
    public function validateConfig(array $config): bool;
}

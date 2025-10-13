<?php

namespace App\Services\Promotions\Strategies;

use App\DTOs\CartItem;
use App\DTOs\PromotionResult;
use App\Enums\PromotionType;
use App\Models\Promotion;
use App\Models\User;
use App\Services\Promotions\Contracts\PromotionStrategyInterface;
use Illuminate\Support\Collection;

class StepDiscountStrategy implements PromotionStrategyInterface
{
    public function getType(): string
    {
        return PromotionType::STEP_DISCOUNT->value;
    }

    public function validateConfig(array $config): bool
    {
        return isset($config['discount_tiers']) && is_array($config['discount_tiers']) && !empty($config['discount_tiers']);
    }

    public function isEligible(Collection $cartItems, ?User $user = null): bool
    {
        return $cartItems->count() >= 2; // Need at least 2 items for step discounts
    }

    public function apply(Collection $cartItems, Promotion $promotion, ?User $user = null): PromotionResult
    {
        $conditions = $promotion->conditions ?? [];

        // Get discount tiers (position => percentage)
        // Default: [2 => 20, 3 => 30, 5 => 50]
        $rawTiers = $conditions['discount_tiers'] ?? [
            ['position' => 2, 'percentage' => 20],
            ['position' => 3, 'percentage' => 30],
            ['position' => 5, 'percentage' => 50],
        ];

        // Convert array format to key-value pairs
        $discountTiers = [];
        foreach ($rawTiers as $tier) {
            if (isset($tier['position']) && isset($tier['percentage'])) {
                $discountTiers[(int)$tier['position']] = (float)$tier['percentage'];
            }
        }

        // Apply discounts based on cart item order (not sorted by price)
        $sortedItems = $cartItems;

        $totalDiscount = 0;
        $affectedItems = [];
        $appliedDiscounts = [];

        foreach ($sortedItems as $position => $item) {
            $itemPosition = $position + 1; // 1-indexed position
            // Check if this position has a discount tier
            if (isset($discountTiers[$itemPosition])) {
                 $discountPercentage = $discountTiers[$itemPosition];
                $originalPrice = $item->price;
                $discountAmount = $originalPrice * ($discountPercentage / 100);

                // Apply discount to the item
                $item->discountAmount += $discountAmount;
                $item->promotionId = $promotion->id;

                // Update item details
                $item->promotionDetails = [
                    'type' => $promotion->type,
                    'position' => $itemPosition,
                    'discount_percentage' => $discountPercentage,
                    'original_price' => $originalPrice,
                    'discount_amount' => $discountAmount,
                ];

                $totalDiscount += $discountAmount;
                $affectedItems[] = $item->product->id;

                $appliedDiscounts[] = [
                    'position' => $itemPosition,
                    'product_name' => $item->product->name,
                    'discount_percentage' => $discountPercentage,
                    'original_price' => $originalPrice,
                    'discount_amount' => $discountAmount,
                ];
            }
        }

        if ($totalDiscount <= 0) {
            return new PromotionResult(
                promotion: $promotion,
                applied: false,
                message: 'Not enough items in cart for step discount'
            );
        }

        $message = $this->generateDiscountMessage($appliedDiscounts);

        return new PromotionResult(
            promotion: $promotion,
            applied: true,
            discountAmount: $totalDiscount,
            affectedItems: $affectedItems,
            message: $message,
            metadata: [
                'discount_tiers' => $discountTiers,
                'applied_discounts' => $appliedDiscounts,
                'total_items' => $cartItems->count(),
            ]
        );
    }

    /**
     * Generate a user-friendly message about the applied discounts
     */
    private function generateDiscountMessage(array $appliedDiscounts): string
    {
        $messages = [];
        foreach ($appliedDiscounts as $discount) {
            $messages[] = "{$discount['position']}nd item {$discount['discount_percentage']}% off";
        }

        return 'Step Discount Applied: ' . implode(', ', $messages);
    }
}

<?php

namespace App\Services\Promotions\Strategies;

use App\DTOs\PromotionResult;
use App\Enums\PromotionType;
use App\Models\Promotion;
use App\Models\User;
use App\Services\Promotions\Contracts\PromotionStrategyInterface;
use Illuminate\Support\Collection;

class PercentageDiscountStrategy implements PromotionStrategyInterface
{
    public function getType(): string
    {
        return PromotionType::PERCENTAGE_DISCOUNT->value;
    }

    public function validateConfig(array $config): bool
    {
        return isset($config['discount_type']) 
            && isset($config['discount_value'])
            && $config['discount_value'] > 0;
    }

    public function isEligible(Collection $cartItems, ?User $user = null): bool
    {
        // Will be checked in apply method with specific promotion config
        return true;
    }

    public function apply(Collection $cartItems, Promotion $promotion, ?User $user = null): PromotionResult
    {
        $conditions = $promotion->conditions ?? [];
        
        // Get configuration
        $discountType = $conditions['discount_type'] ?? 'percentage';
        $discountValue = $conditions['discount_value'] ?? 0;
        $eligibleProductIds = $conditions['eligible_product_ids'] ?? [];
        $eligibleCategoryIds = $conditions['eligible_category_ids'] ?? [];
        
        // Convert to arrays if needed
        if ($eligibleProductIds && !is_array($eligibleProductIds)) {
            $eligibleProductIds = [$eligibleProductIds];
        }
        if ($eligibleCategoryIds && !is_array($eligibleCategoryIds)) {
            $eligibleCategoryIds = [$eligibleCategoryIds];
        }
        
        // Get eligible items
        $eligibleItems = $this->getEligibleItems($cartItems, $eligibleProductIds, $eligibleCategoryIds);
        
        if ($eligibleItems->isEmpty()) {
            return new PromotionResult(
                promotion: $promotion,
                applied: false,
                message: 'No eligible items in cart'
            );
        }
        
        // Calculate total eligible amount
        $totalEligibleAmount = $eligibleItems->sum(fn($item) => $item->price * $item->quantity);
        
        if ($totalEligibleAmount <= 0) {
            return new PromotionResult(
                promotion: $promotion,
                applied: false,
                message: 'No eligible amount to discount'
            );
        }
        
        // Calculate discount amount
        $discountAmount = $this->calculateDiscount($totalEligibleAmount, $discountType, $discountValue);
        
        if ($discountAmount <= 0) {
            return new PromotionResult(
                promotion: $promotion,
                applied: false,
                message: 'No discount calculated'
            );
        }
        
        // Apply discount proportionally to eligible items
        $affectedItems = [];
        $remainingDiscount = $discountAmount;
        
        foreach ($eligibleItems as $item) {
            if ($remainingDiscount <= 0) {
                break;
            }
            
            $itemTotal = $item->price * $item->quantity;
            $itemDiscount = min($remainingDiscount, $itemTotal);
            
            // Calculate discount per unit
            $discountPerUnit = $itemDiscount / $item->quantity;
            
            $item->discountAmount += $discountPerUnit;
            $item->promotionId = $promotion->id;
            $item->promotionDetails = [
                'type' => $promotion->type,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
            ];
            
            $affectedItems[] = $item->product->id;
            $remainingDiscount -= $itemDiscount;
        }
        
        $message = $this->generatePromotionMessage($discountType, $discountValue, $discountAmount);
        
        return new PromotionResult(
            promotion: $promotion,
            applied: true,
            discountAmount: $discountAmount,
            affectedItems: $affectedItems,
            message: $message,
            metadata: [
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'total_eligible_amount' => $totalEligibleAmount,
            ]
        );
    }
    
    /**
     * Filter cart items based on eligible products and categories
     */
    private function getEligibleItems(
        Collection $cartItems, 
        array $eligibleProductIds, 
        array $eligibleCategoryIds
    ): Collection {
        return $cartItems->filter(function ($item) use ($eligibleProductIds, $eligibleCategoryIds) {
            // If no restrictions, all items are eligible
            if (empty($eligibleProductIds) && empty($eligibleCategoryIds)) {
                return true;
            }
            
            // Check if product is in eligible list
            if (!empty($eligibleProductIds) && in_array($item->product->id, $eligibleProductIds)) {
                return true;
            }
            
            // Check if product's category is in eligible list
            if (!empty($eligibleCategoryIds) && $item->product->category_id) {
                if (in_array($item->product->category_id, $eligibleCategoryIds)) {
                    return true;
                }
            }
            
            return false;
        });
    }
    
    /**
     * Calculate discount amount based on type
     */
    private function calculateDiscount(float $amount, string $discountType, float $discountValue): float
    {
        return match($discountType) {
            'percentage' => $amount * ($discountValue / 100),
            'fixed_amount' => min($discountValue, $amount),
            default => 0,
        };
    }
    
    /**
     * Generate promotion message
     */
    private function generatePromotionMessage(string $discountType, float $discountValue, float $discountAmount): string
    {
        $discountText = match($discountType) {
            'percentage' => "{$discountValue}% off",
            'fixed_amount' => "$$discountValue off",
            default => 'discount',
        };
        
        return "{$discountText} applied! You saved \${$discountAmount}.";
    }
}

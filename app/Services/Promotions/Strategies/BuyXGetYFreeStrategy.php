<?php

namespace App\Services\Promotions\Strategies;

use App\DTOs\CartItem;
use App\DTOs\PromotionResult;
use App\Enums\PromotionType;
use App\Models\Promotion;
use App\Models\User;
use App\Services\Promotions\Contracts\PromotionStrategyInterface;
use Illuminate\Support\Collection;

class BuyXGetYFreeStrategy implements PromotionStrategyInterface
{
    public function getType(): string
    {
        return PromotionType::BUY_X_GET_Y_FREE->value;
    }

    public function validateConfig(array $config): bool
    {
        return isset($config['buy_quantity']) 
            && isset($config['get_quantity'])
            && $config['buy_quantity'] > 0
            && $config['get_quantity'] > 0;
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
        $buyQuantity = $conditions['buy_quantity'] ?? 2;
        $getQuantity = $conditions['get_quantity'] ?? 1;
        $applyToType = $conditions['apply_to_type'] ?? 'any';
        $applyToProductIds = $conditions['apply_to_product_ids'] ?? null;
        $applyToCategoryIds = $conditions['apply_to_category_ids'] ?? null;
        $getType = $conditions['get_type'] ?? 'cheapest';
        $getProductIds = $conditions['get_product_ids'] ?? null;
        $applyToCheapest = $conditions['apply_to_cheapest'] ?? true;

        // Convert single values to arrays for backward compatibility
        if ($applyToProductIds && !is_array($applyToProductIds)) {
            $applyToProductIds = [$applyToProductIds];
        }
        if ($applyToCategoryIds && !is_array($applyToCategoryIds)) {
            $applyToCategoryIds = [$applyToCategoryIds];
        }
        if ($getProductIds && !is_array($getProductIds)) {
            $getProductIds = [$getProductIds];
        }

        // Get eligible items for buying (qualification)
        $eligibleBuyItems = $this->getEligibleBuyItems(
            $cartItems, 
            $applyToType,
            $applyToProductIds, 
            $applyToCategoryIds
        );

        if ($eligibleBuyItems->isEmpty()) {
            return new PromotionResult(
                promotion: $promotion,
                applied: false,
                message: 'No eligible items in cart for buying'
            );
        }

        // Calculate total eligible buy quantity
        $totalEligibleBuyQuantity = $eligibleBuyItems->sum(fn($item) => $item->quantity);

        // Check if minimum buy quantity is met
        if ($totalEligibleBuyQuantity < $buyQuantity) {
            return new PromotionResult(
                promotion: $promotion,
                applied: false,
                message: "Need to buy at least {$buyQuantity} eligible items"
            );
        }

        // Calculate how many free items can be given
        $setsQualified = floor($totalEligibleBuyQuantity / $buyQuantity);
        $totalFreeItems = $setsQualified * $getQuantity;

        if ($totalFreeItems <= 0) {
            return new PromotionResult(
                promotion: $promotion,
                applied: false,
                message: 'Not enough items to qualify for free items'
            );
        }

        // Get eligible items for getting free (based on get_type)
        $eligibleFreeItems = $this->getEligibleFreeItems(
            $cartItems,
            $getType,
            $getProductIds,
            $applyToType,
            $applyToProductIds,
            $applyToCategoryIds
        );

        if ($eligibleFreeItems->isEmpty()) {
            return new PromotionResult(
                promotion: $promotion,
                applied: false,
                message: 'No eligible items in cart for getting free'
            );
        }

        // Sort free items based on strategy
        $sortedFreeItems = $this->sortFreeItems($eligibleFreeItems, $getType, $applyToCheapest);

        // Apply free items
        $freeItems = [];
        $affectedItems = [];
        $remainingFreeItems = $totalFreeItems;
        $totalDiscount = 0;

        foreach ($sortedFreeItems as $item) {
            if ($remainingFreeItems <= 0) {
                break;
            }

            $freeQuantity = min($remainingFreeItems, $item->quantity);
            
            if ($freeQuantity > 0) {
                $discountPerItem = $item->price;
                $itemDiscount = $discountPerItem * $freeQuantity;
                
                $freeItems[] = [
                    'product_id' => $item->product->id,
                    'product_name' => $item->product->name,
                    'quantity' => $freeQuantity,
                    'original_price' => $item->price,
                    'discount_amount' => $discountPerItem,
                ];

                $affectedItems[] = $item->product->id;
                $totalDiscount += $itemDiscount;
                $remainingFreeItems -= $freeQuantity;

                // Update the cart item with discount
                $item->discountAmount += $discountPerItem;
                $item->promotionId = $promotion->id;
                $item->isFree = ($freeQuantity === $item->quantity);
                $item->promotionDetails = [
                    'type' => $promotion->type,
                    'free_quantity' => $freeQuantity,
                    'buy_quantity' => $buyQuantity,
                    'get_quantity' => $getQuantity,
                    'apply_to_type' => $applyToType,
                    'get_type' => $getType,
                ];
            }
        }

        $message = $this->generatePromotionMessage($buyQuantity, $getQuantity, $setsQualified, $totalFreeItems, $applyToType, $getType);

        return new PromotionResult(
            promotion: $promotion,
            applied: true,
            discountAmount: $totalDiscount,
            affectedItems: $affectedItems,
            freeItems: $freeItems,
            message: $message,
            metadata: [
                'buy_quantity' => $buyQuantity,
                'get_quantity' => $getQuantity,
                'sets_qualified' => $setsQualified,
                'total_free_items' => $totalFreeItems,
                'apply_to_type' => $applyToType,
                'get_type' => $getType,
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
     * Get items eligible for buying (qualification)
     */
    private function getEligibleBuyItems(
        Collection $cartItems,
        string $applyToType,
        array $applyToProductIds,
        array $applyToCategoryIds
    ): Collection {
        return match($applyToType) {
            'specific_products' => $this->getEligibleItems($cartItems, $applyToProductIds, []),
            'specific_categories' => $this->getEligibleItems($cartItems, [], $applyToCategoryIds),
            'any' => $cartItems, // All items are eligible
            default => $cartItems,
        };
    }

    /**
     * Get items eligible for getting free
     */
    private function getEligibleFreeItems(
        Collection $cartItems,
        string $getType,
        array $getProductIds,
        string $applyToType,
        array $applyToProductIds,
        array $applyToCategoryIds
    ): Collection {
        return match($getType) {
            'specific_products' => $this->getEligibleItems($cartItems, $getProductIds, []),
            'cheapest' => $this->getEligibleBuyItems($cartItems, $applyToType, $applyToProductIds, $applyToCategoryIds),
            default => $cartItems,
        };
    }

    /**
     * Sort items for getting free based on strategy
     */
    private function sortFreeItems(Collection $items, string $getType, bool $applyToCheapest): Collection
    {
        if ($getType === 'specific_products') {
            // For specific products, sort by price ascending (cheapest first)
            return $items->sortBy('price');
        }

        // For cheapest strategy, sort by price
        return $applyToCheapest 
            ? $items->sortBy('price')
            : $items->sortByDesc('price');
    }

    /**
     * Generate promotion message based on configuration
     */
    private function generatePromotionMessage(
        int $buyQuantity, 
        int $getQuantity, 
        int $setsQualified, 
        int $totalFreeItems,
        string $applyToType,
        string $getType
    ): string {
        $buyText = match($applyToType) {
            'specific_products' => "{$buyQuantity} specific products",
            'specific_categories' => "{$buyQuantity} products from specific categories",
            'any' => "{$buyQuantity} items",
            default => "{$buyQuantity} items",
        };

        $getText = match($getType) {
            'specific_products' => "{$getQuantity} specific product(s) free",
            'cheapest' => "{$getQuantity} cheapest item(s) free",
            default => "{$getQuantity} item(s) free",
        };

        return "Buy {$buyText}, Get {$getText}! {$totalFreeItems} free item(s) added.";
    }
}

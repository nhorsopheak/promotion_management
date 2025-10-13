<?php

namespace App\Services\Promotions\Strategies;

use App\DTOs\CartItem;
use App\DTOs\PromotionResult;
use App\Enums\PromotionType;
use App\Models\Promotion;
use App\Models\User;
use App\Services\Promotions\Contracts\PromotionStrategyInterface;
use Illuminate\Support\Collection;

class FixedPriceBundleStrategy implements PromotionStrategyInterface
{
    public function getType(): string
    {
        return PromotionType::FIXED_PRICE_BUNDLE->value;
    }

    public function validateConfig(array $config): bool
    {
        return isset($config['bundle_quantity']) 
            && isset($config['bundle_price'])
            && $config['bundle_quantity'] > 0
            && $config['bundle_price'] > 0;
    }

    public function isEligible(Collection $cartItems, ?User $user = null): bool
    {
        return $cartItems->count() >= 1;
    }

    public function apply(Collection $cartItems, Promotion $promotion, ?User $user = null): PromotionResult
    {
        $conditions = $promotion->conditions ?? [];

        // Get configuration
        $bundleQuantity = $conditions['bundle_quantity'] ?? 3;
        $bundlePrice = $conditions['bundle_price'] ?? 30.00;
        $eligibleProductIds = $conditions['eligible_product_ids'] ?? null;
        $eligibleCategoryIds = $conditions['eligible_category_ids'] ?? null;

        // Convert single values to arrays
        if ($eligibleProductIds && !is_array($eligibleProductIds)) {
            $eligibleProductIds = [$eligibleProductIds];
        }
        if ($eligibleCategoryIds && !is_array($eligibleCategoryIds)) {
            $eligibleCategoryIds = [$eligibleCategoryIds];
        }

        // Filter eligible items
        $eligibleItems = $this->getEligibleItems(
            $cartItems,
            $eligibleProductIds,
            $eligibleCategoryIds
        );

        if ($eligibleItems->isEmpty()) {
            return new PromotionResult(
                promotion: $promotion,
                applied: false,
                message: 'No eligible items for bundle'
            );
        }

        // Calculate total eligible quantity
        $totalEligibleQuantity = $eligibleItems->sum(fn($item) => $item->quantity);

        // Check if bundle quantity is met
        if ($totalEligibleQuantity < $bundleQuantity) {
            return new PromotionResult(
                promotion: $promotion,
                applied: false,
                message: "Need to buy at least {$bundleQuantity} eligible items for bundle"
            );
        }

        // Calculate how many complete bundles
        $completeBundles = floor($totalEligibleQuantity / $bundleQuantity);

        if ($completeBundles <= 0) {
            return new PromotionResult(
                promotion: $promotion,
                applied: false,
                message: 'Not enough items to form a bundle'
            );
        }

        // Calculate original price of items in bundles
        $itemsInBundles = [];
        $remainingQuantity = $completeBundles * $bundleQuantity;
        $originalBundlePrice = 0;

        foreach ($eligibleItems as $item) {
            if ($remainingQuantity <= 0) {
                break;
            }

            $quantityToUse = min($remainingQuantity, $item->quantity);
            $itemsInBundles[] = [
                'item' => $item,
                'quantity' => $quantityToUse,
                'original_price' => $item->price,
                'total_original' => $item->price * $quantityToUse,
            ];

            $originalBundlePrice += $item->price * $quantityToUse;
            $remainingQuantity -= $quantityToUse;
        }

        // Calculate total bundle price
        $totalBundlePrice = $completeBundles * $bundlePrice;
        $totalDiscount = $originalBundlePrice - $totalBundlePrice;

        if ($totalDiscount <= 0) {
            return new PromotionResult(
                promotion: $promotion,
                applied: false,
                message: 'Bundle price is higher than original price'
            );
        }

        // Apply proportional discount to each item
        $affectedItems = [];
        $appliedDiscounts = [];

        foreach ($itemsInBundles as $bundleItem) {
            $item = $bundleItem['item'];
            $quantityInBundle = $bundleItem['quantity'];
            $itemOriginalTotal = $bundleItem['total_original'];

            // Calculate proportional discount
            $proportionalDiscount = ($itemOriginalTotal / $originalBundlePrice) * $totalDiscount;
            $discountPerItem = $proportionalDiscount / $quantityInBundle;

            // Apply discount
            $item->discountAmount += $discountPerItem;
            $item->promotionId = $promotion->id;
            $item->promotionDetails = [
                'type' => $promotion->type,
                'bundle_quantity' => $bundleQuantity,
                'bundle_price' => $bundlePrice,
                'bundles_count' => $completeBundles,
                'proportional_discount' => $proportionalDiscount,
            ];

            $affectedItems[] = $item->product->id;

            $appliedDiscounts[] = [
                'product_name' => $item->product->name,
                'quantity' => $quantityInBundle,
                'original_price' => $item->price,
                'discount_amount' => $discountPerItem,
                'proportional_discount' => $proportionalDiscount,
            ];
        }

        $message = "Buy {$bundleQuantity} for \${$bundlePrice}! {$completeBundles} bundle(s) applied. Saved \${$totalDiscount}";

        return new PromotionResult(
            promotion: $promotion,
            applied: true,
            discountAmount: $totalDiscount,
            affectedItems: $affectedItems,
            message: $message,
            metadata: [
                'bundle_quantity' => $bundleQuantity,
                'bundle_price' => $bundlePrice,
                'complete_bundles' => $completeBundles,
                'original_bundle_price' => $originalBundlePrice,
                'total_bundle_price' => $totalBundlePrice,
                'applied_discounts' => $appliedDiscounts,
            ]
        );
    }

    /**
     * Filter cart items based on eligible products and categories
     */
    private function getEligibleItems(
        Collection $cartItems,
        ?array $eligibleProductIds,
        ?array $eligibleCategoryIds
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
}

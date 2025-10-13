<?php

namespace App\Services\Promotions;

use App\DTOs\PromotionResult;
use App\Models\Promotion;
use App\Models\User;
use App\Services\Promotions\Contracts\PromotionStrategyInterface;
use App\Services\Promotions\Strategies\BuyXGetYFreeStrategy;
use App\Services\Promotions\Strategies\StepDiscountStrategy;
use App\Services\Promotions\Strategies\FixedPriceBundleStrategy;
use App\Services\Promotions\Strategies\PercentageDiscountStrategy;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PromotionEngine
{
    protected array $strategies = [];

    public function __construct()
    {
        $this->registerStrategies();
    }

    /**
     * Register all promotion strategies
     */
    protected function registerStrategies(): void
    {
        $this->registerStrategy(new BuyXGetYFreeStrategy());
        $this->registerStrategy(new StepDiscountStrategy());
        $this->registerStrategy(new FixedPriceBundleStrategy());
        $this->registerStrategy(new PercentageDiscountStrategy());
        // Add more strategies here as they are implemented
        // $this->registerStrategy(new PriceResetRangeStrategy());
        // etc...
    }

    /**
     * Register a promotion strategy
     */
    public function registerStrategy(PromotionStrategyInterface $strategy): void
    {
        $this->strategies[$strategy->getType()] = $strategy;
    }

    /**
     * Get strategy for a promotion type
     */
    public function getStrategy(string $type): ?PromotionStrategyInterface
    {
        return $this->strategies[$type] ?? null;
    }

    /**
     * Apply all eligible promotions to cart items
     */
    public function applyPromotions(Collection $cartItems, ?User $user = null): Collection
    {
        $results = collect();

        // Get active promotions ordered by priority
        $promotions = Promotion::active()
            ->byPriority()
            ->get();

        foreach ($promotions as $promotion) {
            // Check if promotion is valid for current time
            if (!$promotion->isValidForTime()) {
                continue;
            }

            // Check membership requirements
            if ($promotion->requires_membership) {
                if (!$user || !$user->isMember()) {
                    continue;
                }

                if ($promotion->membership_tiers && !empty($promotion->membership_tiers)) {
                    if (!in_array($user->membership_tier, $promotion->membership_tiers)) {
                        continue;
                    }
                }
            }

            // Check usage limits
            if ($promotion->usage_limit && $promotion->usage_count >= $promotion->usage_limit) {
                continue;
            }

            // Check if user has already used this promotion
            if ($promotion->usage_limit_per_user && $user && $promotion->usage_count_by_user($user->id) >= $promotion->usage_limit_per_user) {
                continue;
            }

            // Get strategy for this promotion type
            $strategy = $this->getStrategy($promotion->type);

            if (!$strategy) {
                Log::warning("No strategy found for promotion type: {$promotion->type}");
                continue;
            }

            // Check if eligible
            if (!$strategy->isEligible($cartItems, $user)) {
                continue;
            }

            // Apply promotion
            try {
                $result = $strategy->apply($cartItems, $promotion, $user);
                
                if ($result->isApplied()) {
                    $results->push($result);
                    
                    // If promotion cannot stack, stop here
                    // Removed: can_stack feature not implemented
                    // if (!$promotion->can_stack) {
                    //     break;
                    // }
                }
            } catch (\Exception $e) {
                Log::error("Error applying promotion {$promotion->id}: {$e->getMessage()}");
                continue;
            }
        }

        return $results;
    }

    /**
     * Apply a specific promotion to cart items
     */
    public function applyPromotion(
        Collection $cartItems, 
        Promotion $promotion, 
        ?User $user = null
    ): PromotionResult {
        // Check if promotion is active
        if (!$promotion->isActive()) {
            return new PromotionResult(
                promotion: $promotion,
                applied: false,
                message: 'Promotion is not active'
            );
        }

        // Check if promotion is valid for current time
        if (!$promotion->isValidForTime()) {
            return new PromotionResult(
                promotion: $promotion,
                applied: false,
                message: 'Promotion is not valid at this time'
            );
        }

        // Check membership requirements
        if ($promotion->requires_membership) {
            if (!$user || !$user->isMember()) {
                return new PromotionResult(
                    promotion: $promotion,
                    applied: false,
                    message: 'This promotion requires membership'
                );
            }

            if ($promotion->membership_tiers && !empty($promotion->membership_tiers)) {
                if (!in_array($user->membership_tier, $promotion->membership_tiers)) {
                    return new PromotionResult(
                        promotion: $promotion,
                        applied: false,
                        message: 'Your membership tier is not eligible for this promotion'
                    );
                }
            }
        }

        // Get strategy
        $strategy = $this->getStrategy($promotion->type);

        if (!$strategy) {
            return new PromotionResult(
                promotion: $promotion,
                applied: false,
                message: 'Promotion type not supported'
            );
        }

        // Apply promotion
        try {
            return $strategy->apply($cartItems, $promotion, $user);
        } catch (\Exception $e) {
            Log::error("Error applying promotion {$promotion->id}: {$e->getMessage()}");
            
            return new PromotionResult(
                promotion: $promotion,
                applied: false,
                message: 'Error applying promotion: ' . $e->getMessage()
            );
        }
    }

    /**
     * Calculate total discount from promotion results
     */
    public function calculateTotalDiscount(Collection $results): float
    {
        return $results->sum(fn($result) => $result->discountAmount);
    }

    /**
     * Get all free items from promotion results
     */
    public function getAllFreeItems(Collection $results): array
    {
        return $results->flatMap(fn($result) => $result->freeItems)->toArray();
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Promotion;
use App\Models\PromotionLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PromotionStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Active promotions
        $activePromotions = Promotion::active()->count();

        // Total promotions
        $totalPromotions = Promotion::count();

        // Total discount given today
        $todayDiscount = PromotionLog::whereDate('created_at', today())
            ->where('action', 'applied')
            ->sum('discount_amount');

        // Total discount this month
        $thisMonthDiscount = PromotionLog::where('created_at', '>=', now()->startOfMonth())
            ->where('action', 'applied')
            ->sum('discount_amount');

        // Most used promotion this month
        $mostUsedPromotion = PromotionLog::selectRaw('promotion_id, COUNT(*) as usage_count')
            ->where('created_at', '>=', now()->startOfMonth())
            ->where('action', 'applied')
            ->groupBy('promotion_id')
            ->orderBy('usage_count', 'desc')
            ->with('promotion')
            ->first();

        $mostUsedName = $mostUsedPromotion ? $mostUsedPromotion->promotion->name : 'None';
        $mostUsedCount = $mostUsedPromotion ? $mostUsedPromotion->usage_count : 0;

        return [
            Stat::make('Active Promotions', $activePromotions)
                ->description('Out of ' . $totalPromotions . ' total promotions')
                ->descriptionIcon('heroicon-m-tag')
                ->color('success'),

            Stat::make('Today\'s Discount', '$' . number_format($todayDiscount, 2))
                ->description('Total discount given today')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info'),

            Stat::make('Monthly Discount', '$' . number_format($thisMonthDiscount, 2))
                ->description('Most used: ' . $mostUsedName . ' (' . $mostUsedCount . ' times)')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning'),
        ];
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Promotion;
use App\Models\PromotionLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $today = today();
        $thisMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        // Today's sales
        $todaySales = Order::whereDate('created_at', $today)->sum('total');

        // This month's sales
        $thisMonthSales = Order::where('created_at', '>=', $thisMonth)->sum('total');

        // Last month's sales
        $lastMonthSales = Order::whereBetween('created_at', [
            $lastMonth,
            $lastMonth->copy()->endOfMonth()
        ])->sum('total');

        // Month over month growth
        $growth = $lastMonthSales > 0 ? (($thisMonthSales - $lastMonthSales) / $lastMonthSales) * 100 : 0;

        // Today's orders
        $todayOrders = Order::whereDate('created_at', $today)->count();

        // Total orders this month
        $thisMonthOrders = Order::where('created_at', '>=', $thisMonth)->count();

        // Average order value
        $avgOrderValue = $thisMonthOrders > 0 ? $thisMonthSales / $thisMonthOrders : 0;

        return [
            Stat::make('Today\'s Sales', '$' . number_format($todaySales, 2))
                ->description('Sales for today')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('This Month', '$' . number_format($thisMonthSales, 2))
                ->description($growth >= 0 ? '+' . number_format($growth, 1) . '% from last month' : number_format($growth, 1) . '% from last month')
                ->descriptionIcon($growth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($growth >= 0 ? 'success' : 'danger'),

            Stat::make('Average Order Value', '$' . number_format($avgOrderValue, 2))
                ->description($thisMonthOrders . ' orders this month')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info'),
        ];
    }
}

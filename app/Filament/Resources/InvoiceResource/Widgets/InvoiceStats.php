<?php

namespace App\Filament\Resources\InvoiceResource\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class InvoiceStats extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        // Today's stats
        $todayOrders = Order::whereDate('created_at', $today)->get();
        $todayRevenue = $todayOrders->sum('total');
        $todayCount = $todayOrders->count();

        // This week's stats
        $weekOrders = Order::where('created_at', '>=', $thisWeek)->get();
        $weekRevenue = $weekOrders->sum('total');
        $weekCount = $weekOrders->count();

        // This month's stats
        $monthOrders = Order::where('created_at', '>=', $thisMonth)->get();
        $monthRevenue = $monthOrders->sum('total');
        $monthCount = $monthOrders->count();

        // Total discount given
        $totalDiscount = Order::sum('discount_amount');

        // Free items count
        $freeItemsCount = \App\Models\OrderItem::where('is_free', true)->sum('quantity');

        // Average order value
        $avgOrderValue = Order::where('status', 'completed')->avg('total') ?? 0;

        return [
            Stat::make('Today\'s Sales', '$' . number_format($todayRevenue, 2))
                ->description($todayCount . ' orders today')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->chart($this->getRevenueChart(7))
                ->color('success'),

            Stat::make('This Week', '$' . number_format($weekRevenue, 2))
                ->description($weekCount . ' orders this week')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->chart($this->getRevenueChart(7))
                ->color('info'),

            Stat::make('This Month', '$' . number_format($monthRevenue, 2))
                ->description($monthCount . ' orders this month')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->chart($this->getRevenueChart(30))
                ->color('warning'),

            Stat::make('Total Discounts', '$' . number_format($totalDiscount, 2))
                ->description($freeItemsCount . ' free items given')
                ->descriptionIcon('heroicon-m-gift')
                ->color('danger'),

            Stat::make('Avg Order Value', '$' . number_format($avgOrderValue, 2))
                ->description('Per transaction')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('primary'),

            Stat::make('Conversion Rate', $this->getConversionRate() . '%')
                ->description('Completed orders')
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color('success'),
        ];
    }

    protected function getRevenueChart(int $days): array
    {
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $revenue = Order::whereDate('created_at', $date)->sum('total');
            $data[] = $revenue;
        }
        return $data;
    }

    protected function getConversionRate(): float
    {
        $total = Order::count();
        if ($total === 0) {
            return 0;
        }
        $completed = Order::where('status', 'completed')->count();
        return round(($completed / $total) * 100, 1);
    }

    protected static ?string $pollingInterval = '30s';
}

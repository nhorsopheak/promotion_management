<?php

namespace App\Filament\Widgets;

use App\Models\PromotionLog;
use Filament\Widgets\ChartWidget;

class PromotionPerformanceChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Promotion Performance';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        // Get top 5 most used promotions this month
        $topPromotions = PromotionLog::selectRaw('promotion_id, COUNT(*) as usage_count, SUM(discount_amount) as total_discount')
            ->where('created_at', '>=', now()->startOfMonth())
            ->where('action', 'applied')
            ->groupBy('promotion_id')
            ->orderBy('usage_count', 'desc')
            ->limit(5)
            ->with('promotion')
            ->get();

        $labels = $topPromotions->map(fn($log) => $log->promotion->name ?? 'Unknown')->toArray();
        $usageData = $topPromotions->map(fn($log) => $log->usage_count)->toArray();
        $discountData = $topPromotions->map(fn($log) => $log->total_discount)->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Usage Count',
                    'data' => $usageData,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.8)',
                    'borderColor' => 'rgb(54, 162, 235)',
                    'borderWidth' => 1,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Total Discount ($)',
                    'data' => $discountData,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.8)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'borderWidth' => 1,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => 'Usage Count',
                    ],
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Total Discount ($)',
                    ],
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                    'ticks' => [
                        'callback' => 'function(value) { return "$" + value.toFixed(2); }',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
        ];
    }
}

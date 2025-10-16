<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class POSQuickAccess extends Widget
{
    protected static string $view = 'filament.widgets.pos-quick-access';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = -1;
    
    public static function canView(): bool
    {
        return auth()->check();
    }
}

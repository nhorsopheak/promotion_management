<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'Order #' . $this->record->order_number;
    }

    protected function getViewData(): array
    {
        return [
            'record' => $this->record->load(['items.product', 'user', 'promotionLogs.promotion']),
        ];
    }
}

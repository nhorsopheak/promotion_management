<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Models\Order;
use Filament\Resources\Pages\Page;

class PrintInvoice extends Page
{
    protected static string $resource = InvoiceResource::class;

    protected static string $view = 'filament.resources.invoice-resource.pages.print-invoice';

    public Order $record;

    public function mount($record): void
    {
        $this->record = Order::with(['items.product', 'user'])->findOrFail($record);
    }

    public function getTitle(): string
    {
        return '';
    }

    public function getRegularItems()
    {
        return $this->record->items->where('is_free', false);
    }

    public function getFreeItems()
    {
        return $this->record->items->where('is_free', true);
    }

    public function hasPromotion(): bool
    {
        return $this->record->discount_amount > 0 || $this->getFreeItems()->count() > 0;
    }
}

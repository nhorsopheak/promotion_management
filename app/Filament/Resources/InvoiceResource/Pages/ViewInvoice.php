<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Models\Order;
use Filament\Resources\Pages\Page;
use Filament\Actions;

class ViewInvoice extends Page
{
    protected static string $resource = InvoiceResource::class;

    protected static string $view = 'filament.resources.invoice-resource.pages.view-invoice';

    public Order $record;

    public function mount($record): void
    {
        $this->record = Order::with(['items.product', 'user'])->findOrFail($record);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label('Print Invoice')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->extraAttributes([
                    'onclick' => 'window.print()',
                ]),

            Actions\Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    // For now, show a notification that PDF export requires additional setup
                    \Filament\Notifications\Notification::make()
                        ->title('PDF Export')
                        ->body('PDF export requires barryvdh/laravel-dompdf package. Install with: composer require barryvdh/laravel-dompdf')
                        ->info()
                        ->send();
                }),

            Actions\Action::make('back')
                ->label('Back to List')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(InvoiceResource::getUrl('index')),
        ];
    }

    public function getTitle(): string
    {
        return 'Invoice #' . $this->record->order_number;
    }

    public function getSubheading(): ?string
    {
        return 'Invoice Date: ' . $this->record->created_at->format('F d, Y');
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

    public function getPromotionDetails()
    {
        if ($this->record->applied_promotions) {
            return $this->record->applied_promotions;
        }
        return [];
    }
}

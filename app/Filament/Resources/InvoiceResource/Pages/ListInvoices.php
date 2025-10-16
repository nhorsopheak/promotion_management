<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action for invoices
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            InvoiceResource\Widgets\InvoiceStats::class,
        ];
    }

    public function getTitle(): string
    {
        return 'Sales Invoices';
    }

    public function getSubheading(): ?string
    {
        return 'View and manage all sales invoices generated from POS transactions';
    }
}

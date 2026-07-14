<?php

namespace App\Exports;

use App\Models\Medicine;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StockExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Medicine::with('category')->orderBy('quantity_in_stock')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Category', 'Unit', 'Stock', 'Reorder Level', 'Expiry Date', 'Status'];
    }

    public function map($m): array
    {
        return [
            $m->id, $m->name, $m->category->name, $m->unit,
            $m->quantity_in_stock, $m->reorder_level,
            $m->expiry_date?->format('Y-m-d'),
            $m->isExpired() ? 'Expired' : ($m->isLowStock() ? 'Low Stock' : 'OK'),
        ];
    }
}

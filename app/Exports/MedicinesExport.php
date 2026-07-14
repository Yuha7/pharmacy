<?php

namespace App\Exports;

use App\Models\Medicine;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MedicinesExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Medicine::with('category')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Brand', 'Generic', 'Category', 'Unit', 'Cost Price', 'Selling Price', 'Stock', 'Reorder Level', 'Expiry Date', 'Status'];
    }

    public function map($m): array
    {
        return [
            $m->id, $m->name, $m->brand_name, $m->generic_name,
            $m->category->name, $m->unit, $m->cost_price, $m->selling_price,
            $m->quantity_in_stock, $m->reorder_level,
            $m->expiry_date?->format('Y-m-d'), $m->status,
        ];
    }
}

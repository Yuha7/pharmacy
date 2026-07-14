<?php

namespace App\Exports;

use App\Models\Purchase;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PurchasesExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private ?string $from = null, private ?string $to = null) {}

    public function collection()
    {
        $q = Purchase::with('supplier', 'user');
        if ($this->from && $this->to) {
            $q->whereBetween('purchase_date', [$this->from, $this->to]);
        }
        return $q->latest()->get();
    }

    public function headings(): array
    {
        return ['ID', 'Invoice', 'Supplier', 'Date', 'Total', 'Discount', 'Tax', 'Grand Total', 'Payment Status', 'By'];
    }

    public function map($p): array
    {
        return [
            $p->id, $p->invoice_number, $p->supplier->name,
            $p->purchase_date->format('Y-m-d'), $p->total_amount,
            $p->discount, $p->tax, $p->grand_total,
            $p->payment_status, $p->user->name,
        ];
    }
}

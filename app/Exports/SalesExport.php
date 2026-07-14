<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private ?string $from = null, private ?string $to = null) {}

    public function collection()
    {
        $q = Sale::with('customer', 'user');
        if ($this->from && $this->to) {
            $q->whereBetween('sale_date', [$this->from, $this->to]);
        }
        return $q->latest()->get();
    }

    public function headings(): array
    {
        return ['ID', 'Invoice', 'Customer', 'Date', 'Subtotal', 'Discount', 'Tax', 'Grand Total', 'Amount Paid', 'Change', 'Payment Method', 'Status', 'By'];
    }

    public function map($s): array
    {
        return [
            $s->id, $s->invoice_number, $s->customer?->name ?? 'Walk-in',
            $s->sale_date->format('Y-m-d'), $s->subtotal, $s->discount,
            $s->tax, $s->grand_total, $s->amount_paid, $s->change_amount,
            $s->payment_method, $s->payment_status, $s->user->name,
        ];
    }
}

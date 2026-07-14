<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomersExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Customer::withCount('sales')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Phone', 'Email', 'Address', 'Total Sales'];
    }

    public function map($c): array
    {
        return [$c->id, $c->name, $c->phone, $c->email, $c->address, $c->sales_count];
    }
}

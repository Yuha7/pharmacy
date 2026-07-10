@extends('layouts.app')
@section('title', 'Sales Report')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">From</label>
                <input type="date" name="from" class="form-control" value="{{ $from }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">To</label>
                <input type="date" name="to" class="form-control" value="{{ $to }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="alert alert-info d-flex justify-content-between align-items-center">
    <span>Total Sales: <strong>{{ number_format($total, 2) }}</strong> ({{ $sales->count() }} transactions)</span>
    <a href="{{ route('export.sales', ['from' => $from, 'to' => $to]) }}" class="btn btn-sm btn-success">
        <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Invoice</th><th>Customer</th><th>Date</th><th>Grand Total</th><th>Method</th><th>By</th></tr></thead>
            <tbody>
            @forelse($sales as $s)
                <tr>
                    <td><a href="{{ route('sales.show', $s) }}">{{ $s->invoice_number }}</a></td>
                    <td>{{ $s->customer?->name ?? 'Walk-in' }}</td>
                    <td>{{ $s->sale_date->format('d M Y') }}</td>
                    <td>{{ number_format($s->grand_total, 2) }}</td>
                    <td>{{ $s->payment_method }}</td>
                    <td>{{ $s->user->name }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">No sales in this period.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

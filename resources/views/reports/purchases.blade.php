@extends('layouts.app')
@section('title', 'Purchase Report')

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
    <span>Total Purchases: <strong>{{ number_format($total, 2) }}</strong> ({{ $purchases->count() }} orders)</span>
    <a href="{{ route('export.purchases', ['from' => $from, 'to' => $to]) }}" class="btn btn-sm btn-success">
        <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Invoice</th><th>Supplier</th><th>Date</th><th>Grand Total</th><th>Status</th><th>By</th></tr></thead>
            <tbody>
            @forelse($purchases as $p)
                <tr>
                    <td><a href="{{ route('purchases.show', $p) }}">{{ $p->invoice_number ?? '#'.$p->id }}</a></td>
                    <td>{{ $p->supplier->name }}</td>
                    <td>{{ $p->purchase_date->format('d M Y') }}</td>
                    <td>{{ number_format($p->grand_total, 2) }}</td>
                    <td><span class="badge {{ $p->payment_status === 'paid' ? 'bg-success' : 'bg-danger' }}">{{ $p->payment_status }}</span></td>
                    <td>{{ $p->user->name }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">No purchases in this period.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

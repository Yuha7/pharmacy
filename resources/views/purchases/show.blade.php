@extends('layouts.app')
@section('title', 'Purchase #' . ($purchase->invoice_number ?? $purchase->id))

@section('content')
<div class="card" style="max-width:750px">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-3">
            <div>
                <h5 class="mb-0">Purchase Invoice</h5>
                <small class="text-muted">{{ $purchase->invoice_number ?? 'N/A' }}</small>
            </div>
            <span class="badge {{ $purchase->payment_status === 'paid' ? 'bg-success' : 'bg-danger' }} fs-6">{{ $purchase->payment_status }}</span>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <strong>Supplier:</strong> {{ $purchase->supplier->name }}<br>
                <strong>Date:</strong> {{ $purchase->purchase_date->format('d M Y') }}<br>
                <strong>By:</strong> {{ $purchase->user->name }}
            </div>
        </div>
        <table class="table table-bordered table-sm">
            <thead class="table-light"><tr><th>Medicine</th><th>Batch</th><th>Expiry</th><th>Qty</th><th>Unit Cost</th><th>Subtotal</th></tr></thead>
            <tbody>
            @foreach($purchase->items as $item)
                <tr>
                    <td>{{ $item->medicine->name }}</td>
                    <td>{{ $item->batch_number ?? '—' }}</td>
                    <td>{{ $item->expiry_date?->format('d M Y') ?? '—' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->unit_cost, 2) }}</td>
                    <td>{{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr><td colspan="5" class="text-end">Total</td><td>{{ number_format($purchase->total_amount, 2) }}</td></tr>
                <tr><td colspan="5" class="text-end">Discount</td><td>-{{ number_format($purchase->discount, 2) }}</td></tr>
                <tr><td colspan="5" class="text-end">Tax</td><td>+{{ number_format($purchase->tax, 2) }}</td></tr>
                <tr class="fw-bold"><td colspan="5" class="text-end">Grand Total</td><td>{{ number_format($purchase->grand_total, 2) }}</td></tr>
            </tfoot>
        </table>
        <a href="{{ route('purchases.index') }}" class="btn btn-secondary btn-sm">← Back</a>
    </div>
</div>
@endsection

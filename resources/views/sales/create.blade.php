@extends('layouts.app')
@section('title', 'New Sale')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('sales.store') }}" method="POST">
            @csrf
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Customer (optional)</label>
                    <select name="customer_id" class="form-select">
                        <option value="">Walk-in Customer</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Payment Method *</label>
                    <select name="payment_method" class="form-select" required>
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="mobile">Mobile</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Discount</label>
                    <input type="number" step="0.01" name="discount" id="discount" class="form-control" value="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tax</label>
                    <input type="number" step="0.01" name="tax" id="tax" class="form-control" value="0">
                </div>
            </div>

            <h6 class="fw-semibold mb-2">Items</h6>
            <table class="table table-bordered" id="items-table">
                <thead class="table-light">
                    <tr><th>Medicine</th><th>Price</th><th>Stock</th><th>Qty</th><th>Subtotal</th><th></th></tr>
                </thead>
                <tbody id="items-body">
                    <tr class="item-row">
                        <td>
                            <select name="items[0][medicine_id]" class="form-select form-select-sm med-select" required>
                                <option value="">Select medicine</option>
                                @foreach($medicines as $m)
                                    <option value="{{ $m->id }}" data-price="{{ $m->selling_price }}" data-stock="{{ $m->quantity_in_stock }}">{{ $m->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="text" class="form-control form-control-sm price-display" readonly></td>
                        <td><input type="text" class="form-control form-control-sm stock-display" readonly></td>
                        <td><input type="number" name="items[0][quantity]" class="form-control form-control-sm qty" min="1" value="1" required></td>
                        <td><input type="text" class="form-control form-control-sm subtotal" readonly></td>
                        <td><button type="button" class="btn btn-sm btn-outline-danger remove-row">✕</button></td>
                    </tr>
                </tbody>
            </table>
            <button type="button" id="add-row" class="btn btn-sm btn-outline-primary mb-3">+ Add Item</button>

            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Grand Total</label>
                    <input type="text" id="grand-total-display" class="form-control fw-bold fs-5" readonly value="0.00">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Amount Paid *</label>
                    <input type="number" step="0.01" name="amount_paid" id="amount-paid" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Change</label>
                    <input type="text" id="change-display" class="form-control" readonly value="0.00">
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-success btn-lg">Complete Sale</button>
                <a href="{{ route('sales.index') }}" class="btn btn-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let rowIndex = 1;
const medicines = {!! $medicinesJson !!};

function buildOptions() {
    return medicines.map(m => `<option value="${m.id}" data-price="${m.price}" data-stock="${m.stock}">${m.name}</option>`).join('');
}

function calcRow(row) {
    const qty = parseFloat(row.querySelector('.qty').value) || 0;
    const price = parseFloat(row.querySelector('.price-display').value) || 0;
    row.querySelector('.subtotal').value = (qty * price).toFixed(2);
    updateTotal();
}

function updateTotal() {
    let subtotal = 0;
    document.querySelectorAll('.subtotal').forEach(el => subtotal += parseFloat(el.value) || 0);
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const tax = parseFloat(document.getElementById('tax').value) || 0;
    const grand = subtotal - discount + tax;
    document.getElementById('grand-total-display').value = grand.toFixed(2);
    const paid = parseFloat(document.getElementById('amount-paid').value) || 0;
    document.getElementById('change-display').value = (paid - grand).toFixed(2);
}

function attachEvents(row) {
    row.querySelector('.med-select').addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        row.querySelector('.price-display').value = opt.dataset.price || '';
        row.querySelector('.stock-display').value = opt.dataset.stock || '';
        calcRow(row);
    });
    row.querySelector('.qty').addEventListener('input', () => calcRow(row));
    row.querySelector('.remove-row').addEventListener('click', () => { row.remove(); updateTotal(); });
}

document.getElementById('add-row').addEventListener('click', () => {
    const tbody = document.getElementById('items-body');
    const tr = document.createElement('tr');
    tr.className = 'item-row';
    tr.innerHTML = `
        <td><select name="items[${rowIndex}][medicine_id]" class="form-select form-select-sm med-select" required><option value="">Select medicine</option>${buildOptions()}</select></td>
        <td><input type="text" class="form-control form-control-sm price-display" readonly></td>
        <td><input type="text" class="form-control form-control-sm stock-display" readonly></td>
        <td><input type="number" name="items[${rowIndex}][quantity]" class="form-control form-control-sm qty" min="1" value="1" required></td>
        <td><input type="text" class="form-control form-control-sm subtotal" readonly></td>
        <td><button type="button" class="btn btn-sm btn-outline-danger remove-row">✕</button></td>`;
    tbody.appendChild(tr);
    rowIndex++;
    attachEvents(tr);
});

document.querySelectorAll('.item-row').forEach(attachEvents);
document.getElementById('discount').addEventListener('input', updateTotal);
document.getElementById('tax').addEventListener('input', updateTotal);
document.getElementById('amount-paid').addEventListener('input', updateTotal);
</script>
@endpush

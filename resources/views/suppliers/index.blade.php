@extends('layouts.app')
@section('title', 'Suppliers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex gap-2">
        <a href="{{ route('export.suppliers') }}" class="btn btn-sm btn-success"><i class="bi bi-file-earmark-excel me-1"></i>Export Excel</a>
        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-upload me-1"></i>Import Excel
        </button>
    </div>
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Add Supplier</a>
</div>

<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Import Suppliers from Excel</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('import.suppliers') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small">Required columns: <code>name, phone, email, address</code></p>
                    <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Name</th><th>Phone</th><th>Email</th><th>Purchases</th><th></th></tr></thead>
            <tbody>
            @forelse($suppliers as $s)
                <tr>
                    <td>{{ $s->name }}</td>
                    <td>{{ $s->phone ?? '—' }}</td>
                    <td>{{ $s->email ?? '—' }}</td>
                    <td>{{ $s->purchases_count }}</td>
                    <td>
                        <a href="{{ route('suppliers.edit', $s) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('suppliers.destroy', $s) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Del</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-4 text-muted">No suppliers yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($suppliers->hasPages())
    <div class="card-footer bg-white">{{ $suppliers->links() }}</div>
    @endif
</div>
@endsection

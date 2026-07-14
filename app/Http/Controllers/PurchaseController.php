<?php

namespace App\Http\Controllers;

use App\Http\Requests\Purchase\StorePurchaseRequest;
use App\Models\Medicine;
use App\Models\Purchase;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with('supplier', 'user')->latest()->paginate(15);
        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $medicines = Medicine::where('status', 'active')->orderBy('name')->get();
        $medicinesJson = $medicines->map(function ($m) {
            return [
                'id'   => $m->id,
                'name' => $m->name,
                'cost' => (float) $m->cost_price,
            ];
        })->toJson();
        return view('purchases.create', compact('suppliers', 'medicines', 'medicinesJson'));
    }

    public function store(StorePurchaseRequest $request)
    {
        DB::transaction(function () use ($request) {
            $data = $request->validated();

            $purchase = Purchase::create([
                'supplier_id'    => $data['supplier_id'],
                'user_id'        => auth()->id(),
                'purchase_date'  => $data['purchase_date'],
                'invoice_number' => $data['invoice_number'] ?? null,
                'discount'       => $data['discount'] ?? 0,
                'tax'            => $data['tax'] ?? 0,
                'payment_status' => $data['payment_status'],
                'notes'          => $data['notes'] ?? null,
                'total_amount'   => 0,
                'grand_total'    => 0,
            ]);

            $total = 0;
            foreach ($data['items'] as $item) {
                $subtotal = $item['quantity'] * $item['unit_cost'];
                $total += $subtotal;

                $purchase->items()->create([
                    'medicine_id'  => $item['medicine_id'],
                    'quantity'     => $item['quantity'],
                    'unit_cost'    => $item['unit_cost'],
                    'subtotal'     => $subtotal,
                    'expiry_date'  => $item['expiry_date'] ?? null,
                    'batch_number' => $item['batch_number'] ?? null,
                ]);

                Medicine::where('id', $item['medicine_id'])
                    ->increment('quantity_in_stock', $item['quantity']);

                StockMovement::create([
                    'medicine_id'    => $item['medicine_id'],
                    'user_id'        => auth()->id(),
                    'movement_type'  => 'purchase',
                    'quantity'       => $item['quantity'],
                    'reference_type' => Purchase::class,
                    'reference_id'   => $purchase->id,
                ]);
            }

            $grandTotal = $total - ($data['discount'] ?? 0) + ($data['tax'] ?? 0);
            $purchase->update(['total_amount' => $total, 'grand_total' => $grandTotal]);
        });

        return redirect()->route('purchases.index')->with('success', 'Purchase recorded successfully.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load('supplier', 'user', 'items.medicine');
        return view('purchases.show', compact('purchase'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        $sales = Sale::with('customer', 'user')
            ->whereBetween('sale_date', [$from, $to])
            ->latest()
            ->get();

        $total = $sales->sum('grand_total');

        return view('reports.sales', compact('sales', 'total', 'from', 'to'));
    }

    public function purchases(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        $purchases = Purchase::with('supplier', 'user')
            ->whereBetween('purchase_date', [$from, $to])
            ->latest()
            ->get();

        $total = $purchases->sum('grand_total');

        return view('reports.purchases', compact('purchases', 'total', 'from', 'to'));
    }

    public function stock()
    {
        $medicines = Medicine::with('category')
            ->orderBy('quantity_in_stock')
            ->paginate(20);

        $low_stock = Medicine::whereColumn('quantity_in_stock', '<=', 'reorder_level')->count();
        $expired   = Medicine::where('expiry_date', '<', now())->count();

        return view('reports.stock', compact('medicines', 'low_stock', 'expired'));
    }
}

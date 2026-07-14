<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\Supplier;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today  = Carbon::today();
        $in30   = Carbon::today()->addDays(30);
        $in90   = Carbon::today()->addDays(90);

        $stats = [
            'total_medicines'   => Medicine::count(),
            'low_stock'         => Medicine::whereColumn('quantity_in_stock', '<=', 'reorder_level')->count(),
            'expired'           => Medicine::where('expiry_date', '<', $today)->count(),
            'expiring_soon'     => Medicine::whereBetween('expiry_date', [$today, $in30])->count(),
            'today_sales'       => Sale::whereDate('sale_date', $today)->sum('grand_total'),
            'today_purchases'   => Purchase::whereDate('purchase_date', $today)->sum('grand_total'),
            'total_suppliers'   => Supplier::count(),
        ];

        $low_stock_medicines = Medicine::with('category')
            ->whereColumn('quantity_in_stock', '<=', 'reorder_level')
            ->latest()->take(5)->get();

        $recent_sales = Sale::with('customer', 'user')
            ->latest()->take(5)->get();

        // Medicines expiring within 90 days (not yet expired), sorted soonest first
        $expiring_medicines = Medicine::with('category')
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [$today, $in90])
            ->orderBy('expiry_date')
            ->take(8)
            ->get();

        // Batches expiring within 90 days from purchase_items
        $expiring_batches = PurchaseItem::with('medicine', 'purchase.supplier')
            ->whereNotNull('expiry_date')
            ->whereNotNull('batch_number')
            ->whereBetween('expiry_date', [$today, $in90])
            ->orderBy('expiry_date')
            ->take(8)
            ->get();

        return view('dashboard.index', compact(
            'stats', 'low_stock_medicines', 'recent_sales',
            'expiring_medicines', 'expiring_batches'
        ));
    }
}

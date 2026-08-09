<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Debt;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // الفترة الزمنية (افتراضياً هذا الشهر)
        $fromDate = $request->input('from_date', now()->startOfMonth()->toDateString());
        $toDate   = $request->input('to_date', now()->toDateString());

        // 1. المبيعات والأرباح
        $salesQuery = Sale::whereDate('created_at', '>=', $fromDate)
                          ->whereDate('created_at', '<=', $toDate);

        $totalSales = $salesQuery->sum('total_amount');
        $salesCount = $salesQuery->count();

        // 2. المخزون والأصناف
        $totalProducts    = Product::count();
        $inventoryValue   = Product::selectRaw('SUM(selling_price * quantity_packets) as total')->value('total') ?? 0;
        $nearExpiryCount  = Product::nearExpiry(90)->count();
        $expiryPercentage = $totalProducts > 0 ? round(($nearExpiryCount / $totalProducts) * 100, 1) : 0;

        // 3. الديون والمصروفات
        $totalDebts = Debt::where('status', 'unpaid')->sum('amount');
        $netProfit  = $totalSales * 0.25; // مثال لحساب صافي الربح أو يمكن ربطه بالـ cost_price

        return view('dashboard.dashboard', compact(
            'fromDate', 'toDate', 'totalSales', 'salesCount',
            'totalProducts', 'inventoryValue', 'expiryPercentage',
            'totalDebts', 'netProfit'
        ));
    }
}
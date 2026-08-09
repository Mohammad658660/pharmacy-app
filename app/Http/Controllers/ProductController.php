<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * عرض قائمة الأدوية مع إمكانية البحث والترقيم وحساب حالات المخزون
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $query = Product::query();

        if ($status === 'low_stock') {
            $query->lowStock();
        } elseif ($status === 'near_expiry') {
            $query->nearExpiry();
        } elseif ($status === 'expired') {
            $query->expired();
        } elseif ($status === 'damaged') {
            $query->where(function ($q) {
                $q->where('damaged_quantity', '>', 0)
                  ->orWhereDate('expiry_date', '<=', now());
            });
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('trade_name', 'LIKE', "%{$search}%")
                  ->orWhere('name_ar', 'LIKE', "%{$search}%")
                  ->orWhere('scientific_name', 'LIKE', "%{$search}%")
                  ->orWhere('company', 'LIKE', "%{$search}%");

                if (Schema::hasColumn('products', 'barcode')) {
                    $q->orWhere('barcode', 'LIKE', "%{$search}%");
                }
            });
        }

        $products = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total_count' => Product::count(),
            'low_stock'   => Product::lowStock()->count(),
            'near_expiry' => Product::nearExpiry()->count(),
            'expired'     => Product::expired()->count(),
            'damaged'     => Product::where(function ($q) {
                $q->where('damaged_quantity', '>', 0)
                  ->orWhereDate('expiry_date', '<=', now());
            })->count(),
        ];

        return view('products.index', compact('products', 'stats', 'status'));
    }

    /**
     * استيراد ملف الإكسل
     */
    public function import(Request $request)
    {
        set_time_limit(300);
        ini_set('max_execution_time', 300);

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        DB::transaction(function () use ($request) {
            Excel::import(new ProductsImport, $request->file('file'));
        });

        return redirect()->back()->with('success', 'تم استيراد جميع المنتجات بنجاح');
    }

    /**
     * إضافة دواء جديد يدوياً
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'barcode'          => 'nullable|string|max:100',
            'trade_name'       => 'required|string',
            'name_ar'          => 'nullable|string',
            'scientific_name'  => 'nullable|string',
            'company'          => 'nullable|string',
            'category'         => 'nullable|string',
            'form'             => 'nullable|string',
            'cost_price'       => 'required|numeric|min:0',
            'selling_price'    => 'required|numeric|min:0',
            'quantity_packets' => 'required|integer|min:0',
            'quantity_strips'  => 'nullable|integer|min:0',
            'items_per_packet' => 'required|integer|min:1',
            'min_quantity'     => 'required|integer|min:0',
            'damaged_quantity' => 'nullable|integer|min:0',
            'expiry_date'      => 'nullable|date',
        ]);

        Product::create($validated);

        return redirect()->back()->with('success', 'تمت إضافة الدواء بنجاح');
    }

    /**
     * تعديل بيانات وسعر الدواء
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'barcode'          => 'nullable|string|max:100|unique:products,barcode,' . $id,
            'trade_name'       => 'required|string',
            'name_ar'          => 'nullable|string',
            'scientific_name'  => 'nullable|string',
            'company'          => 'nullable|string',
            'category'         => 'nullable|string',
            'form'             => 'nullable|string',
            'cost_price'       => 'required|numeric|min:0',
            'selling_price'    => 'required|numeric|min:0',
            'quantity_packets' => 'required|integer|min:0',
            'quantity_strips'  => 'nullable|integer|min:0',
            'items_per_packet' => 'required|integer|min:1',
            'min_quantity'     => 'required|integer|min:0',
            'damaged_quantity' => 'nullable|integer|min:0',
            'expiry_date'      => 'nullable|date',
        ]);

        $product->update($validated);

        return redirect()->back()->with('success', 'تم تحديث بيانات الدواء بنجاح');
    }

    /**
     * حذف دواء منفرد
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->back()->with('success', 'تم حذف الدواء بنجاح');
    }

    /**
     * حذف جميع المنتجات دفعة واحدة
     */
    public function destroyAll()
    {
        Schema::disableForeignKeyConstraints();
        Product::truncate();
        Schema::enableForeignKeyConstraints();

        return redirect()->back()->with('success', 'تم حذف جميع المنتجات بنجاح');
    }

    /**
     * البحث عن الدواء لشاشة البيع
     */
    public function posSearch(Request $request)
    {
        $query = strtolower(trim($request->get('query')));

        if (empty($query)) {
            return response()->json(['type' => 'search', 'products' => []]);
        }

        $products = Product::whereRaw('LOWER(trade_name) LIKE ?', ["%{$query}%"])
            ->orWhereRaw('LOWER(scientific_name) LIKE ?', ["%{$query}%"])
            ->take(10)
            ->get();

        return response()->json([
            'type'     => 'search',
            'products' => $products
        ]);
    }

    /**
     * عرض صفحة المواد التالفة وإحصائياتها
     */
    public function damaged()
    {
        $damagedProducts = Product::where(function ($q) {
            $q->where('damaged_quantity', '>', 0)
              ->orWhereDate('expiry_date', '<=', now());
        })->get();

        $stats = [
            'total_items' => $damagedProducts->count(),
            'total_units' => $damagedProducts->sum(function ($p) {
                return $p->damaged_quantity > 0 ? $p->damaged_quantity : $p->quantity_packets;
            }),
            'total_loss'  => $damagedProducts->sum(function ($p) {
                $qty = $p->damaged_quantity > 0 ? $p->damaged_quantity : $p->quantity_packets;
                return $qty * ($p->cost_price ?? 0);
            }),
        ];

        return view('damaged.index', compact('damagedProducts', 'stats'));
    }

    /**
     * جلب تفاصيل الدواء للبيع
     */
    public function getProductDetails($id)
    {
        $product = Product::findOrFail($id);

        $itemsPerPacket = $product->items_per_packet > 0 ? $product->items_per_packet : 1;
        $packetPrice = $product->selling_price;
        $stripPrice = $packetPrice / $itemsPerPacket;

        return response()->json([
            'id'               => $product->id,
            'trade_name'       => $product->trade_name,
            'packet_price'     => $packetPrice,
            'strip_price'      => round($stripPrice, 2),
            'items_per_packet' => $itemsPerPacket,
            'available_packets'=> $product->quantity_packets,
            'available_strips' => $product->quantity_strips ?? 0,
        ]);
    }
}
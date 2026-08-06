<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Schema;

class ProductController extends Controller
{
    /**
     * عرض قائمة الأدوية مع إمكانية البحث والترقيم
     */
   public function index(Request $request)
{
    $query = Product::query();

    if ($request->filled('search')) {
        $search = $request->input('search');

        $query->where(function ($q) use ($search) {
            $q->where('trade_name', 'LIKE', "%{$search}%")
              ->orWhere('name_ar', 'LIKE', "%{$search}%")
              ->orWhere('scientific_name', 'LIKE', "%{$search}%")
              ->orWhere('company', 'LIKE', "%{$search}%");

            // في حال وجود عمود الباركود في جدولك
            if (Schema::hasColumn('products', 'barcode')) {
                $q->orWhere('barcode', 'LIKE', "%{$search}%");
            }
        });
    }

    $products = $query->latest()->paginate(25);

    return view('products.index', compact('products'));
}

    /**
     * استيراد ملف الإكسل وقراءة البيانات
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

   try {
    Excel::import(new ProductsImport, $request->file('file'));
    return redirect()->back()->with('success', '!تم استيراد قائمة الأدوية بنجاح');
} catch (\Throwable $e) {
    return redirect()->back()->with('error', 'حدث خطأ أثناء استيراد الملف: ' . $e->getMessage());
}
    }
}
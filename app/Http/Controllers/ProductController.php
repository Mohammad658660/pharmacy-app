<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    /**
     * عرض قائمة الأدوية مع إمكانية البحث والترقيم
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // تصفية النتائج عند وجود كلمة بحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('trade_name', 'LIKE', "%{$search}%")
                  ->orWhere('name_ar', 'LIKE', "%{$search}%")
                  ->orWhere('scientific_name', 'LIKE', "%{$search}%")
                  ->orWhere('barcode', 'LIKE', "%{$search}%");
        }

        // جلب الأدوية مجزأة (25 عنصر بكل صفحة)
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
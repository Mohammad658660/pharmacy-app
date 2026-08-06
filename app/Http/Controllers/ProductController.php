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

                if (Schema::hasColumn('products', 'barcode')) {
                    $q->orWhere('barcode', 'LIKE', "%{$search}%");
                }
            });
        }

        // استخدام withQueryString للحفاظ على كلمة البحث أثناء التنقل بين الصفحات
        $products = $query->latest()->paginate(25)->withQueryString();

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
            return redirect()->back()->with('success', 'تم استيراد قائمة الأدوية بنجاح!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء استيراد الملف: ' . $e->getMessage());
        }
    }

    /**
     * إضافة دواء جديد يدوياً
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'trade_name'       => 'required|string',
            'name_ar'          => 'nullable|string',
            'scientific_name'  => 'nullable|string',
            'company'          => 'nullable|string',
            'category'         => 'nullable|string',
            'form'             => 'nullable|string',
            'cost_price'       => 'required|numeric|min:0',
            'selling_price'    => 'required|numeric|min:0',
            'quantity_packets' => 'required|integer|min:0',
            'min_quantity'     => 'required|integer|min:0',
        ]);

        Product::create($validated);

        return redirect()->back()->with('success', 'تمت إضافة الدواء بنجاح!');
    }

    /**
     * تعديل بيانات وسعر الدواء
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'trade_name'       => 'required|string',
            'name_ar'          => 'nullable|string',
            'scientific_name'  => 'nullable|string',
            'company'          => 'nullable|string',
            'category'         => 'nullable|string',
            'form'             => 'nullable|string',
            'cost_price'       => 'required|numeric|min:0',
            'selling_price'    => 'required|numeric|min:0',
            'quantity_packets' => 'required|integer|min:0',
            'min_quantity'     => 'required|integer|min:0',
        ]);

        $product->update($validated);

        return redirect()->back()->with('success', 'تم تحديث بيانات الدواء بنجاح!');
    }

    /**
     * حذف دواء
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->back()->with('success', 'تم حذف الدواء بنجاح!');
    }
}
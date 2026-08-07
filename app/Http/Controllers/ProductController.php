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

        // تطبيق شرط البحث إذا تم إرسال كلمة بحث
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

        // عرض 15 عنصر بالصفحة وحفظ معايير البحث في أزرار التنقل بين الصفحات
        $products = $query->latest()->paginate(15)->withQueryString();

        return view('products.index', compact('products'));
    }

    /**
     * استيراد ملف الإكسل
     */
  public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv|max:10240', // حد أقصى 10 ميجابايت
    ], [
        'file.required' => 'يرجى اختيار ملف الإكسل أولاً.',
        'file.mimes'    => 'صيغة الملف يجب أن تكون xlsx, xls أو csv.',
        'file.max'      => 'حجم الملف كبير جداً (الأقصى 10 ميجابايت).',
    ]);

    try {
        Excel::import(new ProductsImport, $request->file('file'));

        return redirect()->back()->with('success', 'تم استيراد شيت الإكسل وإضافة الأدوية بنجاح!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'حدث خطأ أثناء رفع الملف: ' . $e->getMessage());
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
            'barcode' => 'nullable|string|max:100|unique:products,barcode,' . ($id ?? 'NULL') . ',id',
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
    /**
 * البحث عن الدواء لشاشة البيع (عن طريق الباركود أو الاسم)
 */
public function posSearch(Request $request)
{
    $query = strtolower(trim($request->get('query')));

    if (empty($query)) {
        return response()->json(['type' => 'search', 'products' => []]);
    }

    // استخدام LOWER للبحث بغض النظر عن حالة الحروف الكبيرة/الصغيرة
   $products = Product::whereRaw('LOWER(trade_name) LIKE ?', ["%{$query}%"])
    ->orWhereRaw('LOWER(scientific_name) LIKE ?', ["%{$query}%"])
    ->take(10)
    ->get();

    return response()->json([
        'type' => 'search',
        'products' => $products
    ]);
}
}
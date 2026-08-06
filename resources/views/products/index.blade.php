@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- رسائل التنبيه والنجاح/الخطأ --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-500/10 border border-rose-500/20 text-rose-500 rounded-xl">
            {{ session('error') }}
        </div>
    @endif

    {{-- الهيدر وزر الإضافة ونموذج رفع الإكسل --}}
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800">
        <div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                💊 إدارة المنتجات والأدوية
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                عرض وإضافة وتعديل الأدوية أو استيرادها من ملفات الإكسل
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            {{-- زر فتح نافذة إضافة دواء جديد --}}
            <button onclick="openModal('addProductModal')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold rounded-xl flex items-center gap-2 transition">
                <span>➕</span> إضافة دواء جديد
            </button>

            {{-- نموذج رفع شيت الإكسل --}}
            <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                @csrf
                <input type="file" name="file" required class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-slate-800 dark:file:text-indigo-400">
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl transition flex items-center gap-1">
                    <span>📥</span> رفع شيت الإكسل
                </button>
            </form>
        </div>
    </div>

    {{-- صندوق البحث --}}
    <form method="GET" action="{{ route('products.index') }}" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="البحث باسم الدواء التجاري، العربي، العلمي، أو الشركة..." class="w-full px-4 py-3 text-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl focus:outline-none focus:border-indigo-500 text-slate-800 dark:text-slate-100">
        <button type="submit" class="px-6 py-3 bg-slate-800 hover:bg-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 text-white text-sm font-semibold rounded-xl transition">
            بحث
        </button>
    </form>

    {{-- جدول عرض الأدوية --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-950/50 text-xs text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="p-4">#</th>
                        <th class="p-4">اسم الدواء التجاري (EN)</th>
                        <th class="p-4">الاسم العربي</th>
                        <th class="p-4">المادة الفعالة / الاسم العلمي</th>
                        <th class="p-4">الشركة</th>
                        <th class="p-4">الشكل الصيدلاني</th>
                        <th class="p-4">سعر التكلفة</th>
                        <th class="p-4">سعر البيع</th>
                        <th class="p-4 text-center">العمليات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($products as $product)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition">
                            <td class="p-4 text-xs font-mono text-slate-400">{{ $loop->iteration }}</td>
                            <td class="p-4 font-bold text-slate-800 dark:text-slate-100">{{ $product->trade_name }}</td>
                            <td class="p-4">{{ $product->name_ar ?? '-' }}</td>
                            <td class="p-4 text-xs font-mono text-slate-500 dark:text-slate-400 max-w-xs truncate" title="{{ $product->scientific_name }}">
                                {{ $product->scientific_name ?? '-' }}
                            </td>
                            <td class="p-4 text-xs">{{ $product->company ?? '-' }}</td>
                            <td class="p-4 text-xs">
                                <span class="px-2 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-lg">
                                    {{ $product->form ?? 'عام' }}
                                </span>
                            </td>
                            <td class="p-4 font-mono text-slate-500 dark:text-slate-400">{{ number_format($product->cost_price ?? 0) }}</td>
                            <td class="p-4 font-bold font-mono text-emerald-600 dark:text-emerald-400">{{ number_format($product->selling_price ?? 0) }}</td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- زر التعديل --}}
                                    <button onclick="openModal('editModal{{ $product->id }}')" class="p-2 text-amber-500 hover:bg-amber-500/10 rounded-lg transition" title="تعديل">
                                        ✏️
                                    </button>

                                    {{-- زر الحذف --}}
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('هل أنت تأكد من حذف هذا الدواء؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-rose-500 hover:bg-rose-500/10 rounded-lg transition" title="حذف">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- Modal تعديل الدواء لكل عنصر --}}
                        <div id="editModal{{ $product->id }}" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm overflow-y-auto h-full w-full flex items-center justify-center p-4">
                            <div class="relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl max-w-2xl w-full p-6 shadow-xl space-y-4 text-right">
                                <div class="flex justify-between items-center border-b border-slate-200 dark:border-slate-800 pb-3">
                                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">تعديل بيانات: {{ $product->trade_name }}</h3>
                                    <button onclick="closeModal('editModal{{ $product->id }}')" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
                                </div>

                                <form action="{{ route('products.update', $product->id) }}" method="POST" class="space-y-4">
                                    @csrf
                                    @method('PUT')

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">الاسم التجاري (EN)</label>
                                            <input type="text" name="trade_name" value="{{ $product->trade_name }}" required class="w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">الاسم العربي</label>
                                            <input type="text" name="name_ar" value="{{ $product->name_ar }}" class="w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">المادة الفعالة / الاسم العلمي</label>
                                            <textarea name="scientific_name" rows="2" class="w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500">{{ $product->scientific_name }}</textarea>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">الشركة المصنعة</label>
                                            <input type="text" name="company" value="{{ $product->company }}" class="w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">الفئة (Category)</label>
                                            <input type="text" name="category" value="{{ $product->category }}" class="w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">الشكل الصيدلاني</label>
                                            <input type="text" name="form" value="{{ $product->form }}" class="w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">سعر الشراء (التكلفة)</label>
                                            <input type="number" step="0.01" name="cost_price" value="{{ $product->cost_price }}" required class="w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">سعر البيع</label>
                                            <input type="number" step="0.01" name="selling_price" value="{{ $product->selling_price }}" required class="w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">الكمية المتوفرة</label>
                                            <input type="number" name="quantity_packets" value="{{ $product->quantity_packets }}" required class="w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">الحد الأدنى للتنبيه</label>
                                            <input type="number" name="min_quantity" value="{{ $product->min_quantity }}" required class="w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500">
                                        </div>
                                    </div>

                                    <div class="flex justify-end gap-2 pt-4 border-t border-slate-200 dark:border-slate-800">
                                        <button type="button" onclick="closeModal('editModal{{ $product->id }}')" class="px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl">إلغاء</button>
                                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl">حفظ التعديلات</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="9" class="p-12 text-center text-slate-400">
                                لا توجد أدوبة مضافة حالياً. اختر ملف إكسل واضغط على "رفع شيت الإكسل" أو أضف دواء جديد يدوياً.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modal إضافة دواء جديد --}}
<div id="addProductModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm overflow-y-auto h-full w-full flex items-center justify-center p-4">
    <div class="relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl max-w-2xl w-full p-6 shadow-xl space-y-4 text-right">
        <div class="flex justify-between items-center border-b border-slate-200 dark:border-slate-800 pb-3">
            <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">➕ إضافة دواء جديد</h3>
            <button onclick="closeModal('addProductModal')" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
        </div>

        <form action="{{ route('products.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">الاسم التجاري (EN)</label>
                    <input type="text" name="trade_name" required class="w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">الاسم العربي</label>
                    <input type="text" name="name_ar" class="w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">المادة الفعالة / الاسم العلمي</label>
                    <textarea name="scientific_name" rows="2" class="w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">الشركة المصنعة</label>
                    <input type="text" name="company" class="w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">الفئة (Category)</label>
                    <input type="text" name="category" class="w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">الشكل الصيدلاني</label>
                    <input type="text" name="form" class="w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">سعر الشراء (التكلفة)</label>
                    <input type="number" step="0.01" name="cost_price" value="0" required class="w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">سعر البيع</label>
                    <input type="number" step="0.01" name="selling_price" value="0" required class="w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">الكمية المتوفرة</label>
                    <input type="number" name="quantity_packets" value="0" required class="w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">الحد الأدنى للتنبيه</label>
                    <input type="number" name="min_quantity" value="5" required class="w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="closeModal('addProductModal')" class="px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl">إلغاء</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold rounded-xl">إضافة الدواء</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
</script>
@endsection
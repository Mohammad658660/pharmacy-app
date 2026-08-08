@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- رسائل التنبيه --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 rounded-xl">
            {{ session('error') }}
        </div>
    @endif

    {{-- الهيدر وزر الإضافة ونموذج رفع الإكسل --}}
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">
                💊 إدارة المنتجات والأدوية
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                إضافة وتعديل الأدوية أو استيرادها من ملفات الإكسل
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            {{-- زر فتح نافذة إضافة دواء جديد --}}
            <button onclick="openModal('addProductModal')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold flex items-center gap-2 transition-all shadow-lg shadow-indigo-500/20">
                <span>➕</span> إضافة دواء جديد
            </button>

            {{-- نموذج رفع شيت الإكسل --}}
            <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                @csrf
                <input type="file" name="file" required class="block w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 dark:file:bg-slate-800 file:text-slate-700 dark:file:text-slate-300 hover:file:bg-slate-200 cursor-pointer">
                <button type="submit" class="px-4 py-2 bg-slate-800 dark:bg-slate-700 hover:bg-slate-900 text-white rounded-xl text-sm font-semibold transition-all flex items-center gap-2">
                    <span>📊</span> رفع شيت الإكسل
                </button>
            </form>
        </div>
    </div>

    {{-- كروت الإحصائيات والفلترة السريعة --}}
    @if(isset($stats))
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        {{-- إجمالي الأدوية --}}
        <a href="{{ route('products.index') }}"
           class="p-4 rounded-2xl border transition-all {{ ($status ?? 'all') === 'all' ? 'bg-indigo-500/10 border-indigo-500/30' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 hover:border-slate-300' }}">
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">إجمالي الأدوية</div>
            <div class="text-2xl font-bold text-slate-800 dark:text-slate-100 mt-1">{{ $stats['total_count'] ?? 0 }}</div>
        </a>

        {{-- نواقص المخزون --}}
        <a href="{{ route('products.index', array_merge(request()->query(), ['status' => 'low_stock'])) }}"
           class="p-4 rounded-2xl border transition-all {{ ($status ?? '') === 'low_stock' ? 'bg-amber-500/10 border-amber-500/30' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 hover:border-amber-500/30' }}">
            <div class="text-xs font-semibold text-amber-500">نواقص المخزون</div>
            <div class="text-2xl font-bold text-amber-500 mt-1">{{ $stats['low_stock'] ?? 0 }}</div>
        </a>

        {{-- قريبة الانتهاء --}}
        <a href="{{ route('products.index', array_merge(request()->query(), ['status' => 'near_expiry'])) }}"
           class="p-4 rounded-2xl border transition-all {{ ($status ?? '') === 'near_expiry' ? 'bg-orange-500/10 border-orange-500/30' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 hover:border-orange-500/30' }}">
            <div class="text-xs font-semibold text-orange-500">قريبة الانتهاء</div>
            <div class="text-2xl font-bold text-orange-500 mt-1">{{ $stats['near_expiry'] ?? 0 }}</div>
        </a>

        {{-- منتهية الصلاحية --}}
        <a href="{{ route('products.index', array_merge(request()->query(), ['status' => 'expired'])) }}"
           class="p-4 rounded-2xl border transition-all {{ ($status ?? '') === 'expired' ? 'bg-rose-500/10 border-rose-500/30' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 hover:border-rose-500/30' }}">
            <div class="text-xs font-semibold text-rose-500">منتهية الصلاحية</div>
            <div class="text-2xl font-bold text-rose-500 mt-1">{{ $stats['expired'] ?? 0 }}</div>
        </a>

        {{-- المواد التالفة --}}
<a href="{{ route('damaged.index') }}"
   class="p-4 rounded-2xl border transition-all {{ ($status ?? '') === 'damaged' ? 'bg-purple-500/10 border-purple-500/30' : '' }}">
    <div class="text-xs font-semibold text-purple-500">المواد التالفة</div>
    <div class="text-2xl font-bold text-purple-500 mt-1">{{ $stats['damaged'] ?? 0 }}</div>
</a>
    </div>
    @endif

    {{-- صندوق البحث --}}
    <form method="GET" action="{{ route('products.index') }}" class="flex gap-2">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم الدواء، الاسم العلمي، أو الباركود..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <button type="submit" class="px-6 py-2.5 bg-slate-800 dark:bg-slate-700 hover:bg-slate-900 text-white rounded-xl text-sm font-semibold transition-all">
            بحث
        </button>
        @if(request('search') || request('status'))
            <a href="{{ route('products.index') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl text-sm font-semibold hover:bg-slate-200 transition-all flex items-center">
                إلغاء الفلترة
            </a>
        @endif
    </form>

    {{-- جدول عرض الأدوية --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-950/50 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="p-4">#</th>
                        <th class="p-4">اسم الدواء التجاري (EN)</th>
                        <th class="p-4">الاسم العربي</th>
                        <th class="p-4">الاسم العلمي / المادة الفعالة</th>
                        <th class="p-4">الشركة / الشكل</th>
                        <th class="p-4">الكمية</th>
                        <th class="p-4">التالف</th>
                        <th class="p-4">تاريخ الانتهاء</th>
                        <th class="p-4">سعر التكلفة</th>
                        <th class="p-4">سعر البيع</th>
                        <th class="p-4 text-center">العمليات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($products as $product)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="p-4 text-xs font-mono text-slate-400">{{ $loop->iteration }}</td>
                            <td class="p-4">
                                <div class="font-bold text-slate-800 dark:text-slate-100">{{ $product->trade_name }}</div>
                                @if($product->barcode)
                                    <div class="text-[10px] font-mono text-slate-400">🏷️ {{ $product->barcode }}</div>
                                @endif
                            </td>
                            <td class="p-4">{{ $product->name_ar ?? '-' }}</td>
                            <td class="p-4 text-xs font-mono text-slate-500">{{ $product->scientific_name ?? '-' }}</td>
                            <td class="p-4 text-xs">
                                <div>{{ $product->company ?? '-' }}</div>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-500 text-[10px]">
                                    {{ $product->form ?? 'عام' }}
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-semibold {{ $product->quantity_packets <= $product->min_quantity ? 'bg-amber-500/10 text-amber-600' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                                    {{ $product->quantity_packets }}
                                </span>
                            </td>
                            <td class="p-4">
                                @if($product->damaged_quantity > 0)
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-purple-500/10 text-purple-600">
                                        {{ $product->damaged_quantity }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">0</span>
                                @endif
                            </td>
                            <td class="p-4 text-xs font-mono">
                                @if($product->expiry_date)
                                    @if($product->expiry_date->isPast())
                                        <span class="text-rose-500 font-bold">{{ $product->expiry_date->format('Y-m-d') }} (منتهي)</span>
                                    @elseif($product->expiry_date->diffInDays(now()) <= 90)
                                        <span class="text-orange-400 font-bold">{{ $product->expiry_date->format('Y-m-d') }}</span>
                                    @else
                                        <span class="text-slate-400">{{ $product->expiry_date->format('Y-m-d') }}</span>
                                    @endif
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="p-4 text-xs font-mono text-slate-500">{{ number_format($product->cost_price, 2) }}</td>
                            <td class="p-4 font-bold font-mono text-emerald-600 dark:text-emerald-400">{{ number_format($product->selling_price, 2) }}</td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- زر التعديل --}}
                                    <button type="button" onclick="openModal('editModal{{ $product->id }}')" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-amber-500 transition-colors" title="تعديل">
                                        ✏️
                                    </button>

                                    {{-- زر الحذف --}}
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('هل أنت تأكد من حذف هذا الدواء؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-rose-500 transition-colors" title="حذف">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="p-12 text-center text-slate-400 font-medium">
                                لا توجد أدوية مطابقة للبحث حالياً
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- الترقيم والتنقل بين الصفحات --}}
        @if($products->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modal إضافة دواء جديد --}}
<div id="addProductModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
    <div class="relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl max-w-2xl w-full p-6 shadow-2xl space-y-4 my-8">
        <div class="flex justify-between items-center border-b border-slate-200 dark:border-slate-800 pb-4">
            <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">إضافة دواء جديد</h3>
            <button type="button" onclick="closeModal('addProductModal')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

      <form action="{{ route('products.store') }}" method="POST">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        
        <!-- حقل الباركود (يأخذ عرض الشاشة بالكامل) -->
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-500 mb-1">حقل الباركود</label>
            <input type="text" name="barcode" placeholder="أدخل الباركود أو اتركه فارغاً" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-sm">
        </div>

        <!-- اسم الدواء التجاري والاسم العربي -->
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">اسم الدواء التجاري (EN) *</label>
            <input type="text" name="trade_name" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">الاسم العربي</label>
            <input type="text" name="name_ar" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-sm">
        </div>

        <!-- الاسم العلمي (يأخذ عرض الشاشة بالكامل) -->
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-500 mb-1">الاسم العلمي / المادة الفعالة</label>
            <textarea name="scientific_name" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-sm"></textarea>
        </div>

        <!-- الشركة المصنعة والفئة -->
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">الشركة المصنعة</label>
            <input type="text" name="company" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">الفئة / التصنيف</label>
            <input type="text" name="category" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-sm">
        </div>

        <!-- الشكل الصيدلاني وتاريخ الانتهاء -->
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">الشكل الدوائي</label>
            <input type="text" name="form" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">تاريخ الانتهاء</label>
            <input type="date" name="expiry_date" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-sm">
        </div>

    <!-- سعر التكلفة وسعر البيع -->
<div>
    <label class="block text-xs font-semibold text-slate-500 mb-1">سعر التكلفة *</label>
    <input type="number" step="0.01" name="cost_price" value="{{ old('cost_price', $product->cost_price ?? '0.00') }}" class="w-full px-3 py-2 bg-[#080d1a] border border-[#1e293b] rounded-lg text-slate-100 focus:outline-none focus:border-emerald-500">
</div>

<div>
    <label class="block text-xs font-semibold text-slate-500 mb-1">سعر البيع *</label>
    <input type="number" step="0.01" name="selling_price" value="{{ old('selling_price', $product->selling_price ?? '0.00') }}" class="w-full px-3 py-2 bg-[#080d1a] border border-[#1e293b] rounded-lg text-slate-100 focus:outline-none focus:border-emerald-500">
</div>

<!-- الكمية المتوفرة (باكيت وشريط) -->
<div>
    <label class="block text-xs font-semibold text-slate-500 mb-1">الكمية المتوفرة (باكيت) *</label>
    <input type="number" name="quantity_packets" value="{{ old('quantity_packets', $product->quantity_packets ?? 0) }}" class="w-full px-3 py-2 bg-[#080d1a] border border-[#1e293b] rounded-lg text-slate-100 focus:outline-none focus:border-emerald-500">
</div>

<div>
    <label class="block text-xs font-semibold text-slate-500 mb-1">الكمية المتوفرة (شريط)</label>
    <input type="number" name="quantity_strips" value="{{ old('quantity_strips', $product->quantity_strips ?? 0) }}" class="w-full px-3 py-2 bg-[#080d1a] border border-[#1e293b] rounded-lg text-slate-100 focus:outline-none focus:border-emerald-500" placeholder="0">
</div>

<!-- عدد الأشرطة والحد الأدنى -->
<div>
    <label class="block text-xs font-semibold text-slate-500 mb-1">عدد الأشرطة في الباكيت *</label>
    <input type="number" name="items_per_packet" value="{{ old('items_per_packet', $product->items_per_packet ?? 1) }}" class="w-full px-3 py-2 bg-[#080d1a] border border-[#1e293b] rounded-lg text-slate-100 focus:outline-none focus:border-emerald-500">
</div>

<div>
    <label class="block text-xs font-semibold text-slate-500 mb-1">الحد الأدنى (النواقص) *</label>
    <input type="number" name="min_quantity" value="{{ old('min_quantity', $product->min_quantity ?? 5) }}" class="w-full px-3 py-2 bg-[#080d1a] border border-[#1e293b] rounded-lg text-slate-100 focus:outline-none focus:border-emerald-500">
</div>

<!-- الكمية التالفة -->
<div class="md:col-span-2">
    <label class="block text-xs font-semibold text-slate-500 mb-1">الكمية التالفة</label>
    <input type="number" name="damaged_quantity" value="{{ old('damaged_quantity', $product->damaged_quantity ?? 0) }}" class="w-full px-3 py-2 bg-[#080d1a] border border-[#1e293b] rounded-lg text-slate-100 focus:outline-none focus:border-emerald-500">
</div>

        <!-- الأزرار بالأسفل (تأخذ عرض الشاشة بالكامل) -->
        <div class="md:col-span-2 flex justify-end gap-2 pt-4 border-t border-slate-200 dark:border-slate-800 mt-2">
            <button type="button" onclick="closeModal('addProductModal')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl text-sm font-semibold">إلغاء</button>
            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold transition-all shadow-lg shadow-emerald-500/20">حفظ الدواء</button>
        </div>

    </div>
</form>
    </div>
</div>

{{-- Modals تعديل الأدوية --}}
@foreach($products as $product)
<div id="editModal{{ $product->id }}" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
    <div class="relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl max-w-2xl w-full p-6 shadow-2xl space-y-4 my-8">
        <div class="flex justify-between items-center border-b border-slate-200 dark:border-slate-800 pb-4">
            <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">تعديل بيانات الدواء: {{ $product->trade_name }}</h3>
            <button type="button" onclick="closeModal('editModal{{ $product->id }}')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <form action="{{ route('products.update', $product->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">حقل الباركود</label>
                    <input type="text" name="barcode" value="{{ $product->barcode }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">اسم الدواء التجاري (EN) *</label>
                    <input type="text" name="trade_name" value="{{ $product->trade_name }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">الاسم العربي</label>
                    <input type="text" name="name_ar" value="{{ $product->name_ar }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-sm">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">الاسم العلمي / المادة الفعالة</label>
                    <textarea name="scientific_name" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-sm">{{ $product->scientific_name }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">الشركة المصنعة</label>
                    <input type="text" name="company" value="{{ $product->company }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">الفئة / التصنيف</label>
                    <input type="text" name="category" value="{{ $product->category }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">الشكل الدوائي</label>
                    <input type="text" name="form" value="{{ $product->form }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">تاريخ الانتهاء</label>
                    <input type="date" name="expiry_date" value="{{ $product->expiry_date ? $product->expiry_date->format('Y-m-d') : '' }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-sm">
                </div>

                
<!-- سعر التكلفة وسعر البيع -->
<div>
    <label class="block text-xs font-semibold text-slate-500 mb-1">سعر التكلفة *</label>
    <input type="number" step="0.01" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" class="w-full px-3 py-2 bg-[#020617] border border-slate-800 rounded-lg text-slate-100 focus:outline-none focus:border-emerald-500">
</div>

<div>
    <label class="block text-xs font-semibold text-slate-500 mb-1">سعر البيع *</label>
    <input type="number" step="0.01" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" class="w-full px-3 py-2 bg-[#020617] border border-slate-800 rounded-lg text-slate-100 focus:outline-none focus:border-emerald-500">
</div>

<!-- الكمية المتوفرة (باكيت) والكمية المتوفرة (شريط) -->
<div>
    <label class="block text-xs font-semibold text-slate-500 mb-1">الكمية المتوفرة (باكيت) *</label>
    <input type="number" name="quantity_packets" value="{{ old('quantity_packets', $product->quantity_packets) }}" class="w-full px-3 py-2 bg-[#020617] border border-slate-800 rounded-lg text-slate-100 focus:outline-none focus:border-emerald-500">
</div>

<div>
    <label class="block text-xs font-semibold text-slate-500 mb-1">الكمية المتوفرة (شريط)</label>
    <input type="number" name="quantity_strips" value="{{ old('quantity_strips', $product->quantity_strips ?? 0) }}" class="w-full px-3 py-2 bg-[#020617] border border-slate-800 rounded-lg text-slate-100 focus:outline-none focus:border-emerald-500" placeholder="0">
</div>

<!-- عدد الأشرطة والحد الأدنى -->
<div>
    <label class="block text-xs font-semibold text-slate-500 mb-1">عدد الأشرطة في الباكيت *</label>
    <input type="number" name="items_per_packet" value="{{ old('items_per_packet', $product->items_per_packet) }}" class="w-full px-3 py-2 bg-[#020617] border border-slate-800 rounded-lg text-slate-100 focus:outline-none focus:border-emerald-500">
</div>

<div>
    <label class="block text-xs font-semibold text-slate-500 mb-1">الحد الأدنى (النواقص) *</label>
    <input type="number" name="min_quantity" value="{{ old('min_quantity', $product->min_quantity) }}" class="w-full px-3 py-2 bg-[#020617] border border-slate-800 rounded-lg text-slate-100 focus:outline-none focus:border-emerald-500">
</div>

<!-- الكمية التالفة -->
<div class="md:col-span-2">
    <label class="block text-xs font-semibold text-slate-500 mb-1">الكمية التالفة</label>
    <input type="number" name="damaged_quantity" value="{{ old('damaged_quantity', $product->damaged_quantity ?? 0) }}" class="w-full px-3 py-2 bg-[#020617] border border-slate-800 rounded-lg text-slate-100 focus:outline-none focus:border-emerald-500">
</div>
                
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-slate-200 dark:border-slate-800 mt-4">
                <button type="button" onclick="closeModal('editModal{{ $product->id }}')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl text-sm font-semibold hover:bg-slate-200 transition-all">إلغاء</button>
                <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-sm font-semibold transition-all shadow-lg shadow-amber-500/20">تحديث البيانات</button>
            </div>
        </form>
    </div>
</div>
@endforeach

{{-- السكربتات الخاصة بالـ Modals --}}
<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('hidden');
            
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('hidden');
        }
    }
</script>
@endsection
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- رسائل التنبيه والنجاح/الخطأ -->
    @if(session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-xl text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-500/10 border border-rose-500/20 text-rose-500 rounded-xl text-sm font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <!-- الهيدر ونموذج رفع ملف الإكسل -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">💊 إدارة المنتجات والأدوية</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">عرض واكتشاف واستيراد دليل الأدوية بالنظام من ملفات الإكسل</p>
        </div>

        <!-- نموذج رفع الإكسل -->
        <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2 w-full md:w-auto">
            @csrf
            <input type="file" name="file" required class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-slate-800 dark:file:text-slate-200 hover:file:bg-indigo-100 cursor-pointer" />
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition whitespace-nowrap shadow-sm">
                📥 رفع شيت الإكسل
            </button>
        </form>
    </div>

    <!-- صندوق البحث -->
    <form method="GET" action="{{ route('products.index') }}" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم الدواء التجاري، العلمي، العربي، أو الباركود..." class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:border-indigo-500 shadow-sm">
        <button type="submit" class="bg-slate-800 hover:bg-slate-700 dark:bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition">
            بحث
        </button>
    </form>

    <!-- جدول عرض الأدوية -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
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
                        <th class="p-4">سعر البيع</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($products as $product)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition">
                            <td class="p-4 text-xs font-mono text-slate-400">{{ $product->id }}</td>
                            <td class="p-4 font-bold text-slate-800 dark:text-slate-100">{{ $product->trade_name }}</td>
                            <td class="p-4">{{ $product->name_ar ?? '-' }}</td>
                            <td class="p-4 text-xs font-mono text-slate-500 dark:text-slate-400">{{ $product->scientific_name ?? '-' }}</td>
                            <td class="p-4 text-xs">{{ $product->company ?? '-' }}</td>
                            <td class="p-4 text-xs">
                                <span class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 px-2.5 py-1 rounded-lg">
                                    {{ $product->form ?? 'عام' }}
                                </span>
                            </td>
                            <td class="p-4 font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($product->selling_price, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center text-slate-400">
                                لا توجد أدوية مضافة حالياً. اختر ملف إكسل واضغط على "رفع شيت الإكسل" لإدخال المنتجات.
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
@endsection
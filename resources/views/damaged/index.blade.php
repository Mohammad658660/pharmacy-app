@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- الهيدر والإحصائيات السريعة --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                <span>🗑️</span> سجل المواد التالفة والخسائر
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                متابعة وتسجيل الأدوية التالفة ومنتهية الصلاحية مع حساب إجمالي خسائر التكلفة
            </p>
        </div>

        <button onclick="openModal('addDamagedModal')" class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-sm font-semibold transition-all shadow-lg shadow-rose-500/20 flex items-center gap-2">
            <span>➕</span> تسجيل تالف جديد
        </button>
    </div>

    {{-- كروت الملخص --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <div class="text-xs font-semibold text-slate-500">عدد المواد التالفة</div>
            <div class="text-2xl font-bold text-rose-500 mt-1">{{ $damagedProducts->count() }}باكيت</div>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <div class="text-xs font-semibold text-slate-500">إجمالي الوحدات التالفة</div>
            <div class="text-2xl font-bold text-amber-500 mt-1">{{ $damagedProducts->sum('damaged_quantity') }} شريط</div>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <div class="text-xs font-semibold text-slate-500">إجمالي الخسارة المادية (التكلفة)</div>
            <div class="text-2xl font-bold text-rose-600 dark:text-rose-400 mt-1 font-mono">
                {{ number_format($damagedProducts->sum(fn($p) => $p->damaged_quantity * $p->cost_price), 2) }}
            </div>
        </div>
    </div>

    {{-- جدول المواد التالفة --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-950/50 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="p-4">#</th>
                        <th class="p-4">اسم الدواء</th>
                        <th class="p-4">الباركود</th>
                        <th class="p-4">الكمية التالفة</th>
                        <th class="p-4">سعر التكلفة</th>
                        <th class="p-4">إجمالي الخسارة</th>
                        <th class="p-4">تاريخ الانتهاء</th>
                        <th class="p-4 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($damagedProducts as $product)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="p-4 text-xs font-mono text-slate-400">{{ $loop->iteration }}</td>
                            <td class="p-4 font-bold text-slate-800 dark:text-slate-100">
                                {{ $product->trade_name }}
                                @if($product->name_ar)
                                    <span class="block text-xs font-normal text-slate-400">{{ $product->name_ar }}</span>
                                @endif
                            </td>
                            <td class="p-4 font-mono text-xs text-slate-400">{{ $product->barcode ?? '-' }}</td>
                            <td class="p-4 font-bold text-rose-500">{{ $product->damaged_quantity }} باكيت</td>
                            <td class="p-4 font-mono text-xs">{{ number_format($product->cost_price, 2) }}</td>
                            <td class="p-4 font-mono font-bold text-rose-600 dark:text-rose-400">
                                {{ number_format($product->damaged_quantity * $product->cost_price, 2) }}
                            </td>
                            <td class="p-4 text-xs font-mono text-slate-400">
                                {{ $product->expiry_date ? $product->expiry_date->format('Y-m-d') : '-' }}
                            </td>
                            <td class="p-4 text-center">
                                <a href="{{ route('products.index', ['search' => $product->trade_name]) }}" class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-lg text-xs hover:bg-slate-200 transition-colors">
                                    عرض بالمنتجات
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-12 text-center text-slate-400">
                                لا توجد مواد تالفة مسجلة حالياً
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
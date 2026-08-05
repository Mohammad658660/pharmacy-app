@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex justify-between items-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-xl shadow-sm">
        <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
            🧾 سجل الفواتير والمبيعات
        </h1>
        <a href="/sales" class="bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold px-4 py-2 rounded-xl transition shadow flex items-center gap-1">
            + فاتورة جديدة
        </a>
    </div>

    <!-- Invoices Table Container -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-slate-700 dark:text-slate-300 text-sm">
                <thead class="bg-slate-100 dark:bg-slate-950 text-slate-600 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="p-4">#</th>
                        <th class="p-4">رقم الفاتورة</th>
                        <th class="p-4">اسم العميل</th>
                        <th class="p-4">المبلغ الكلي</th>
                        <th class="p-4">الخصم</th>
                        <th class="p-4">الصافي</th>
                        <th class="p-4">طريقة الدفع</th>
                        <th class="p-4">التاريخ</th>
                        <th class="p-4">الأدوية المباعة</th>
                        <th class="p-4 text-center">الخيارات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="p-4 font-mono text-slate-400 font-semibold">{{ $sale->id }}</td>
                            <td class="p-4">
                                <span class="bg-slate-100 dark:bg-slate-800 text-purple-600 dark:text-purple-400 px-2.5 py-1 rounded border border-slate-200 dark:border-slate-700 font-mono text-xs">
                                    {{ $sale->invoice_number }}
                                </span>
                            </td>
                            <td class="p-4 font-medium text-slate-800 dark:text-slate-200">{{ $sale->customer_name }}</td>
                            <td class="p-4 font-mono">{{ number_format($sale->total_amount) }}</td>
                            <td class="p-4 font-mono text-rose-500 dark:text-rose-400">{{ number_format($sale->discount) }}</td>
                            <td class="p-4 font-mono font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($sale->final_amount) }}</td>
                            <td class="p-4">
                                @if($sale->payment_method === 'credit')
                                    <span class="bg-rose-100 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20 px-2.5 py-1 rounded-full text-xs font-bold inline-block">
                                        آجل (دين)
                                    </span>
                                @else
                                    <span class="bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20 px-2.5 py-1 rounded-full text-xs font-bold inline-block">
                                        نقداً (كاش)
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-xs text-slate-500 dark:text-slate-400 dir-ltr text-right font-mono">
                                {{ $sale->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="p-4">
                                <div class="space-y-1">
                                    @foreach($sale->items as $item)
                                        <div class="text-xs text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800/80 px-2 py-1 rounded border border-slate-200 dark:border-slate-700/50 inline-block">
                                            <span class="text-slate-800 dark:text-slate-200 font-medium">{{ $item->trade_name }}</span>
                                            <span class="text-purple-600 dark:text-purple-400 font-mono">(×{{ $item->qty }})</span>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="p-4 text-center">
                                <a href="{{ route('sales.print', $sale->id) }}" target="_blank" class="bg-indigo-50 dark:bg-indigo-600/20 hover:bg-indigo-100 dark:hover:bg-indigo-600/40 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/30 px-3 py-1.5 rounded-lg text-xs font-semibold transition inline-flex items-center gap-1 shadow-sm">
                                    🖨️ طباعة
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="p-8 text-center text-slate-400 dark:text-slate-500">
                                لا توجد فواتير مسجلة في السجل حتى الآن
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sales->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/40">
                {{ $sales->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
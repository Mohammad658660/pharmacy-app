@extends('layouts.app') 

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex justify-between items-center bg-slate-900 border border-slate-800 p-4 rounded-xl">
        <h1 class="text-xl font-bold text-slate-100 flex items-center gap-2">
            📋 سجل الفواتير والمبيعات
        </h1>
        <a href="/sales" class="bg-emerald-600 hover:bg-emerald-500 text-white text-sm px-4 py-2 rounded-lg font-medium transition-colors">
            + فاتورة جديدة
        </a>
    </div>

    <!-- Invoices Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-slate-300 text-sm">
                <thead class="bg-slate-950 text-slate-400 border-b border-slate-800 uppercase text-xs">
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
                        <th class="p-4">الخيارات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-slate-800/50 transition-colors">
                            <td class="p-4 font-mono text-slate-500">{{ $sale->id }}</td>
                            <td class="p-4">
                                <span class="bg-slate-800 text-purple-400 px-2.5 py-1 rounded-md text-xs font-mono border border-slate-700">
                                    {{ $sale->invoice_number }}
                                </span>
                            </td>
                            <td class="p-4 font-medium text-slate-200">{{ $sale->customer_name ?? 'زبون عام' }}</td>
                            <td class="p-4 font-mono">{{ number_format($sale->total_amount) }}</td>
                            <td class="p-4 font-mono text-rose-400">{{ number_format($sale->discount) }}</td>
                            <td class="p-4 font-mono font-bold text-emerald-400">{{ number_format($sale->final_amount) }} د.ع</td>
                            <td class="p-4">
                                @if($sale->payment_method === 'credit')
                                    <span class="bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs px-2.5 py-1 rounded-full">آجل (دين)</span>
                                @else
                                    <span class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs px-2.5 py-1 rounded-full">نقدًا (كاش)</span>
                                @endif
                            </td>
                            <td class="p-4 text-xs text-slate-400 dir-ltr text-right">
                                {{ $sale->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="p-4">
                                <div class="space-y-1">
                                    @foreach($sale->items as $item)
                                        <div class="text-xs text-slate-400 bg-slate-950/50 px-2 py-1 rounded border border-slate-800/60 inline-block mb-1">
                                            <span class="text-slate-200 font-medium">{{ $item->trade_name }}</span> 
                                            <span class="text-purple-400 font-mono">(×{{ $item->quantity }})</span>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="p-4">
    <a href="{{ route('sales.print', $sale->id) }}" 
       target="_blank" 
       class="bg-indigo-600/20 hover:bg-indigo-600/40 text-indigo-400 border border-indigo-500/30 text-xs px-3 py-1.5 rounded-lg transition-colors inline-flex items-center gap-1 font-medium">
        🖨️ طباعة
    </a>
</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-slate-500">
                                لا توجد فواتير مسجلة في السجل حتى الآن.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sales->hasPages())
            <div class="p-4 border-t border-slate-800 bg-slate-950/40">
                {{ $sales->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
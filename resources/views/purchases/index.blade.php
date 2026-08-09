@extends('layouts.app')

@section('content')
<!-- استدعاء ملفات التنسيق السريع -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<style>
    /* تخصيص ألوان البحث لتناسب الثيم الغامق */
    .ts-control {
        background-color: #070b14 !important;
        border-color: #1e293b !important;
        color: #ffffff !important;
        border-radius: 0.75rem !important;
        padding: 0.75rem !important;
        font-size: 0.875rem !important;
    }
    .ts-dropdown {
        background-color: #0f172a !important;
        border-color: #1e293b !important;
        color: #ffffff !important;
        border-radius: 0.75rem !important;
        margin-top: 5px !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5) !important;
    }
    .ts-dropdown .option {
        color: #e2e8f0 !important;
        padding: 10px 15px !important;
    }
    .ts-dropdown .option.active, .ts-dropdown .option:hover {
        background-color: #1e293b !important;
        color: #60a5fa !important;
    }
    .ts-control input {
        color: #ffffff !important;
    }
    .ts-wrapper.single .ts-control:after {
        border-color: #94a3b8 transparent transparent transparent !important;
    }
</style>

<div class="container mx-auto p-6 text-white">
    @if(session('success'))
        <div class="bg-green-600/90 text-white p-4 rounded-xl mb-6 border border-green-500">
            {{ session('success') }}
        </div>
    @endif

    <!-- نموذج إضافة شحنة جديدة -->
    <div class="bg-[#0f172a] border border-slate-800/80 p-6 rounded-2xl shadow-xl mb-8">
        <h3 class="text-lg font-semibold mb-5 text-gray-200">إضافة شحنة دواء جديدة</h3>
        <form action="{{ route('purchases.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @csrf

            <div class="lg:col-span-3">
                <label class="block mb-2 text-sm text-gray-300 font-medium">اختر الدواء *</label>
                <select id="product_select" name="product_id" required placeholder="ابحث باسم الدواء التجاري أو العلمي...">
                    <option value="">-- اختر الدواء --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->trade_name }} - {{ $product->scientific_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block mb-2 text-sm text-gray-300 font-medium">الكمية (باكيت) *</label>
                <input type="number" name="quantity_packets" min="0" value="0" required class="w-full p-3 bg-[#070b14] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block mb-2 text-sm text-gray-300 font-medium">الكمية (شريط) (اختياري)</label>
                <input type="number" name="quantity_strips" min="0" value="0" class="w-full p-3 bg-[#070b14] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block mb-2 text-sm text-gray-300 font-medium">سعر التكلفة للباكيت *</label>
                <input type="number" step="0.01" name="cost_price" required class="w-full p-3 bg-[#070b14] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block mb-2 text-sm text-gray-300 font-medium">تاريخ الانتهاء *</label>
                <input type="date" name="expiry_date" required class="w-full p-3 bg-[#070b14] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block mb-2 text-sm text-gray-300 font-medium">رقم الوجبة (Batch) (اختياري)</label>
                <input type="text" name="batch_number" class="w-full p-3 bg-[#070b14] border border-slate-800 rounded-xl text-white focus:outline-none focus:border-blue-500">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium p-3 rounded-xl transition-colors">
                    حفظ الشحنة
                </button>
            </div>
        </form>
    </div>

    <!-- سجل المشتريات -->
    <div class="bg-[#0f172a] border border-slate-800/80 p-6 rounded-2xl shadow-xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-200">سجل الشحنات والمشتريات الأخيرة</h3>
            
            @if($purchases->count() > 0)
                <form action="{{ route('purchases.destroyAll') }}" method="POST" onsubmit="return confirm('هل أنت تأكد من حذف جميع سجلات المشتريات؟ لا يمكن التراجع عن هذا الإجراء.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600/20 text-red-400 hover:bg-red-600 hover:text-white border border-red-800/50 px-4 py-2 rounded-xl text-sm font-medium transition-all">
                        حذف جميع السجلات
                    </button>
                </form>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 text-gray-400 text-sm">
                        <th class="p-3">الدواء</th>
                        <th class="p-3">الكمية (باكيت)</th>
                        <th class="p-3">الكمية (شريط)</th>
                        <th class="p-3">سعر التكلفة</th>
                        <th class="p-3">تاريخ الانتهاء</th>
                        <th class="p-3">تاريخ الإضافة</th>
                        <th class="p-3 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                        <tr class="border-b border-slate-800/50 hover:bg-slate-800/30 transition-colors">
                            <td class="p-3 font-medium">{{ $purchase->product->trade_name ?? 'غير محدد' }}</td>
                            <td class="p-3">{{ $purchase->quantity_packets }} باكيت</td>
                            <td class="p-3">{{ $purchase->quantity_strips }} شريط</td>
                            <td class="p-3">{{ number_format($purchase->cost_price, 2) }}</td>
                            <td class="p-3">{{ $purchase->expiry_date }}</td>
                            <td class="p-3 text-gray-400">{{ $purchase->created_at->format('Y-m-d') }}</td>
                            <td class="p-3 text-center">
                                <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST" class="inline-block" onsubmit="return confirm('هل أنت تأكد من حذف هذا السجل؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-950/40 hover:bg-red-900/80 text-red-400 border border-red-800/50 px-3 py-1 rounded-lg text-xs font-medium transition-colors">
                                        حذف
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-gray-500">لا توجد سجلات مشتريات حتى الآن</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $purchases->links() }}
        </div>
    </div>
</div>

<!-- السكريبت الخاص بـ TomSelect -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        new TomSelect('#product_select', {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });
    });
</script>
@endsection
@extends('layouts.app') {{-- أو الماستر لايوت الخاص بك --}}

@section('content')
<div class="min-h-screen bg-slate-900 text-slate-100 p-4 md:p-6 dir-rtl" dir="rtl">

    <!-- الشريط العلوي والقائمة الرئيسية -->
    <div class="flex flex-wrap items-center justify-between gap-4 pb-6 border-b border-slate-800 mb-6">
        
        <div class="flex items-center gap-3">
            <!-- زر القائمة الرئيسية Mneu Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-5 py-2.5 rounded-lg flex items-center gap-2 shadow-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    <span>القائمة الرئيسية</span>
                </button>

                <!-- القائمة المنسدلة للأنظمة -->
<div x-show="open" x-cloak @click.away="open = false" style="display: none;" class="absolute right-0 mt-2 w-64 bg-slate-800 border border-slate-700 rounded-xl shadow-2xl py-2 z-50">                    <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-700 text-slate-200 transition">
                        <span class="text-emerald-400">💊</span> إدارة الأدوية والمخزون
                    </a>
                    <a href="{{ route('sales.index') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-700 text-slate-200 transition">
                        <span class="text-blue-400">🛒</span> نقطة البيع (POS)
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-700 text-slate-200 transition">
                        <span class="text-purple-400">📦</span> فواتير الشراء
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-700 text-slate-200 transition">
                        <span class="text-amber-400">📋</span> الجرد اليومي والديون
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-700 text-slate-200 transition">
                        <span class="text-rose-400">📊</span> التقارير المالية
                    </a>
                </div>
            </div>

            <!-- روابط سريعة علوية -->
            <div class="hidden lg:flex items-center gap-2">
                <a href="{{ route('sales.index') }}" class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-4 py-2 rounded-lg border border-slate-700 text-sm">شاشة البيع</a>
                <a href="#" class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-4 py-2 rounded-lg border border-slate-700 text-sm">طلبات الشراء</a>
                <a href="#" class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-4 py-2 rounded-lg border border-slate-700 text-sm">الجرد اليومي</a>
            </div>
        </div>

        <div class="text-right">
            <h1 class="text-xl font-bold text-white">أداء المبيعات للفترة المحددة</h1>
            <p class="text-xs text-slate-400">اختر الفترة الزمنية لعرض إحصائيات المبيعات والأرباح</p>
        </div>
    </div>

    <!-- شريط تصفية التواريخ والفلترة -->
    <form method="GET" action="{{ route('dashboard') }}" class="bg-slate-800/60 p-4 rounded-xl border border-slate-700/50 mb-8 flex flex-wrap items-center justify-between gap-4">
        
        <!-- أزرار اختيار سريعة -->
        <div class="flex items-center gap-2">
            <a href="?from_date={{ now()->toDateString() }}&to_date={{ now()->toDateString() }}" class="px-3 py-1.5 bg-slate-700 hover:bg-slate-600 rounded-lg text-xs font-semibold text-slate-200">اليوم</a>
            <a href="?from_date={{ now()->subDays(7)->toDateString() }}&to_date={{ now()->toDateString() }}" class="px-3 py-1.5 bg-slate-700 hover:bg-slate-600 rounded-lg text-xs font-semibold text-slate-200">أسبوع</a>
            <a href="?from_date={{ now()->startOfMonth()->toDateString() }}&to_date={{ now()->toDateString() }}" class="px-3 py-1.5 bg-emerald-600/30 text-emerald-400 border border-emerald-500/30 rounded-lg text-xs font-semibold">شهر</a>
            <a href="?from_date={{ now()->startOfYear()->toDateString() }}&to_date={{ now()->toDateString() }}" class="px-3 py-1.5 bg-slate-700 hover:bg-slate-600 rounded-lg text-xs font-semibold text-slate-200">سنة</a>
        </div>

        <!-- حقول التاريخ -->
        <div class="flex items-center gap-3 text-sm">
            <div class="flex items-center gap-2">
                <label class="text-slate-400 text-xs">من:</label>
                <input type="date" name="from_date" value="{{ $fromDate }}" class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-slate-200 text-xs focus:outline-none focus:border-emerald-500">
            </div>
            <div class="flex items-center gap-2">
                <label class="text-slate-400 text-xs">إلى:</label>
                <input type="date" name="to_date" value="{{ $toDate }}" class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-slate-200 text-xs focus:outline-none focus:border-emerald-500">
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-1.5 rounded-lg text-xs font-bold transition">عرض</button>
        </div>
    </form>

    <!-- شبكة البطاقات الإحصائية (Stats Grid) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- 1. إجمالي المبيعات -->
        <div class="bg-slate-800 border border-slate-700/80 p-5 rounded-2xl relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-xs font-medium text-slate-400">إجمالي المبيعات</span>
                    <h3 class="text-2xl font-black text-emerald-400 mt-2">{{ number_format($totalSales, 2) }} <span class="text-xs text-slate-400">د.ع</span></h3>
                    <p class="text-[11px] text-slate-500 mt-1">من {{ $salesCount }} فاتورة بيع</p>
                </div>
                <div class="p-3 bg-emerald-500/10 text-emerald-400 rounded-xl">
                    💰
                </div>
            </div>
        </div>

        <!-- 2. صافي الربح -->
        <div class="bg-slate-800 border border-slate-700/80 p-5 rounded-2xl relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-xs font-medium text-slate-400">صافي الربح المتوقع</span>
                    <h3 class="text-2xl font-black text-cyan-400 mt-2">{{ number_format($netProfit, 2) }} <span class="text-xs text-slate-400">د.ع</span></h3>
                    <p class="text-[11px] text-slate-500 mt-1">الربح التقريبي بعد المصروفات</p>
                </div>
                <div class="p-3 bg-cyan-500/10 text-cyan-400 rounded-xl">
                    📈
                </div>
            </div>
        </div>

        <!-- 3. قيمة المخزون الكلية -->
        <div class="bg-slate-800 border border-slate-700/80 p-5 rounded-2xl relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-xs font-medium text-slate-400">قيمة المخزون الإجمالية</span>
                    <h3 class="text-2xl font-black text-amber-400 mt-2">{{ number_format($inventoryValue, 2) }} <span class="text-xs text-slate-400">د.ع</span></h3>
                    <p class="text-[11px] text-slate-500 mt-1">حسب سعر البيع الحالي</p>
                </div>
                <div class="p-3 bg-amber-500/10 text-amber-400 rounded-xl">
                    🏭
                </div>
            </div>
        </div>

        <!-- 4. عدد الأصناف -->
        <div class="bg-slate-800 border border-slate-700/80 p-5 rounded-2xl relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-xs font-medium text-slate-400">عدد الأصناف في المخزن</span>
                    <h3 class="text-2xl font-black text-white mt-2">{{ number_format($totalProducts) }}</h3>
                    <p class="text-[11px] text-slate-500 mt-1">إجمالي الأدوية والمواد المسجلة</p>
                </div>
                <div class="p-3 bg-slate-700 text-slate-300 rounded-xl">
                    📦
                </div>
            </div>
        </div>

        <!-- 5. نسبة قريبة الانتهاء -->
        <div class="bg-slate-800 border border-slate-700/80 p-5 rounded-2xl relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-xs font-medium text-slate-400">نسبة المخزون قريب الانتهاء</span>
                    <h3 class="text-2xl font-black text-rose-400 mt-2">{{ $expiryPercentage }}%</h3>
                    <p class="text-[11px] text-slate-500 mt-1">خلال الـ 90 يوماً القادمة</p>
                </div>
                <div class="p-3 bg-rose-500/10 text-rose-400 rounded-xl">
                    ⚠️
                </div>
            </div>
        </div>

        <!-- 6. ديون الموردين -->
        <div class="bg-slate-800 border border-slate-700/80 p-5 rounded-2xl relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-xs font-medium text-slate-400">إجمالي ديون الموردين</span>
                    <h3 class="text-2xl font-black text-purple-400 mt-2">{{ number_format($totalDebts, 2) }} <span class="text-xs text-slate-400">د.ع</span></h3>
                    <p class="text-[11px] text-slate-500 mt-1">المبالغ غير المسددة للمكاتب</p>
                </div>
                <div class="p-3 bg-purple-500/10 text-purple-400 rounded-xl">
                    👥
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
@extends('layouts.app') <!-- أو الـ Layout الرئيسي اللي تستخدمه -->

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-white">معلومات الصيدلية والطباعة</h1>
        <a href="{{ route('settings.index') }}" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-lg text-sm hover:bg-slate-700">← العودة للإعدادات</a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-500/20 text-emerald-300 p-4 rounded-lg mb-6 text-sm border border-emerald-500/30">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('settings.pharmacy.update') }}" method="POST" enctype="multipart/form-data" class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-4">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-slate-300 text-sm mb-1">اسم الصيدلية</label>
                <input type="text" name="pharmacy_name" value="{{ $setting->pharmacy_name ?? '' }}" required class="w-full bg-slate-950 border border-slate-700 text-white rounded-lg p-2.5 text-sm focus:outline-none focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-slate-300 text-sm mb-1">رقم الهاتف</label>
                <input type="text" name="phone" value="{{ $setting->phone ?? '' }}" class="w-full bg-slate-950 border border-slate-700 text-white rounded-lg p-2.5 text-sm focus:outline-none focus:border-emerald-500">
            </div>
        </div>

        <div>
            <label class="block text-slate-300 text-sm mb-1">العنوان</label>
            <input type="text" name="address" value="{{ $setting->address ?? '' }}" class="w-full bg-slate-950 border border-slate-700 text-white rounded-lg p-2.5 text-sm focus:outline-none focus:border-emerald-500">
        </div>

        <div>
            <label class="block text-slate-300 text-sm mb-1">شعار الصيدلية (Logo)</label>
            <input type="file" name="logo" class="w-full bg-slate-950 border border-slate-700 text-slate-300 rounded-lg p-2 text-sm focus:outline-none">
            @if(isset($setting->logo))
                <img src="{{ asset('storage/' . $setting->logo) }}" class="h-12 mt-2 rounded border border-slate-700">
            @endif
        </div>

        <div>
            <label class="block text-slate-300 text-sm mb-1">ملاحظة أسفل الفاتورة (مثلاً: البضاعة المباعة لا ترد ولا تستبدل بعد 3 أيام)</label>
            <textarea name="invoice_footer" rows="3" class="w-full bg-slate-950 border border-slate-700 text-white rounded-lg p-2.5 text-sm focus:outline-none focus:border-emerald-500">{{ $setting->invoice_footer ?? '' }}</textarea>
        </div>

        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-medium p-3 rounded-lg text-sm transition-colors">
            حفظ التغييرات
        </button>
    </form>
</div>
@endsection
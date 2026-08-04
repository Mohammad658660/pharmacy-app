@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">

    <!-- Header -->
    <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl">
        <h1 class="text-2xl font-bold text-slate-100 flex items-center gap-2">
            ⚙️ لوحة إعدادات النظام
        </h1>
        <p class="text-slate-400 text-sm mt-1">اختر القسم الذي تريد التحكم بإعداداته</p>
    </div>

    <!-- شبكة الكروت (Cards) -->
   <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

    <!-- 1️⃣ كارت إدارة الحسابات والموظفين -->
    <a href="{{ route('settings.users.index') }}" class="group bg-slate-900 border border-slate-800 p-6 rounded-2xl hover:border-indigo-500/50 transition flex flex-col justify-between">
        <div>
            <div class="w-12 h-12 rounded-xl bg-indigo-600/10 text-indigo-400 flex items-center justify-center text-xl font-bold mb-4">
                👥
            </div>
            <h3 class="text-lg font-bold text-slate-100 mb-1">إدارة الحسابات والموظفين</h3>
            <p class="text-xs text-slate-400 leading-relaxed">
                إضافة كاشير جديد، تغيير كلمة المرور، وتحديد صلاحيات المستخدمين.
            </p>
        </div>
        <div class="mt-6 text-xs text-indigo-400 font-medium flex items-center gap-1">
            الدخول للقسم ←
        </div>
    </a>

    <!-- 2️⃣ كارت معلومات الصيدلية والطباعة -->
    <a href="{{ route('settings.pharmacy.index') }}" class="group bg-slate-900 border border-slate-800 p-6 rounded-2xl hover:border-emerald-500/50 transition flex flex-col justify-between">
        <div>
            <div class="w-12 h-12 rounded-xl bg-emerald-600/10 text-emerald-400 flex items-center justify-center text-xl font-bold mb-4">
                🏥
            </div>
            <h3 class="text-lg font-bold text-slate-100 mb-1">معلومات الصيدلية والطباعة</h3>
            <p class="text-xs text-slate-400 leading-relaxed">
                اسم الصيدلية، الشعار، رقم الهاتف، وإعدادات طابعة الفواتير.
            </p>
        </div>
        <div class="mt-6 text-xs text-emerald-400 font-medium flex items-center gap-1">
            الدخول للقسم ←
        </div>
    </a>

    <!-- 3️⃣ كارت مظهر النظام (مستقل) -->
    <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl flex flex-col justify-between">
        <div>
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-xl font-bold mb-4">
                🎨
            </div>
            <h3 class="text-lg font-bold text-slate-100 mb-1">مظهر النظام</h3>
            <p class="text-xs text-slate-400 leading-relaxed mb-4">
                تبديل ألوان الواجهة بين الداكن، الفاتح، أو التلقائي.
            </p>
            
            <div class="grid grid-cols-3 gap-2">
                <button type="button" onclick="setTheme('dark')" id="theme-dark-btn" class="flex flex-col items-center py-2 px-1 rounded-xl border border-slate-700 bg-slate-800 hover:border-amber-500 transition text-slate-200">
                    <span class="text-lg">🌙</span>
                    <span class="text-[10px] font-bold mt-1">داكن</span>
                </button>

                <button type="button" onclick="setTheme('light')" id="theme-light-btn" class="flex flex-col items-center py-2 px-1 rounded-xl border border-slate-700 bg-slate-800 hover:border-amber-500 transition text-slate-200">
                    <span class="text-lg">☀️</span>
                    <span class="text-[10px] font-bold mt-1">فاتح</span>
                </button>

                <button type="button" onclick="setTheme('auto')" id="theme-auto-btn" class="flex flex-col items-center py-2 px-1 rounded-xl border border-slate-700 bg-slate-800 hover:border-amber-500 transition text-slate-200">
                    <span class="text-lg">⏰</span>
                    <span class="text-[10px] font-bold mt-1">تلقائي</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 4️⃣ كارت النسخ الاحتياطي (قريباً/أو مستقبلاً) -->
    <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl flex flex-col justify-between opacity-75">
        <div>
            <div class="w-12 h-12 rounded-xl bg-slate-800 text-slate-400 flex items-center justify-center text-xl font-bold mb-4">
                💾
            </div>
            <h3 class="text-lg font-bold text-slate-100 mb-1">النسخ الاحتياطي وقاعدة البيانات</h3>
            <p class="text-xs text-slate-400 leading-relaxed">
                تنزيل نسخة احتياطية من البيانات أو استعادة النسخ السابقة.
            </p>
        </div>
        <div class="mt-6">
            <span class="text-[10px] bg-slate-800 text-slate-400 px-2.5 py-1 rounded-md">قريباً</span>
        </div>
    </div>

</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">

    <!-- Header -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl shadow-sm">
        <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
            ⚙️ لوحة إعدادات النظام
        </h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
            اختر القسم الذي تريد التحكم بإعداداته
        </p>
    </div>

    <!-- شبكة الكروت (Cards) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- 1- كارت إدارة الحسابات والموظفين -->
        <a href="{{ route('settings.users.index') }}" class="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl flex flex-col justify-between hover:border-indigo-500/50 transition shadow-sm">
            <div>
                <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-600/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl mb-4">
                    👥
                </div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-1">إدارة الحسابات والموظفين</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    إضافة كاشير جديد، تغيير كلمة المرور، وتحديد صلاحيات المستخدمين.
                </p>
            </div>
            <div class="mt-6 text-xs text-indigo-600 dark:text-indigo-400 font-medium flex items-center gap-1 group-hover:translate-x-1 transition-transform dir-ltr justify-end">
                ← الدخول للقسم
            </div>
        </a>

        <!-- 2- كارت معلومات الصيدلية والطباعة -->
        <a href="{{ route('settings.pharmacy.index') }}" class="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl flex flex-col justify-between hover:border-emerald-500/50 transition shadow-sm">
            <div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-600/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl mb-4">
                    🏥
                </div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-1">معلومات الصيدلية والطباعة</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    اسم الصيدلية، الشعار، رقم الهاتف، وإعدادات طابعة الفواتير.
                </p>
            </div>
            <div class="mt-6 text-xs text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1 group-hover:translate-x-1 transition-transform dir-ltr justify-end">
                ← الدخول للقسم
            </div>
        </a>

        <!-- 3- كارت مظهر النظام -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl flex flex-col justify-between shadow-sm">
            <div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl mb-4">
                    🎨
                </div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-1">مظهر النظام</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed mb-4">
                    تبديل ألوان الواجهة بين الداكن، الفاتح، أو التلقائي.
                </p>

                <div class="grid grid-cols-3 gap-2 bg-slate-50 dark:bg-slate-950 p-1.5 rounded-xl border border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="setTheme('dark')" id="theme-dark-btn" class="flex flex-col items-center justify-center py-2.5 px-1 rounded-lg hover:bg-white dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 transition">
                        <span class="text-lg">🌙</span>
                        <span class="text-[10px] font-bold mt-1">داكن</span>
                    </button>
                    <button type="button" onclick="setTheme('light')" id="theme-light-btn" class="flex flex-col items-center justify-center py-2.5 px-1 rounded-lg hover:bg-white dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 transition">
                        <span class="text-lg">🌞</span>
                        <span class="text-[10px] font-bold mt-1">فاتح</span>
                    </button>
                    <button type="button" onclick="setTheme('auto')" id="theme-auto-btn" class="flex flex-col items-center justify-center py-2.5 px-1 rounded-lg hover:bg-white dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 transition">
                        <span class="text-lg">⏰</span>
                        <span class="text-[10px] font-bold mt-1">تلقائي</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 4- كارت النسخ الاحتياطي (قريباً) -->
        <div class="bg-slate-50/80 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl flex flex-col justify-between opacity-80 shadow-sm">
            <div>
                <div class="w-12 h-12 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-500 dark:text-slate-400 flex items-center justify-center text-xl mb-4">
                    💾
                </div>
                <h3 class="text-lg font-bold text-slate-700 dark:text-slate-300 mb-1">النسخ الاحتياطي وقاعدة البيانات</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    تنزيل نسخة احتياطية من البيانات أو استعادة النسخ السابقة.
                </p>
            </div>
            <div class="mt-6">
                <span class="text-[10px] bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400 px-2.5 py-1 rounded-md font-medium border border-slate-300 dark:border-slate-700">
                    قريباً
                </span>
            </div>
        </div>

    </div>

</div>
@endsection
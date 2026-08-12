<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الرئيسية - نظام إدارة الصيدلية والعيادة</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- سكربت تطبيق الثيم فوراً لتجنب الوميض -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('app_theme') || 'auto';
            let isDark = true;

            if (savedTheme === 'dark') {
                isDark = true;
            } else if (savedTheme === 'light') {
                isDark = false;
            } else if (savedTheme === 'auto') {
                const hour = new Date().getHours();
                isDark = (hour < 6 || hour >= 18);
            }

            if (isDark) {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            } else {
                document.documentElement.classList.add('light');
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    <style>
        /* التنسيق الأساسي للوضع الداكن */
        body {
            background-color: #0b0f19;
            color: #f1f5f9;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* تنسيقات المظهر الفاتح الأنيق والمريح للعين */
        html.light body {
            background-color: #f8fafc !important;
            color: #0f172a !important;
        }

        /* تحويل الكروت والهيدر إلى الأبيض مع إعطائها ظلال ناعمة */
        html.light header,
        html.light .bg-slate-900,
        html.light .bg-slate-950,
        html.light .bg-slate-800,
        html.light [class*="bg-slate-"] {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
            color: #0f172a !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
        }

        /* ضبط ألوان العناوين والنصوص بالوضع الفاتح */
        html.light h1, html.light h2, html.light h3, html.light h4,
        html.light .text-slate-100,
        html.light .text-white {
            color: #0f172a !important;
        }

        html.light p,
        html.light span,
        html.light .text-slate-400,
        html.light .text-slate-300,
        html.light .text-slate-500 {
            color: #475569 !important;
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen font-sans flex flex-col p-6">

    <div class="max-w-7xl mx-auto w-full space-y-8">

     <!-- الشريط العلوي (الهيدر) -->
<header class="bg-white border-b border-slate-200 dark:bg-slate-900 dark:border-slate-800 py-3 px-6 flex items-center justify-between">
    
    <!-- اسم النظام والعنوان -->
    <div class="flex items-center gap-2 text-xl font-bold text-slate-800 dark:text-white">
        <span>💊</span>
        <span>نظام إدارة الصيدلية والعيادة</span>
    </div>

    <!-- أزرار التحكم بالمظهر والأزرار العامة -->
    <div class="flex items-center gap-4">

        <!-- أزرار تبديل الثيم (فاتح / تلقائي / داكن) -->
        <div class="flex items-center bg-slate-100 dark:bg-slate-800 p-1 rounded-xl gap-1 border border-slate-200 dark:border-slate-700">
            <button id="theme-light-btn" onclick="setTheme('light')" class="px-2.5 py-1 text-xs rounded-lg transition-colors flex items-center gap-1 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
                🌞 فاتح
            </button>
            <button id="theme-auto-btn" onclick="setTheme('auto')" class="px-2.5 py-1 text-xs rounded-lg transition-colors flex items-center gap-1 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
                ⏰ تلقائي
            </button>
            <button id="theme-dark-btn" onclick="setTheme('dark')" class="px-2.5 py-1 text-xs rounded-lg transition-colors flex items-center gap-1 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
                🌙 داكن
            </button>
        </div>

        <!-- زر لوحة الإحصائيات وتسجيل الخروج -->
        <div class="flex items-center gap-2">
            <a href="{{ route('dashboard') }}" class="bg-slate-800 hover:bg-slate-700 text-white dark:bg-slate-700 dark:hover:bg-slate-600 px-3 py-1.5 rounded-xl text-xs font-medium transition-colors flex items-center gap-1.5 border border-slate-700 dark:border-slate-600">
                📊 لوحة الإحصائيات
            </a>

            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 px-3 py-1.5 rounded-xl text-xs font-medium transition-colors flex items-center gap-1.5 border border-rose-500/20">
                    🚪 تسجيل الخروج
                </button>
            </form>
        </div>

    </div>

</header>

        <!-- شبكة الكروت والمربعات الكبيرة لكافة أجزاء النظام -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- 1. شاشة البيع / الكاشير -->
            <a href="/sales" class="group bg-slate-900 border border-slate-800 hover:border-purple-500/50 p-6 rounded-2xl transition-all hover:shadow-xl hover:shadow-purple-500/10 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-3xl bg-purple-950/60 p-3 rounded-xl border border-purple-500/20">🛒</span>
                    <span class="text-xs bg-purple-900/40 text-purple-300 px-3 py-1 rounded-full border border-purple-500/20">كاشير</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-100 group-hover:text-purple-400 transition-colors">شاشة البيع</h2>
                    <p class="text-slate-400 text-xs mt-2">تسجيل النقد والآجل فوراً</p>
                </div>
                <div class="mt-4 text-purple-400 text-sm font-semibold flex items-center gap-1">
                    <span>دخول الشاشة</span> &larr;
                </div>
            </a>

            <!-- 2. المبيعات -->
            <a href="/sales-history" class="group bg-slate-900 border border-slate-800 hover:border-indigo-500/50 p-6 rounded-2xl transition-all hover:shadow-xl hover:shadow-indigo-500/10 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-3xl bg-indigo-950/60 p-3 rounded-xl border border-indigo-500/20">📊</span>
                    <span class="text-xs bg-indigo-900/40 text-indigo-300 px-3 py-1 rounded-full border border-indigo-500/20">تقارير</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-100 group-hover:text-indigo-400 transition-colors">سجل المبيعات</h2>
                    <p class="text-slate-400 text-xs mt-2">مراجعة التقارير الفواتير اليومية</p>
                </div>
                <div class="mt-4 text-indigo-400 text-sm font-semibold flex items-center gap-1">
                    <span>عرض المبيعات</span> &larr;
                </div>
            </a>

            <!-- 3. المشتريات -->
            <a href="/purchases" class="group bg-slate-900 border border-slate-800 hover:border-cyan-500/50 p-6 rounded-2xl transition-all hover:shadow-xl hover:shadow-cyan-500/10 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-3xl bg-cyan-950/60 p-3 rounded-xl border border-cyan-500/20">📦</span>
                    <span class="text-xs bg-cyan-900/40 text-cyan-300 px-3 py-1 rounded-full border border-cyan-500/20">مخزون</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-100 group-hover:text-cyan-400 transition-colors">المشتريات</h2>
                    <p class="text-slate-400 text-xs mt-2">إضافة كميات والشحنات المخزنية</p>
                </div>
                <div class="mt-4 text-cyan-400 text-sm font-semibold flex items-center gap-1">
                    <span>سجل المشتريات</span> &larr;
                </div>
            </a>

            <!-- 4. المنتجات -->
            <a href="/products" class="group bg-slate-900 border border-slate-800 hover:border-teal-500/50 p-6 rounded-2xl transition-all hover:shadow-xl hover:shadow-teal-500/10 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-3xl bg-teal-950/60 p-3 rounded-xl border border-teal-500/20">💊</span>
                    <span class="text-xs bg-teal-900/40 text-teal-300 px-3 py-1 rounded-full border border-teal-500/20">دليل</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-100 group-hover:text-teal-400 transition-colors">المنتجات والأدوية</h2>
                    <p class="text-slate-400 text-xs mt-2">قائمة الأدوية والأسعار والباركود</p>
                </div>
                <div class="mt-4 text-teal-400 text-sm font-semibold flex items-center gap-1">
                    <span>قائمة الأدوية</span> &larr;
                </div>
            </a>

            <!-- 5. سجل الديون النشطة -->
            <a href="{{ route('debts.index') }}" class="group bg-slate-900 border border-slate-800 hover:border-emerald-500/50 p-6 rounded-2xl transition-all hover:shadow-xl hover:shadow-emerald-500/10 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-3xl bg-emerald-950/60 p-3 rounded-xl border border-emerald-500/20">📋</span>
                    <span class="text-xs bg-emerald-900/40 text-emerald-300 px-3 py-1 rounded-full border border-emerald-500/20">ذمم</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-100 group-hover:text-emerald-400 transition-colors">سجل الديون النشطة</h2>
                    <p class="text-slate-400 text-xs mt-2">ديون العملاء والعيادات وتسديدها</p>
                </div>
                <div class="mt-4 text-emerald-400 text-sm font-semibold flex items-center gap-1">
                    <span>عرض الديون</span> &larr;
                </div>
            </a>

            <!-- 6. أرشيف الديون المسددة -->
            <a href="{{ route('debts.archive') }}" class="group bg-slate-900 border border-slate-800 hover:border-blue-500/50 p-6 rounded-2xl transition-all hover:shadow-xl hover:shadow-blue-500/10 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-3xl bg-blue-950/60 p-3 rounded-xl border border-blue-500/20">📁</span>
                    <span class="text-xs bg-blue-900/40 text-blue-300 px-3 py-1 rounded-full border border-blue-500/20">أرشيف</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-100 group-hover:text-blue-400 transition-colors">أرشيف الديون المسددة</h2>
                    <p class="text-slate-400 text-xs mt-2">الديون المكتملة وسجلها بالكامل</p>
                </div>
                <div class="mt-4 text-blue-400 text-sm font-semibold flex items-center gap-1">
                    <span>فتح الأرشيف</span> &larr;
                </div>
            </a>

            <!-- 7. الأقساط -->
            <a href="/installments" class="group bg-slate-900 border border-slate-800 hover:border-amber-500/50 p-6 rounded-2xl transition-all hover:shadow-xl hover:shadow-amber-500/10 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-3xl bg-amber-950/60 p-3 rounded-xl border border-amber-500/20">📅</span>
                    <span class="text-xs bg-amber-900/40 text-amber-300 px-3 py-1 rounded-full border border-amber-500/20">جدولة</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-100 group-hover:text-amber-400 transition-colors">الأقساط</h2>
                    <p class="text-slate-400 text-xs mt-2">إدارة وجدولة الدفعات الشهرية أو الأسبوعية</p>
                </div>
                <div class="mt-4 text-amber-400 text-sm font-semibold flex items-center gap-1">
                    <span>عرض الأقساط</span> &larr;
                </div>
            </a>

            <!-- 8. العملاء والعيادات -->
            <a href="/customers" class="group bg-slate-900 border border-slate-800 hover:border-sky-500/50 p-6 rounded-2xl transition-all hover:shadow-xl hover:shadow-sky-500/10 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-3xl bg-sky-950/60 p-3 rounded-xl border border-sky-500/20">👥</span>
                    <span class="text-xs bg-sky-900/40 text-sky-300 px-3 py-1 rounded-full border border-sky-500/20">دليل</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-100 group-hover:text-sky-400 transition-colors">العملاء والعيادات</h2>
                    <p class="text-slate-400 text-xs mt-2">دليل أرقام وبيانات الزبائن والعيادات المتعامل معها</p>
                </div>
                <div class="mt-4 text-sky-400 text-sm font-semibold flex items-center gap-1">
                    <span>إدارة العملاء</span> &larr;
                </div>
            </a>

            <!-- 9. الموردون -->
            <a href="/suppliers" class="group bg-slate-900 border border-slate-800 hover:border-orange-500/50 p-6 rounded-2xl transition-all hover:shadow-xl hover:shadow-orange-500/10 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-3xl bg-orange-950/60 p-3 rounded-xl border border-orange-500/20">🚚</span>
                    <span class="text-xs bg-orange-900/40 text-orange-300 px-3 py-1 rounded-full border border-orange-500/20">شركات</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-100 group-hover:text-orange-400 transition-colors">الموردون</h2>
                    <p class="text-slate-400 text-xs mt-2">بيانات شركات المكاتب والموردين ومستحقاتهم</p>
                </div>
                <div class="mt-4 text-orange-400 text-sm font-semibold flex items-center gap-1">
                    <span>عرض الموردين</span> &larr;
                </div>
            </a>

            <!-- 10. المواد التالفة (تم ربطها بـ route) -->
            <a href="{{ route('damaged.index') }}" class="group bg-slate-900 border border-slate-800 hover:border-red-500/50 p-6 rounded-2xl transition-all hover:shadow-xl hover:shadow-red-500/10 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-3xl bg-red-950/60 p-3 rounded-xl border border-red-500/20">🗑️</span>
                    <span class="text-xs bg-red-900/40 text-red-300 px-3 py-1 rounded-full border border-red-500/20">خسائر</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-100 group-hover:text-red-400 transition-colors">المواد التالفة</h2>
                    <p class="text-slate-400 text-xs mt-2">تسجيل الأدوية منتهية الصلاحية أو التالفة</p>
                </div>
                <div class="mt-4 text-red-400 text-sm font-semibold flex items-center gap-1">
                    <span>سجل التالف</span> &larr;
                </div>
            </a>
<!-- كارت لوحة الإحصائيات (الداشبورد) -->
<a href="{{ route('dashboard') }}" class="bg-slate-800/80 hover:bg-slate-800 border border-slate-700/60 hover:border-emerald-500/50 rounded-2xl p-6 transition-all duration-300 group shadow-lg flex flex-col justify-between">
    <div class="flex items-center justify-between">
        <span class="text-xs bg-emerald-500/10 text-emerald-400 px-3 py-1 rounded-full border border-emerald-500/20 font-semibold">إحصائيات</span>
        <div class="w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center text-emerald-400 text-2xl group-hover:scale-110 transition-transform">
            📊
        </div>
    </div>
    <div class="mt-6">
        <h3 class="text-xl font-bold text-white group-hover:text-emerald-400 transition-colors">لوحة الإحصائيات</h3>
        <p class="text-xs text-slate-400 mt-2">متابعة المبيعات، الأرباح، وقيمة المخزون</p>
    </div>
    <div class="mt-4 text-xs font-bold text-emerald-400 flex items-center gap-1">
        عرض اللوحة ←
    </div>
</a>
            <!-- 11. الإعدادات -->
            <a href="/settings" class="group bg-slate-900 border border-slate-800 hover:border-slate-500/50 p-6 rounded-2xl transition-all hover:shadow-xl hover:shadow-slate-500/10 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-3xl bg-slate-800 p-3 rounded-xl border border-slate-700">⚙️</span>
                    <span class="text-xs bg-slate-800 text-slate-300 px-3 py-1 rounded-full border border-slate-700">نظام</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-100 group-hover:text-slate-300 transition-colors">الإعدادات</h2>
                    <p class="text-slate-400 text-xs mt-2">ضبط بيانات الصيدلية، الطباعة، والصلاحيات</p>
                </div>
                <div class="mt-4 text-slate-300 text-sm font-semibold flex items-center gap-1">
                    <span>الضبط والإعدادات</span> &larr;
                </div>
            </a>

        </div>

        <!-- الفوتر -->
        <footer class="text-center text-xs text-slate-600 border-t border-slate-800/60 pt-6 mt-12">
            <p>نظام إدارة الصيدلية والعيادة &copy; {{ date('Y') }}</p>
        </footer>

    </div>

</body>
</html>
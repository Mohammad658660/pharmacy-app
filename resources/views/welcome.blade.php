<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الرئيسية - نظام إدارة الصيدلية والعيادة</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- سكريبت تطبيق الثيم فوراً لتجنب الوميض -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('app_theme') || 'dark';
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

        /* تنسيقات المظهر الفاتح الأنيق والمريح للعين مع ظلال فخمة */
        html.light body {
            background-color: #f8fafc !important;
            color: #0f172a !important;
        }

        /* تحويل الكروت والهيدر إلى الأبيض مع إعطائها ظلال ناعمة (Soft Shadows) */
        html.light header,
        html.light .bg-slate-900,
        html.light .bg-slate-950,
        html.light .bg-slate-800,
        html.light [class*="bg-slate-"] {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
            color: #0f172a !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04), 0 4px 6px -4px rgba(0, 0, 0, 0.02) !important;
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
<body class="bg-slate-950 text-slate-100 min-h-screen font-sans flex flex-col justify-between p-6">

    <div class="max-w-7xl mx-auto w-full space-y-8">
        
        <!-- الهيدر الرئيسي -->
       <!-- الهيدر الرئيسي -->
<header class="flex items-center justify-between border-b border-slate-800 pb-4">
    <!-- العنوان وتوقيت النظام بجهة اليمين -->
    <div>
        <h1 class="text-3xl font-bold text-slate-100 flex items-center gap-2">
            <span>💊</span> نظام إدارة الصيدلية والعيادة
        </h1>
        <p class="text-slate-400 text-sm mt-1">مرحباً بك، اختر الشاشة أو القسم الذي تريد الانتقال إليه</p>
    </div>

    <!-- توقيت النظام + زر تسجيل الخروج بجهة اليسار -->
    <div class="flex items-center gap-6">
        <div class="text-left text-xs text-slate-500">
            <p>توقيت النظام: Asia/Baghdad</p>
        </div>

        <form action="{{ route('logout') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="bg-rose-600/10 hover:bg-rose-600 text-rose-400 hover:text-white border border-rose-500/30 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 flex items-center gap-2 shadow-lg shadow-rose-950/20">
                <span>🚪</span> تسجيل الخروج
            </button>
        </form>
    </div>
</header>

        <!-- شبكة الكروت والمربعات الكبيرة لكافة أجزاء النظام -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

            <!-- 1. شاشة البيع / الكاشير -->
            <a href="/sales" class="group bg-slate-900 border border-slate-800 hover:border-purple-500/50 p-6 rounded-2xl shadow-xl transition-all duration-200 hover:-translate-y-1 block">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-3xl bg-purple-950/60 p-3 rounded-xl border border-purple-800/40">🛒</span>
                    <span class="text-xs bg-purple-900/40 text-purple-300 border border-purple-700/50 px-2.5 py-1 rounded-full font-medium">جاهز للعمل</span>
                </div>
                <h2 class="text-xl font-bold text-slate-100 group-hover:text-purple-400 transition">شاشة البيع</h2>
                <p class="text-slate-400 text-xs mt-2">إنشاء فواتير مبيعات جديدة، تسجيل النقد والآجل فوراً.</p>
                <div class="mt-4 text-purple-400 text-sm font-semibold flex items-center gap-1 group-hover:gap-2 transition-all">
                    دخول الشاشة &larr;
                </div>
            </a>

            <!-- 2. المبيعات -->
            <a href="/sales-history" class="group bg-slate-900 border border-slate-800 hover:border-indigo-500/50 p-6 rounded-2xl shadow-xl transition-all duration-200 hover:-translate-y-1 block">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-3xl bg-indigo-950/60 p-3 rounded-xl border border-indigo-800/40">📊</span>
                    <span class="text-xs bg-indigo-900/40 text-indigo-300 border border-indigo-700/50 px-2.5 py-1 rounded-full font-medium">سجلات</span>
                </div>
                <h2 class="text-xl font-bold text-slate-100 group-hover:text-indigo-400 transition">سجل المبيعات</h2>
                <p class="text-slate-400 text-xs mt-2">عرض واستعراض الفواتير السابقة والتقارير اليومية.</p>
                <div class="mt-4 text-indigo-400 text-sm font-semibold flex items-center gap-1 group-hover:gap-2 transition-all">
                    عرض المبيعات &larr;
                </div>
            </a>

            <!-- 3. المشتريات -->
            <a href="/purchases" class="group bg-slate-900 border border-slate-800 hover:border-cyan-500/50 p-6 rounded-2xl shadow-xl transition-all duration-200 hover:-translate-y-1 block">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-3xl bg-cyan-950/60 p-3 rounded-xl border border-cyan-800/40">🛍️</span>
                    <span class="text-xs bg-cyan-900/40 text-cyan-300 border border-cyan-700/50 px-2.5 py-1 rounded-full font-medium">مخزن</span>
                </div>
                <h2 class="text-xl font-bold text-slate-100 group-hover:text-cyan-400 transition">المشتريات</h2>
                <p class="text-slate-400 text-xs mt-2">إدخال فواتير الشراء وتعديل الكميات المخزنية.</p>
                <div class="mt-4 text-cyan-400 text-sm font-semibold flex items-center gap-1 group-hover:gap-2 transition-all">
                    سجل المشتريات &larr;
                </div>
            </a>

            <!-- 4. المنتجات -->
            <a href="/products" class="group bg-slate-900 border border-slate-800 hover:border-teal-500/50 p-6 rounded-2xl shadow-xl transition-all duration-200 hover:-translate-y-1 block">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-3xl bg-teal-950/60 p-3 rounded-xl border border-teal-800/40">💊</span>
                    <span class="text-xs bg-teal-900/40 text-teal-300 border border-teal-700/50 px-2.5 py-1 rounded-full font-medium">الأدوية</span>
                </div>
                <h2 class="text-xl font-bold text-slate-100 group-hover:text-teal-400 transition">إدارة المنتجات</h2>
                <p class="text-slate-400 text-xs mt-2">إضافة وتعديل الأدوية، أسعار البيع والأسعار الأساسية.</p>
                <div class="mt-4 text-teal-400 text-sm font-semibold flex items-center gap-1 group-hover:gap-2 transition-all">
                    قائمة الأدوية &larr;
                </div>
            </a>

            <!-- 5. سجل الديون النشطة -->
            <a href="{{ route('debts.index') }}" class="group bg-slate-900 border border-slate-800 hover:border-emerald-500/50 p-6 rounded-2xl shadow-xl transition-all duration-200 hover:-translate-y-1 block">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-3xl bg-emerald-950/60 p-3 rounded-xl border border-emerald-800/40">📄</span>
                    <span class="text-xs bg-emerald-900/40 text-emerald-300 border border-emerald-700/50 px-2.5 py-1 rounded-full font-medium">نشط</span>
                </div>
                <h2 class="text-xl font-bold text-slate-100 group-hover:text-emerald-400 transition">الديون النشطة</h2>
                <p class="text-slate-400 text-xs mt-2">متابعة المبالغ المتبقية على الزبائن والعيادات وتسديدها.</p>
                <div class="mt-4 text-emerald-400 text-sm font-semibold flex items-center gap-1 group-hover:gap-2 transition-all">
                    عرض الديون &larr;
                </div>
            </a>

            <!-- 6. أرشيف الديون المسددة -->
            <a href="{{ route('debts.archive') }}" class="group bg-slate-900 border border-slate-800 hover:border-blue-500/50 p-6 rounded-2xl shadow-xl transition-all duration-200 hover:-translate-y-1 block">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-3xl bg-blue-950/60 p-3 rounded-xl border border-blue-800/40">🗄️</span>
                    <span class="text-xs bg-blue-900/40 text-blue-300 border border-blue-700/50 px-2.5 py-1 rounded-full font-medium">مكتمل</span>
                </div>
                <h2 class="text-xl font-bold text-slate-100 group-hover:text-blue-400 transition">أرشيف الديون</h2>
                <p class="text-slate-400 text-xs mt-2">سجل الحسابات والديون التي تم تسديدها بالكامل.</p>
                <div class="mt-4 text-blue-400 text-sm font-semibold flex items-center gap-1 group-hover:gap-2 transition-all">
                    فتح الأرشيف &larr;
                </div>
            </a>

            <!-- 7. الأقساط -->
            <a href="/installments" class="group bg-slate-900 border border-slate-800 hover:border-amber-500/50 p-6 rounded-2xl shadow-xl transition-all duration-200 hover:-translate-y-1 block">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-3xl bg-amber-950/60 p-3 rounded-xl border border-amber-800/40">🗓️</span>
                    <span class="text-xs bg-amber-900/40 text-amber-300 border border-amber-700/50 px-2.5 py-1 rounded-full font-medium">جدولة</span>
                </div>
                <h2 class="text-xl font-bold text-slate-100 group-hover:text-amber-400 transition">الأقساط</h2>
                <p class="text-slate-400 text-xs mt-2">إدارة وجدولة الدفعات الشهرية أو الأسبوعية.</p>
                <div class="mt-4 text-amber-400 text-sm font-semibold flex items-center gap-1 group-hover:gap-2 transition-all">
                    عرض الأقساط &larr;
                </div>
            </a>

            <!-- 8. العملاء -->
            <a href="/customers" class="group bg-slate-900 border border-slate-800 hover:border-sky-500/50 p-6 rounded-2xl shadow-xl transition-all duration-200 hover:-translate-y-1 block">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-3xl bg-sky-950/60 p-3 rounded-xl border border-sky-800/40">👥</span>
                    <span class="text-xs bg-sky-900/40 text-sky-300 border border-sky-700/50 px-2.5 py-1 rounded-full font-medium">دليل</span>
                </div>
                <h2 class="text-xl font-bold text-slate-100 group-hover:text-sky-400 transition">العملاء والعيادات</h2>
                <p class="text-slate-400 text-xs mt-2">دليل أرقام وبيانات الزبائن والعيادات المتعامل معها.</p>
                <div class="mt-4 text-sky-400 text-sm font-semibold flex items-center gap-1 group-hover:gap-2 transition-all">
                    إدارة العملاء &larr;
                </div>
            </a>

            <!-- 9. الموردون -->
            <a href="/suppliers" class="group bg-slate-900 border border-slate-800 hover:border-orange-500/50 p-6 rounded-2xl shadow-xl transition-all duration-200 hover:-translate-y-1 block">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-3xl bg-orange-950/60 p-3 rounded-xl border border-orange-800/40">🚚</span>
                    <span class="text-xs bg-orange-900/40 text-orange-300 border border-orange-700/50 px-2.5 py-1 rounded-full font-medium">شركات</span>
                </div>
                <h2 class="text-xl font-bold text-slate-100 group-hover:text-orange-400 transition">الموردون</h2>
                <p class="text-slate-400 text-xs mt-2">بيانات شركات المكاتب والموردين ومستحقاتهم.</p>
                <div class="mt-4 text-orange-400 text-sm font-semibold flex items-center gap-1 group-hover:gap-2 transition-all">
                    عرض الموردين &larr;
                </div>
            </a>

            <!-- 10. التالف -->
            <a href="/damaged" class="group bg-slate-900 border border-slate-800 hover:border-red-500/50 p-6 rounded-2xl shadow-xl transition-all duration-200 hover:-translate-y-1 block">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-3xl bg-red-950/60 p-3 rounded-xl border border-red-800/40">🗑️</span>
                    <span class="text-xs bg-red-900/40 text-red-300 border border-red-700/50 px-2.5 py-1 rounded-full font-medium">خسائر</span>
                </div>
                <h2 class="text-xl font-bold text-slate-100 group-hover:text-red-400 transition">المواد التالفة</h2>
                <p class="text-slate-400 text-xs mt-2">تسجيل الأدوية منتهية الصلاحية أو التالفة.</p>
                <div class="mt-4 text-red-400 text-sm font-semibold flex items-center gap-1 group-hover:gap-2 transition-all">
                    سجل التالف &larr;
                </div>
            </a>

            <!-- 11. الإعدادات -->
            <a href="/settings" class="group bg-slate-900 border border-slate-800 hover:border-slate-500/50 p-6 rounded-2xl shadow-xl transition-all duration-200 hover:-translate-y-1 block">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-3xl bg-slate-800 p-3 rounded-xl border border-slate-700">⚙️</span>
                    <span class="text-xs bg-slate-800 text-slate-300 border border-slate-700 px-2.5 py-1 rounded-full font-medium">نظام</span>
                </div>
                <h2 class="text-xl font-bold text-slate-100 group-hover:text-slate-300 transition">الإعدادات</h2>
                <p class="text-slate-400 text-xs mt-2">ضبط بيانات الصيدلية، الطباعة، والصلاحيات.</p>
                <div class="mt-4 text-slate-300 text-sm font-semibold flex items-center gap-1 group-hover:gap-2 transition-all">
                    الضبط والإعدادات &larr;
                </div>
            </a>

        </div>

    </div>

    <!-- الفوتر -->
    <footer class="text-center text-xs text-slate-600 border-t border-slate-900 pt-4 mt-8">
        نظام إدارة الصيدلية والعيادة &copy; {{ date('Y') }} - جميع الحقوق محفوظة
    </footer>

</body>
</html>
<!DOCTYPE html>
<html lang="ar" dir="rtl" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'نظام إدارة الصيدلية والعيادة') }}</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </script>

    <!-- سكربت الفحص السريع لمنع وميض الشاشة عند التنقل -->
   <!-- سكربت الفحص السريع لمنع وميض الشاشة عند التنقل -->
<script>
    (function() {
        const savedTheme = localStorage.getItem('app_theme') || 'auto';
        let isDark = false;

        if (savedTheme === 'dark') {
            isDark = true;
        } else if (savedTheme === 'light') {
            isDark = false;
        } else if (savedTheme === 'auto') {
            // التلقائي حسب الوقت: من 6 صباحاً (6) إلى قبل 6 مساءً (18) يكون فاتح، عدا ذلك يكون داكن
            const currentHour = new Date().getHours();
            isDark = (currentHour < 6 || currentHour >= 18);
        }

        if (isDark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    })();
</script>

    <style>
        html {
            transition: background-color 0.2s ease, color 0.2s ease;
        }
        .theme-btn-active {
            border-color: #3b82f6 !important;
            background-color: rgba(59, 130, 246, 0.15) !important;
            color: #60a5fa !important;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col bg-slate-100 text-slate-800 dark:bg-slate-950 dark:text-slate-100">

    <!-- الشريط العلوي (الهيدر) -->
    <header class="bg-white border-b border-slate-200 dark:bg-slate-900 dark:border-slate-800 px-6 py-4 flex justify-between items-center shadow-sm">
        
        <!-- اسم النظام والعنوان -->
        <div class="flex items-center gap-2 text-xl font-bold text-slate-800 dark:text-slate-100">
            <span>💊</span>
            <span>نظام إدارة الصيدلية والعيادة</span>
        </div>

        <!-- أزرار التحكم بالمظهر والأزرار العامة -->
        <div class="flex items-center gap-4">
            
            <!-- أزرار تبديل الثيم الثلاثية (فاتح / تلقائي / داكن) -->
            <div class="flex items-center bg-slate-100 dark:bg-slate-800 p-1 rounded-xl border border-slate-200 dark:border-slate-700">
                <button id="theme-light-btn" onclick="setTheme('light')" class="px-2.5 py-1 text-xs font-semibold rounded-lg transition text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white flex items-center gap-1">
                    🌞 فاتح
                </button>
                <button id="theme-auto-btn" onclick="setTheme('auto')" class="px-2.5 py-1 text-xs font-semibold rounded-lg transition text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white flex items-center gap-1">
                    ⏰ تلقائي
                </button>
                <button id="theme-dark-btn" onclick="setTheme('dark')" class="px-2.5 py-1 text-xs font-semibold rounded-lg transition text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white flex items-center gap-1">
                    🌙 داكن
                </button>
            </div>

            <!-- أزرار الواجهة الرئيسية وتسجيل الخروج -->
            <div class="flex items-center gap-2">
               <a href="{{ route('home.menu') }}" class="bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold px-3 py-1.5 rounded-lg flex items-center gap-1">
    🏡 الواجهة الرئيسية
</a>
<a href="{{ route('dashboard') }}" class="bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs px-3 py-1.5 rounded-lg flex items-center gap-1">
    📊 لوحة الإحصائيات
</a>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/20 text-xs font-bold px-3 py-1.5 rounded-lg transition flex items-center gap-1">
                        🚪 تسجيل الخروج
                    </button>
                </form>
            </div>

        </div>
    </header>

    <!-- محتوى الصفحات المتغير -->
    <main class="flex-1 p-6">
        @yield('content')
    </main>

    <!-- الفوتر -->
    <footer class="text-center text-xs text-slate-500 dark:text-slate-400 py-4 border-t border-slate-200 dark:border-slate-800">
        جميع الحقوق محفوظة &copy; {{ date('Y') }} - نظام إدارة الصيدلية والعيادة
    </footer>

    <!-- سكربت إدارة الثيم التفاعلي -->
    <script>
        function applyTheme(theme) {
            let isDark = false;

            if (theme === 'dark') {
                isDark = true;
            } else if (theme === 'light') {
                isDark = false;
            } else if (theme === 'auto') {
                isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            }

            if (isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }

            updateActiveButtons(theme);
        }

        function setTheme(theme) {
            localStorage.setItem('app_theme', theme);
            applyTheme(theme);
        }

        function updateActiveButtons(activeTheme) {
            ['dark', 'light', 'auto'].forEach(t => {
                const btn = document.getElementById(`theme-${t}-btn`);
                if (btn) {
                    if (t === activeTheme) {
                        btn.classList.add('theme-btn-active');
                    } else {
                        btn.classList.remove('theme-btn-active');
                    }
                }
            });
        }

        // تشغيل السكربت فور تحميل الصفحة وتحديد الزر النشط
        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('app_theme') || 'auto';
            applyTheme(savedTheme);
        });

        // الاستماع لتغييرات النظام في حال اختيار "تلقائي"
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            const currentTheme = localStorage.getItem('app_theme') || 'auto';
            if (currentTheme === 'auto') {
                applyTheme('auto');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
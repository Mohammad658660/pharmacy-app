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
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- سكريبت الفحص السريع لمنع وميض الشاشة عند التنقل -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('app_theme') || 'auto';
            let isDark = false;

            if (savedTheme === 'dark') {
                isDark = true;
            } else if (savedTheme === 'light') {
                isDark = false;
            } else if (savedTheme === 'auto') {
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
    <header class="bg-white border-b border-slate-200 dark:bg-slate-900 dark:border-slate-800 py-3 px-6 flex items-center justify-between">
        
        <!-- اسم النظام والعنوان -->
        <div class="flex items-center gap-2 text-xl font-bold text-slate-800 dark:text-white">
            <span>💊</span>
            <span>نظام إدارة الصيدلية والعيادة</span>
        </div>

        <!-- أزرار التحكم بالمظهر والأزرار العامة -->
        <div class="flex items-center gap-4">

            @if(request()->routeIs('home.menu'))
                {{-- أزرار تظهر حصراً وفقط في واجهة الكروت الرئيسية (home.menu) --}}

                <!-- أزرار تبديل الثيم -->
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

                <!-- زر الإحصائيات وتسجيل الخروج -->
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

            @else
                {{-- يظهر في لوحة الإحصائيات وفي كل الصفحات الفرعية --}}
                <div class="flex items-center gap-2">
                    <a href="{{ route('home.menu') }}" class="bg-emerald-600 hover:bg-emerald-500 text-white px-3 py-1.5 rounded-xl text-xs font-medium transition-colors flex items-center gap-1.5 shadow-sm">
                        🏠 الواجهة الرئيسية
                    </a>
                </div>
            @endif

        </div>

    </header>
    <!-- محتوى الصفحات المتغير -->
    <main class="flex-1 p-6">
        @yield('content')
    </main>

    <!-- الفوتر -->
    <footer class="text-center text-xs text-slate-500 dark:text-slate-400 py-4 border-t border-slate-200 dark:border-slate-800">
        نظام إدارة الصيدلية والعيادة - &copy; {{ date('Y') }} جميع الحقوق محفوظة
    </footer>

    <!-- سكريبت إدارة الثيم التفاعلي -->
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

        // تشغيل السكريبت فور تحميل الصفحة وتحديد الزر النشط
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
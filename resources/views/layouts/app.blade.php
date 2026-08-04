<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'نظام إدارة الصيدلية والعيادة') }}</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- سكريبت الفحص السريع لمنع وميض الشاشة عند التنقل بين الصفحات -->
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
    /* 1. خلفية الصفحة بالوضع الفاتح (رمادي متباين ليبرز اللون الأبيض) */
    body:not(.dark) {
        background-color: #e2e8f0 !important; 
        color: #0f172a !important;
    }

    /* 2. إجبار الحدود (Border) والظل (Shadow) على كل الكروت */
    body:not(.dark) div[class*="rounded-"] {
        background-color: #ffffff !important;
        border: 2px solid #cbd5e1 !important; /* ستروك واضح بحدود 2px */
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
    }

    /* 3. استثناء الأزرار والـ Badges الصغار من الستروك السميك */
    body:not(.dark) button,
    body:not(.dark) a,
    body:not(.dark) span[class*="rounded-"] {
        border: none !important;
    }

    /* 4. تعديل ألوان النصوص لتبدو حادة وواضحة */
    body:not(.dark) h1, body:not(.dark) h2, body:not(.dark) h3, body:not(.dark) h4,
    body:not(.dark) .text-slate-100 {
        color: #0f172a !important;
    }

    body:not(.dark) p, body:not(.dark) .text-slate-400, body:not(.dark) .text-slate-300 {
        color: #475569 !important;
    }
</style>
</head>

<body class="min-h-screen flex flex-col bg-slate-950 text-slate-100">

    <!-- الشريط العلوي الموحد (الهيدر) -->
    <header class="bg-slate-900 border-b border-slate-800 px-6 py-4 flex items-center justify-between">
        
        <!-- اسم النظام والعنوان يمين -->
        <div class="flex items-center gap-2 text-xl font-bold text-slate-100">
            <span>💊</span>
            <span>نظام إدارة الصيدلية والعيادة</span>
        </div>

        <!-- الأزرار يسار (الرئيسية + تسجيل الخروج) -->
        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1">
                🏡 الواجهة الرئيسية
            </a>

            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-rose-600/20 hover:bg-rose-600/30 text-rose-400 border border-rose-500/30 px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1">
                    🚪 تسجيل الخروج
                </button>
            </form>
        </div>

    </header>

    <!-- محتوى الصفحات المتغير -->
    <main class="flex-1 p-6">
        @yield('content')
    </main>

    <!-- الفوتر البسيط -->
    <footer class="text-center text-xs text-slate-600 py-4 border-t border-slate-900">
        جميع الحقوق محفوظة &copy; {{ date('Y') }} - نظام إدارة الصيدلية والعيادة
    </footer>

    <!-- سكريبت التحكم بالمظهر وقراءة الزر المحدد -->
    <script>
        function applyTheme(theme) {
            let isDark = true;

            if (theme === 'dark') {
                isDark = true;
            } else if (theme === 'light') {
                isDark = false;
            } else if (theme === 'auto') {
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
                        btn.classList.add('active-theme');
                    } else {
                        btn.classList.remove('active-theme');
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('app_theme') || 'dark';
            applyTheme(savedTheme);
        });
    </script>

    @stack('scripts')
</body>
</html>
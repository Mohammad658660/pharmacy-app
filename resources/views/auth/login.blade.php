<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - نظام الصيدلية</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-2xl p-8 shadow-2xl">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-600/10 text-indigo-400 border border-indigo-500/20 mb-4 text-2xl">
                💊
            </div>
            <h1 class="text-2xl font-bold text-slate-100">تسجيل الدخول</h1>
            <p class="text-sm text-slate-400 mt-1">أدخل بيانات الحساب للوصول إلى النظام</p>
        </div>

        @if ($errors->any())
            <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm p-3 rounded-lg mb-6">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-300 mb-2">اسم المستخدم</label>
                <input type="text" name="username" value="{{ old('username') }}" required autofocus
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-slate-100 text-sm focus:outline-none focus:border-indigo-500 transition-colors">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-300 mb-2">كلمة المرور</label>
                <input type="password" name="password" required
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-slate-100 text-sm focus:outline-none focus:border-indigo-500 transition-colors">
            </div>

            <!-- خيار تذكرني -->
            <div class="flex items-center justify-between text-xs text-slate-400">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="remember" class="rounded bg-slate-950 border-slate-800 text-indigo-600 focus:ring-0">
                    <span>تذكرني على هذا الجهاز</span>
                </label>
            </div>

            <button type="submit" 
                    class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-medium py-3 rounded-xl transition-colors text-sm shadow-lg shadow-indigo-600/20">
                دخول للنظام
            </button>
        </form>
    </div>

</body>
</html>
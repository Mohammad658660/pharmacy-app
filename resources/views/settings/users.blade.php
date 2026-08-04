@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">

    <!-- Header مع زر العودة للوحة الإعدادات -->
    <div class="flex justify-between items-center bg-slate-900 border border-slate-800 p-4 rounded-2xl">
        <h1 class="text-xl font-bold text-slate-100 flex items-center gap-2">
            👥 إدارة الحسابات والموظفين
        </h1>
        <a href="{{ route('settings.index') }}" class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-4 py-2 rounded-xl text-sm transition-colors">
            ← العودة للإعدادات
        </a>
    </div>

    @if (session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <!-- تغيير كلمة المرور -->
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl space-y-4">
            <h2 class="text-lg font-bold text-slate-100 border-b border-slate-800 pb-3">🔒 تغيير كلمة المرور الخاصة بك</h2>
            
            <form action="{{ route('settings.password.update') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">كلمة المرور الحالية</label>
                    <input type="password" name="current_password" required
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-slate-100 text-sm focus:outline-none focus:border-indigo-500">
                    @error('current_password') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">كلمة المرور الجديدة</label>
                    <input type="password" name="new_password" required
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-slate-100 text-sm focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">تأكيد كلمة المرور الجديدة</label>
                    <input type="password" name="new_password_confirmation" required
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-slate-100 text-sm focus:outline-none focus:border-indigo-500">
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white py-2.5 rounded-xl text-sm font-medium transition-colors">
                    تحديث كلمة المرور
                </button>
            </form>
        </div>

        <!-- إضافة موظف جديد -->
        @if(auth()->user()->role === 'admin')
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl space-y-4">
            <h2 class="text-lg font-bold text-slate-100 border-b border-slate-800 pb-3">👤 إضافة حساب موظف / كاشير جديد</h2>
            
        <form action="{{ route('settings.users.store') }}" method="POST">
    @csrf

    <!-- عرض رسائل الأخطاء إن وجدت -->
    @if ($errors->any())
        <div class="bg-rose-500/20 text-rose-300 p-3 rounded-lg mb-4 text-xs">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- الاسم الكامل -->
    <div class="mb-4">
        <label class="block text-slate-300 text-xs mb-1">الاسم الكامل</label>
        <input type="text" name="name" required placeholder="مثال: أحمد علي" class="w-full bg-slate-900 border border-slate-700 text-slate-100 rounded-lg p-2.5 text-sm focus:outline-none focus:border-emerald-500">
    </div>

    <!-- اسم المستخدم -->
    <div class="mb-4">
        <label class="block text-slate-300 text-xs mb-1">اسم المستخدم (Username)</label>
        <input type="text" name="username" required placeholder="مثال: ahmed123" class="w-full bg-slate-900 border border-slate-700 text-slate-100 rounded-lg p-2.5 text-sm focus:outline-none focus:border-emerald-500">
    </div>

    <!-- كلمة المرور -->
    <div class="mb-4">
        <label class="block text-slate-300 text-xs mb-1">كلمة المرور</label>
        <input type="password" name="password" required class="w-full bg-slate-900 border border-slate-700 text-slate-100 rounded-lg p-2.5 text-sm focus:outline-none focus:border-emerald-500">
    </div>

    <!-- الصلاحية -->
   <div>
    <label class="block text-sm font-medium text-gray-300 mb-1">الصلاحية</label>
    <select name="role" class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:outline-none focus:border-emerald-500 transition-colors">
        <option value="employee" class="bg-slate-900 text-white">موظف / كاشير (محدود الصلاحيات)</option>
        <option value="admin" class="bg-slate-900 text-white">مدير نظام</option>
    </select>
</div>

    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-medium p-2.5 rounded-lg text-sm transition-colors">
        إنشاء الحساب
    </button>
</form></form>
        </div>
        @endif

    </div>

    <!-- جدول المستخدمين -->
    @if(auth()->user()->role === 'admin')
    <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl space-y-4">
        <h2 class="text-lg font-bold text-slate-100 border-b border-slate-800 pb-3">👥 قائمة المستخدمين المسجلين</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm text-slate-300">
                <thead class="bg-slate-950 text-slate-400 text-xs">
                    <tr>
                        <th class="p-3 rounded-r-lg">الاسم</th>
                        <th class="p-3">اسم المستخدم</th>
                        <th class="p-3">الصلاحية</th>
                        <th class="p-3 rounded-l-lg">تاريخ الإنشاء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($users as $u)
                    <tr>
                        <td class="p-3 font-medium text-slate-100">{{ $u->name }}</td>
                        <td class="p-3 text-indigo-400">{{ $u->username }}</td>
                        <td class="p-3">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $u->role === 'admin' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-slate-800 text-slate-300' }}">
                                {{ $u->role === 'admin' ? 'مدير' : 'موظف' }}
                            </span>
                        </td>
                        <td class="p-3 text-xs text-slate-500">{{ $u->created_at->format('Y-m-d') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
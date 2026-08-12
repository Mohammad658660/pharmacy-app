<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>أرشيف الديون المسددة - نظام الصيدلية والعيادة</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- قارئ الثيم اللحظي (Light/Dark Mode) -->
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
        /* التنسيق الأساسي الداكن */
        body {
            background-color: #0b0f19;
            color: #f1f5f9;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* ☀️ Light Mode */
        html.light body {
            background-color: #f8fafc !important;
            color: #0f172a !important;
        }

        /* تحويل الخلفيات والكروت بالوضع الفاتح */
        html.light header,
        html.light .bg-slate-900,
        html.light .bg-slate-950,
        html.light .bg-slate-800 {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
            color: #0f172a !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important;
        }

        /* تحسين شكل الجدول بالوضع الفاتح */
        html.light table {
            background-color: #ffffff !important;
        }

        html.light thead tr,
        html.light th {
            background-color: #f1f5f9 !important;
            color: #334155 !important;
            border-color: #e2e8f0 !important;
        }

        html.light tbody tr {
            border-color: #e2e8f0 !important;
        }

        html.light tbody td {
            color: #0f172a !important;
        }

        html.light .text-slate-400 {
            color: #64748b !important;
        }

        html.light .text-slate-100 {
            color: #0f172a !important;
        }

        /* 🟢 تنسيق شارة (مسدد بالكامل) بالوضع الفاتح */
        html.light .status-paid {
            background-color: #d1fae5 !important; /* خلفية خضراء فاتحة */
            color: #047857 !important;            /* نص أخضر داكن وواضح */
            border-color: #a7f3d0 !important;
        }

        /* 🔴 الحفاظ على نص أبيض واضح داخل زر الحذف والأزرار الملونة */
        html.light .btn-delete,
        html.light .btn-delete * {
            color: #ffffff !important;
        }

        html.light h1, html.light h2, html.light h3, html.light h4 {
            color: #0f172a !important;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-slate-950 text-slate-100 p-6">

    <div class="max-w-7xl mx-auto w-full space-y-6">

        <!-- الشريط العلوي -->
        <header class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-xl font-bold text-slate-100">
                    <span>💊</span>
                    <span>نظام إدارة الصيدلية والعيادة</span>
                </div>
                <div>
                    <a href="{{ route('home.menu') }}" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-xl text-sm font-bold transition flex items-center gap-2 shadow-lg">
                        <span>🏠</span> الواجهة الرئيسية
                    </a>
                </div>
            </div>
        </header>

        <!-- العنوان وأزرار التحكم -->
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-100 flex items-center gap-2">
                    <span>📦</span> سجل الديون المسددة بالكامل
                </h2>
                <p class="text-slate-400 text-xs mt-1">عرض السجلات المكتملة للتسديد والتواريخ</p>
            </div>
            <div>
                <a href="{{ route('debts.index') }}" class="bg-purple-600 hover:bg-purple-500 text-white px-4 py-2 rounded-xl text-sm font-bold transition flex items-center gap-2 shadow-lg">
                    <span>⬅️</span> العودة للديون النشطة
                </a>
            </div>
        </div>

        <!-- جدول الأرشيف -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right text-slate-300">
                    <thead class="text-xs uppercase bg-slate-950 text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-4 py-3">اسم العميل / العيادة</th>
                            <th class="px-4 py-3">رقم الهاتف</th>
                            <th class="px-4 py-3">المبلغ المسدد</th>
                            <th class="px-4 py-3">الملاحظات</th>
                            <th class="px-4 py-3">تاريخ التسديد النهائي</th>
                            <th class="px-4 py-3 text-center">الحالة</th>
                            <th class="px-4 py-3 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse(($paidDebts ?? $debts ?? []) as $debt)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="px-4 py-3 font-medium text-slate-100">{{ $debt->customer_name }}</td>
                            <td class="px-4 py-3 text-slate-400" dir="ltr">{{ $debt->phone ?? '-' }}</td>
                            <td class="px-4 py-3 text-emerald-500 font-bold">{{ number_format($debt->total_amount ?? $debt->paid_amount) }} د.ع</td>
                            <td class="px-4 py-3 text-slate-400 text-xs">{{ $debt->notes ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-400 text-xs" dir="ltr">{{ $debt->updated_at ? $debt->updated_at->format('Y-m-d H:i:s') : '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="status-paid bg-emerald-950/80 text-emerald-400 border border-emerald-800 px-3 py-1 rounded-full text-xs font-bold inline-block">
                                    مسدد بالكامل
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <form action="{{ route('debts.destroy', $debt->id) }}" method="POST" onsubmit="return confirm('هل أنت تأكد من حذف هذا السجل نهائياً؟');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete bg-rose-600 hover:bg-rose-500 text-white px-3 py-1 rounded-lg text-xs font-bold transition flex items-center gap-1 mx-auto shadow">
                                        <span>🗑️</span>
                                        <span>حذف</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-20 text-slate-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <span class="text-3xl">📬</span>
                                    <p class="text-sm">لا توجد ديون مسددة في الأرشيف.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>
</html>
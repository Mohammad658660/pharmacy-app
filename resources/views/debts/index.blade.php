<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سجل الديون النشطة - نظام الصيدلية والعيادة</title>
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
        background-color: #f1f5f9 !important; /* خلفية رمادية فاتحة لبروز الكروت البيضاء */
        color: #0f172a !important;
    }

    /* 📦 إجبار إظهار الستروك (Border) والظل (Shadow) على جميع الكروت والحاويات */
    html.light header,
    html.light .bg-slate-900,
    html.light .bg-slate-950,
    html.light .bg-slate-800 {
        background-color: #ffffff !important;
        border: 2px solid #cbd5e1 !important; /* ستروك واضح وبارز */
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.1), 0 8px 10px -6px rgba(15, 23, 42, 0.08) !important; /* ظل أسفل الكارت */
        color: #0f172a !important;
    }

    /* تحسين شكل الجدول بالوضع الفاتح */
    html.light table {
        background-color: #ffffff !important;
    }

    html.light thead tr,
    html.light th {
        background-color: #f1f5f9 !important;
        color: #334155 !important;
        border-bottom: 2px solid #cbd5e1 !important;
    }

    html.light tbody tr {
        border-bottom: 1px solid #e2e8f0 !important;
    }

    html.light tbody td {
        color: #0f172a !important;
    }

    /* نصوص وأسماء العناوين */
    html.light .text-slate-100,
    html.light h1, html.light h2, html.light h3, html.light h4 {
        color: #0f172a !important;
    }

    html.light .text-slate-300,
    html.light .text-slate-400 {
        color: #475569 !important;
    }

    /* 📝 تحسين حقول الإدخال والـ Modals بالوضع الفاتح */
    html.light input,
    html.light textarea {
        background-color: #f8fafc !important;
        color: #0f172a !important;
        border: 1.5px solid #cbd5e1 !important;
    }

    html.light input:focus,
    html.light textarea:focus {
        background-color: #ffffff !important;
        border-color: #3b82f6 !important;
        outline: none !important;
    }

    html.light label {
        color: #334155 !important;
    }

    /* 🔘 الحفاظ على نصوص الأزرار الملونة بالوضع الفاتح */
    html.light .btn-action,
    html.light .btn-action * {
        color: #ffffff !important;
        border: none !important; /* عدم تطبيق الستروك على أزرار الإجراءات */
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
                    <a href="{{ route('home') }}" class="btn-action bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-xl text-sm font-bold transition flex items-center gap-2 shadow-lg">
                        <span>🏠</span> الواجهة الرئيسية
                    </a>
                </div>
            </div>
        </header>

        <!-- العنوان وأزرار التحكم -->
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-100 flex items-center gap-2">
                    <span>📑</span> سجل الديون النشطة
                </h2>
                <p class="text-slate-400 text-xs mt-1">متابعة المبالغ المتبقية على الزبائن والعيادات وتسديدها</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="openAddDebtModal()" class="btn-action bg-purple-600 hover:bg-purple-500 text-white px-4 py-2 rounded-xl text-sm font-bold transition flex items-center gap-2 shadow-lg">
                    <span>➕</span> إضافة دين جديد
                </button>
                <a href="{{ route('debts.archive') }}" class="btn-action bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-xl text-sm font-bold transition flex items-center gap-2 shadow-lg">
                    <span>📦</span> عرض أرشيف الديون المسددة
                </a>
            </div>
        </div>

        <!-- جدول الديون النشطة -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right text-slate-300">
                    <thead class="text-xs uppercase bg-slate-950 text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-4 py-3">اسم العميل / العيادة</th>
                            <th class="px-4 py-3">رقم الهاتف</th>
                            <th class="px-4 py-3">إجمالي الدين</th>
                            <th class="px-4 py-3">المبلغ المتبقي</th>
                            <th class="px-4 py-3">التاريخ</th>
                            <th class="px-4 py-3">الملاحظات</th>
                            <th class="px-4 py-3 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($debts ?? [] as $debt)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="px-4 py-3 font-medium text-slate-100">{{ $debt->customer_name }}</td>
                            <td class="px-4 py-3 text-slate-400" dir="ltr">{{ $debt->phone ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-200 font-semibold">{{ number_format($debt->total_amount) }} د.ع</td>
                            <td class="px-4 py-3 text-emerald-500 font-bold">{{ number_format($debt->remaining_amount) }} د.ع</td>
                            <td class="px-4 py-3 text-slate-400 text-xs" dir="ltr">{{ $debt->created_at ? $debt->created_at->format('Y-m-d') : '-' }}</td>
                            <td class="px-4 py-3 text-slate-400 text-xs">{{ $debt->notes ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="openEditModal({{ $debt->id }}, '{{ addslashes($debt->customer_name) }}', '{{ $debt->phone }}', {{ $debt->total_amount }}, '{{ addslashes($debt->notes ?? '') }}')" class="btn-action bg-amber-600 hover:bg-amber-500 text-white px-3 py-1 rounded-lg text-xs font-bold transition flex items-center gap-1 shadow">
                                        <span>✏️</span> تعديل
                                    </button>
                                    <button onclick="openPayModal({{ $debt->id }}, '{{ addslashes($debt->customer_name) }}', {{ $debt->remaining_amount }})" class="btn-action bg-emerald-600 hover:bg-emerald-500 text-white px-3 py-1 rounded-lg text-xs font-bold transition flex items-center gap-1 shadow">
                                        <span>💵</span> تسديد دفعة
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-20 text-slate-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <span class="text-3xl">📬</span>
                                    <p class="text-sm">لا توجد ديون نشطة حالياً.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- 1️⃣ نافذة إضافة دين جديد -->
    <div id="addDebtModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center p-4 z-50">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-lg p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-lg font-bold text-slate-100 flex items-center gap-2">
                    <span>➕</span> تسجيل دين جديد
                </h3>
                <button onclick="closeAddDebtModal()" class="text-slate-400 hover:text-white text-xl font-bold">✕</button>
            </div>

            <form action="{{ route('debts.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-400 font-bold mb-1">اسم العميل / العيادة *</label>
                    <input type="text" name="customer_name" required placeholder="أدخل اسم العميل" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-slate-100 text-sm focus:border-purple-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-slate-400 font-bold mb-1">رقم الهاتف</label>
                    <input type="text" name="phone" placeholder="07700000000" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-slate-100 text-sm focus:border-purple-500 focus:outline-none" dir="ltr">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-400 font-bold mb-1">إجمالي الدين (د.ع) *</label>
                        <input type="text" id="total_amount_input" required placeholder="0" oninput="formatNumberInput(this, 'total_amount')" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-slate-100 text-sm font-bold text-emerald-400 focus:border-purple-500 focus:outline-none">
                        <input type="hidden" name="total_amount" id="total_amount" value="0">
                    </div>
                    <div>
                        <label class="block text-slate-400 font-bold mb-1">الدفعة الأولى (د.ع)</label>
                        <input type="text" id="paid_amount_input" placeholder="0" oninput="formatNumberInput(this, 'paid_amount')" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-slate-100 text-sm font-bold text-blue-400 focus:border-purple-500 focus:outline-none">
                        <input type="hidden" name="paid_amount" id="paid_amount" value="0">
                    </div>
                </div>

                <div>
                    <label class="block text-slate-400 font-bold mb-1">ملاحظات</label>
                    <textarea name="notes" placeholder="ملاحظات إضافية حول الدين..." rows="2" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-slate-100 text-sm focus:border-purple-500 focus:outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" onclick="closeAddDebtModal()" class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-4 py-2 rounded-xl font-bold transition">إلغاء</button>
                    <button type="submit" class="btn-action bg-emerald-600 hover:bg-emerald-500 text-white px-5 py-2 rounded-xl font-bold transition shadow-lg">حفظ الدين</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 2️⃣ نافذة تعديل بيانات الدين -->
    <div id="editDebtModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center p-4 z-50">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-lg p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-lg font-bold text-slate-100 flex items-center gap-2">
                    <span>✏️</span> تعديل بيانات الدين
                </h3>
                <button onclick="closeEditModal()" class="text-slate-400 hover:text-white text-xl font-bold">✕</button>
            </div>

            <form id="editForm" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-slate-400 font-bold mb-1">اسم العميل / العيادة *</label>
                    <input type="text" id="edit_customer_name" name="customer_name" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-slate-100 text-sm focus:border-amber-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-slate-400 font-bold mb-1">رقم الهاتف</label>
                    <input type="text" id="edit_phone" name="phone" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-slate-100 text-sm focus:border-amber-500 focus:outline-none" dir="ltr">
                </div>

                <div>
                    <label class="block text-slate-400 font-bold mb-1">إجمالي الدين (د.ع) *</label>
                    <input type="text" id="edit_total_amount_input" required oninput="formatNumberInput(this, 'edit_total_amount_hidden')" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-slate-100 text-sm font-bold text-emerald-400 focus:border-amber-500 focus:outline-none">
                    <input type="hidden" name="total_amount" id="edit_total_amount_hidden">
                </div>

                <div>
                    <label class="block text-slate-400 font-bold mb-1">ملاحظات</label>
                    <textarea id="edit_notes" name="notes" rows="2" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-slate-100 text-sm focus:border-amber-500 focus:outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" onclick="closeEditModal()" class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-4 py-2 rounded-xl font-bold transition">إلغاء</button>
                    <button type="submit" class="btn-action bg-amber-600 hover:bg-amber-500 text-white px-5 py-2 rounded-xl font-bold transition shadow-lg">تحديث البيانات</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3️⃣ نافذة تسديد دفعة -->
    <div id="payModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center p-4 z-50">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-lg p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-lg font-bold text-slate-100 flex items-center gap-2">
                    <span>💵</span> تسديد دفعة للعميل: <span id="pay_customer_name" class="text-emerald-400"></span>
                </h3>
                <button onclick="closePayModal()" class="text-slate-400 hover:text-white text-xl font-bold">✕</button>
            </div>

            <form id="payForm" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-400 font-bold mb-1">المبلغ المتبقي الحالي</label>
                    <input type="text" id="current_remaining" disabled class="w-full bg-slate-950/60 border border-slate-800 rounded-xl px-3 py-2 text-slate-400 text-sm font-bold">
                </div>

                <div>
                    <label class="block text-slate-400 font-bold mb-1">المبلغ المراد تسديده (د.ع) *</label>
                    <input type="text" id="pay_amount_input" required placeholder="0" oninput="formatNumberInput(this, 'pay_amount_hidden')" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-slate-100 text-sm font-bold text-emerald-400 focus:border-emerald-500 focus:outline-none">
                    <input type="hidden" name="amount" id="pay_amount_hidden" value="0">
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" onclick="closePayModal()" class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-4 py-2 rounded-xl font-bold transition">إلغاء</button>
                    <button type="submit" class="btn-action bg-emerald-600 hover:bg-emerald-500 text-white px-5 py-2 rounded-xl font-bold transition shadow-lg">تأكيد التسديد</button>
                </div>
            </form>
        </div>
    </div>

    <!-- السكريبتات والتحكم بالمودال والتنسيق -->
    <script>
        // التحكم بنوافذ إضافة دين
        function openAddDebtModal() {
            document.getElementById('addDebtModal').classList.remove('hidden');
            document.getElementById('addDebtModal').classList.add('flex');
        }
        function closeAddDebtModal() {
            document.getElementById('addDebtModal').classList.remove('flex');
            document.getElementById('addDebtModal').classList.add('hidden');
        }

        // التحكم بنوافذ تعديل الدين
        function openEditModal(id, name, phone, total, notes) {
            document.getElementById('edit_customer_name').value = name;
            document.getElementById('edit_phone').value = phone || '';
            document.getElementById('edit_total_amount_input').value = Number(total).toLocaleString('en-US');
            document.getElementById('edit_total_amount_hidden').value = total;
            document.getElementById('edit_notes').value = notes || '';
            
            document.getElementById('editForm').action = "/debts/" + id;
            
            document.getElementById('editDebtModal').classList.remove('hidden');
            document.getElementById('editDebtModal').classList.add('flex');
        }
        function closeEditModal() {
            document.getElementById('editDebtModal').classList.remove('flex');
            document.getElementById('editDebtModal').classList.add('hidden');
        }

        // التحكم بنوافذ تسديد دفعة
        function openPayModal(id, name, remaining) {
            document.getElementById('pay_customer_name').innerText = name;
            document.getElementById('current_remaining').value = Number(remaining).toLocaleString('en-US') + ' د.ع';
            document.getElementById('pay_amount_input').value = '';
            document.getElementById('pay_amount_hidden').value = 0;
            
            document.getElementById('payForm').action = "/debts/" + id + "/pay";
            
            document.getElementById('payModal').classList.remove('hidden');
            document.getElementById('payModal').classList.add('flex');
        }
        function closePayModal() {
            document.getElementById('payModal').classList.remove('flex');
            document.getElementById('payModal').classList.add('hidden');
        }

        // تنسيق الأرقام بفاصلة الألوف تلقائياً للحقول
        function formatNumberInput(input, hiddenId) {
            let value = input.value.replace(/\D/g, '');
            if (value === '' || isNaN(value)) {
                input.value = '';
                if (hiddenId && document.getElementById(hiddenId)) {
                    document.getElementById(hiddenId).value = 0;
                }
                return;
            }
            let number = parseInt(value, 10);
            input.value = number.toLocaleString('en-US');
            if (hiddenId && document.getElementById(hiddenId)) {
                document.getElementById(hiddenId).value = number;
            }
        }
    </script>

</body>
</html>
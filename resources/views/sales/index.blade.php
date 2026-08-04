@extends('layouts.app') {{-- تعديل اسم الـ layout إن كان مختلفاً عندك --}}

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center bg-slate-900 border border-slate-800 p-4 rounded-2xl shadow-xl">
        <h1 class="text-xl font-bold text-slate-100 flex items-center gap-2">
            🛒 شاشة البيع / الكاشير
        </h1>
       
    </div>

    <!-- Main Layout -->
    <form action="{{ route('invoices.store') }}" method="POST" id="invoiceForm">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- اليسار: تفاصيل الحساب والدفع -->
            <div class="lg:col-span-1 bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl space-y-4 h-fit">
                <h2 class="text-sm font-bold text-slate-200 border-b border-slate-800 pb-3 flex items-center gap-2">
                    💳 تفاصيل الحساب والدفع
                </h2>

                <div>
                    <label class="block text-xs text-slate-400 mb-1">اسم الزبون / العيادة</label>
                    <input type="text" name="customer_name" value="زبون نقدي" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-purple-500">
                </div>

                <div>
                    <label class="block text-xs text-slate-400 mb-1">رقم الهاتف</label>
                    <input type="text" name="customer_phone" placeholder="07xxxxxxxxx" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-purple-500">
                </div>

                <div>
                    <label class="block text-xs text-slate-400 mb-1">طريقة الدفع</label>
                    <select name="payment_method" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-purple-500">
                        <option value="cash">نقدي (كاش)</option>
                        <option value="credit">آجل (دين)</option>
                    </select>
                </div>

                <div class="space-y-3 pt-3 border-t border-slate-800">
                    <div class="flex justify-between items-center text-xs text-slate-400">
                        <span>المجموع الكلي:</span>
                        <span id="total_amount_display" class="font-bold text-slate-100 text-sm">0 د.ع</span>
                    </div>

                    <div class="flex justify-between items-center text-xs text-slate-400 gap-2">
                        <span>الخصم:</span>
                        <div class="flex items-center gap-1 w-36">
                            <select id="discount_type" onchange="calculateTotalFromCart()" class="bg-slate-950 border border-slate-800 rounded-xl px-2 py-1 text-xs text-slate-200 focus:outline-none">
                                <option value="fixed">د.ع</option>
                                <option value="percent">%</option>
                            </select>
                            <input type="text" id="discount_input" value="0" oninput="calculateTotalFromCart()" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-2 py-1 text-xs text-slate-200 text-center font-bold focus:outline-none">
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-2 border-t border-slate-800">
                        <span class="text-slate-200 font-bold text-xs">الصافي المطلوب:</span>
                        <span id="final_amount_display" class="font-bold text-base text-purple-400">0 د.ع</span>
                    </div>
                </div>

                <!-- Hidden inputs for Laravel -->
                <input type="hidden" name="total_amount" id="hidden_total_amount" value="0">
                <input type="hidden" name="discount" id="hidden_discount" value="0">
                <input type="hidden" name="final_amount" id="hidden_final_amount" value="0">

                <div>
                    <label class="block text-xs text-slate-400 mb-1">ملاحظات</label>
                    <textarea name="notes" placeholder="ملاحظات حول الطلب..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 h-20 focus:outline-none focus:border-purple-500"></textarea>
                </div>

                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 rounded-xl shadow-lg transition text-xs">
                    💾 حفظ الفاتورة
                </button>
            </div>

            <!-- اليمين: حقول الإدخال والجدول -->
            <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl space-y-4">
                
                <!-- Inputs Section -->
                <div class="space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">🔍 باركود الدواء</label>
                            <input type="text" id="product_barcode" placeholder="امسح الباركود..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 text-center focus:outline-none focus:border-purple-500">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">🏷️ الاسم التجاري</label>
                            <input type="text" id="trade_name" placeholder="الاسم التجاري..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-purple-500">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">🧪 الاسم العلمي</label>
                            <input type="text" id="scientific_name" placeholder="الاسم العلمي..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-purple-500">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">💰 السعر (د.ع)</label>
                            <input type="text" id="product_price" value="0" oninput="formatPriceInput(this)" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 text-center font-bold focus:outline-none focus:border-purple-500">
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-2">
                        <div class="w-32">
                            <label class="block text-xs text-slate-400 mb-1">📦 الكمية</label>
                            <input type="number" id="product_qty" value="1" min="1" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 text-center font-bold focus:outline-none focus:border-purple-500">
                        </div>
                        
                        <button type="button" onclick="addItemToCart()" class="bg-purple-600 hover:bg-purple-500 text-white text-xs px-6 py-2.5 rounded-xl font-bold transition shadow-lg shadow-purple-950/50 mt-5">
                            + إضافة
                        </button>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="overflow-x-auto border border-slate-800 rounded-xl">
                    <table class="w-full text-right text-xs">
                        <thead class="bg-slate-950 text-slate-400 border-b border-slate-800">
                            <tr>
                                <th class="px-3 py-3">الاسم التجاري</th>
                                <th class="px-3 py-3">الاسم العلمي</th>
                                <th class="px-3 py-3">السعر المفرد</th>
                                <th class="px-3 py-3">الكمية</th>
                                <th class="px-3 py-3">المجموع</th>
                                <th class="px-3 py-3 text-center">حذف</th>
                            </tr>
                        </thead>
                        <tbody id="cart_items">
                            <tr>
                                <td colspan="6" class="text-center py-16 text-slate-500">
                                    لم يتم إضافة أي مواد للفاتورة بعد.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </form>
</div>

<script>
let cart = [];

function formatPriceInput(input) {
    let value = input.value.replace(/,/g, '').replace(/\D/g, '');
    input.value = value ? Number(value).toLocaleString('en-US') : '';
}

function addItemToCart() {
    let tradeNameInput = document.getElementById('trade_name');
    let scientificNameInput = document.getElementById('scientific_name');
    let priceInput = document.getElementById('product_price');
    let qtyInput = document.getElementById('product_qty');

    let tradeName = tradeNameInput ? tradeNameInput.value.trim() : '';
    let scientificName = scientificNameInput ? scientificNameInput.value.trim() : '';
    let priceRaw = priceInput ? priceInput.value.replace(/,/g, '') : '0';
    let price = parseFloat(priceRaw) || 0;
    let qty = qtyInput ? (parseInt(qtyInput.value) || 1) : 1;

    if (!tradeName) {
        alert("يرجى إدخال الاسم التجاري للدواء!");
        if(tradeNameInput) tradeNameInput.focus();
        return;
    }

    if (price <= 0) {
        alert("يرجى إدخال السعر بشكل صحيح!");
        if(priceInput) priceInput.focus();
        return;
    }

    let subtotal = price * qty;

    cart.push({
        trade_name: tradeName,
        scientific_name: scientificName,
        price: price,
        qty: qty,
        subtotal: subtotal
    });

    updateCartTable();

    if(tradeNameInput) tradeNameInput.value = '';
    if(scientificNameInput) scientificNameInput.value = '';
    let barcodeInput = document.getElementById('product_barcode');
    if(barcodeInput) barcodeInput.value = '';
    if(priceInput) priceInput.value = '0';
    if(qtyInput) qtyInput.value = '1';

    if(tradeNameInput) tradeNameInput.focus();
}

function updateCartTable() {
    let tbody = document.getElementById('cart_items');
    if (!tbody) return;

    tbody.innerHTML = '';

    if (cart.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-16 text-slate-500">
                    لم يتم إضافة أي مواد للفاتورة بعد.
                </td>
            </tr>
        `;
        calculateTotalFromCart();
        return;
    }

    cart.forEach((item, index) => {
        let row = `
            <tr class="border-b border-slate-800/50 text-slate-200 hover:bg-slate-800/30">
                <td class="px-3 py-3 font-medium text-purple-300">
                    ${item.trade_name}
                    <input type="hidden" name="items[${index}][trade_name]" value="${item.trade_name}">
                    <input type="hidden" name="items[${index}][scientific_name]" value="${item.scientific_name}">
                </td>
                <td class="px-3 py-3 text-slate-400">${item.scientific_name || '-'}</td>
                <td class="px-3 py-3">${item.price.toLocaleString('en-US')} د.ع
                    <input type="hidden" name="items[${index}][price]" value="${item.price}">
                </td>
                <td class="px-3 py-3 font-bold text-amber-400">
                    ${item.qty}
                    <input type="hidden" name="items[${index}][qty]" value="${item.qty}">
                </td>
                <td class="px-3 py-3 font-bold text-emerald-400">${item.subtotal.toLocaleString('en-US')} د.ع</td>
                <td class="px-3 py-3 text-center">
                    <button type="button" onclick="removeItem(${index})" class="text-rose-500 hover:text-rose-400 font-bold px-2">
                        ✕
                    </button>
                </td>
            </tr>
        `;
        tbody.innerHTML += row;
    });

    calculateTotalFromCart();
}

function removeItem(index) {
    cart.splice(index, 1);
    updateCartTable();
}

function calculateTotalFromCart() {
    let grandTotal = 0;
    cart.forEach(item => {
        grandTotal += item.subtotal;
    });

    let discountInput = document.getElementById('discount_input');
    let discountType = document.getElementById('discount_type') ? document.getElementById('discount_type').value : 'fixed';
    
    let rawDiscountValue = discountInput ? (parseFloat(discountInput.value.replace(/,/g, '')) || 0) : 0;
    let actualDiscountAmount = 0;

    if (discountType === 'percent') {
        actualDiscountAmount = (grandTotal * rawDiscountValue) / 100;
    } else {
        actualDiscountAmount = rawDiscountValue;
    }

    if (actualDiscountAmount > grandTotal) {
        actualDiscountAmount = grandTotal;
    }

    let finalAmount = grandTotal - actualDiscountAmount;

    let totalDisplay = document.getElementById('total_amount_display');
    let finalDisplay = document.getElementById('final_amount_display');

    if (totalDisplay) totalDisplay.innerText = grandTotal.toLocaleString('en-US') + ' د.ع';
    if (finalDisplay) finalDisplay.innerText = (finalAmount > 0 ? finalAmount : 0).toLocaleString('en-US') + ' د.ع';

    let hiddenTotal = document.getElementById('hidden_total_amount');
    let hiddenDiscount = document.getElementById('hidden_discount');
    let hiddenFinal = document.getElementById('hidden_final_amount');

    if (hiddenTotal) hiddenTotal.value = grandTotal;
    if (hiddenDiscount) hiddenDiscount.value = actualDiscountAmount;
    if (hiddenFinal) hiddenFinal.value = finalAmount;
}
</script>
@endsection
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800">
        <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
            🛒 شاشة البيع / الكاشير
        </h1>
    </div>

    <!-- Main Layout -->
    <form action="{{ route('invoices.store') }}" method="POST" id="invoiceForm">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- اليسار: تفاصيل الحساب والدفع -->
            <div class="lg:col-span-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 space-y-4 h-fit">
                <h2 class="text-sm font-bold text-slate-700 dark:text-slate-200 flex items-center gap-2 pb-2 border-b border-slate-200 dark:border-slate-800">
                    💳 تفاصيل الحساب والدفع
                </h2>

                <div>
                    <label class="block text-xs text-slate-600 dark:text-slate-400 mb-1">اسم الزبون</label>
                    <input type="text" name="customer_name" value="زبون نقدي" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-white focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs text-slate-600 dark:text-slate-400 mb-1">رقم الهاتف</label>
                    <input type="text" name="customer_phone" placeholder="07xx xxx xxxx" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-white focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs text-slate-600 dark:text-slate-400 mb-1">طريقة الدفع</label>
                    <select name="payment_method" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white text-sm rounded-xl px-3 py-2 focus:outline-none focus:border-indigo-500">
                        <option value="cash">نقدي (كاش)</option>
                        <option value="credit">آجل (دين)</option>
                    </select>
                </div>

                <div class="space-y-3 pt-3 border-t border-slate-200 dark:border-slate-800">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-500 dark:text-slate-400">المجموع الكلي:</span>
                        <span id="total_amount_display" class="font-bold text-slate-700 dark:text-slate-200 text-sm">0</span>
                    </div>

                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-500 dark:text-slate-400">الخصم:</span>
                        <div class="flex items-center gap-1 w-36">
                            <select id="discount_type" onchange="calculateTotalFromCart()" class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white text-xs rounded-lg px-2 py-1 focus:outline-none">
                                <option value="fixed">د.ع</option>
                                <option value="percent">%</option>
                            </select>
                            <input type="text" id="discount_input" value="0" oninput="formatPriceInput(this); calculateTotalFromCart();" class="w-full px-2 py-1 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs text-center dark:text-white focus:outline-none">
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-2 border-t border-slate-200 dark:border-slate-800">
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200">الصافي النهائي:</span>
                        <span id="final_amount_display" class="text-lg font-black text-emerald-600 dark:text-emerald-400">0</span>
                    </div>
                </div>

                <!-- Hidden inputs for Laravel -->
                <input type="hidden" name="total_amount" id="hidden_total_amount" value="0">
                <input type="hidden" name="discount" id="hidden_discount" value="0">
                <input type="hidden" name="final_amount" id="hidden_final_amount" value="0">

                <div>
                    <label class="block text-xs text-slate-600 dark:text-slate-400 mb-1">ملاحظات حول الطلب</label>
                    <textarea name="notes" placeholder="ملاحظات..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs dark:text-white focus:outline-none focus:border-indigo-500" rows="2"></textarea>
                </div>

                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg transition-all text-sm flex items-center justify-center gap-2">
                    💾 حفظ الفاتورة
                </button>
            </div>

            <!-- اليمين: حقول الإدخال والجدول -->
            <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 space-y-4">

                <!-- Inputs Section -->
                <div class="space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <!-- الباركود -->
                        <div>
                            <label class="block text-xs text-slate-600 dark:text-slate-400 mb-1">الباركود</label>
                            <input type="text" id="product_barcode" autofocus placeholder="امسح الباركود..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-white focus:outline-none focus:border-indigo-500">
                        </div>

                        <!-- اسم الدواء التجاري + قائمة البحث الفوري -->
                        <div class="relative">
                            <label class="block text-xs text-slate-600 dark:text-slate-400 mb-1">اسم الدواء التجاري</label>
                            <input type="text" id="trade_name" autocomplete="off" placeholder="ابحث باسم الدواء..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-white focus:outline-none focus:border-indigo-500">
                            
                            <!-- قائمة نتائج البحث -->
                            <div id="pos-search-results" class="absolute left-0 right-0 mt-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-2xl hidden z-50 max-h-56 overflow-y-auto"></div>
                        </div>

                        <!-- الاسم العلمي -->
                        <div>
                            <label class="block text-xs text-slate-600 dark:text-slate-400 mb-1">الاسم العلمي</label>
                            <input type="text" id="scientific_name" placeholder="الاسم العلمي" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-white focus:outline-none focus:border-indigo-500">
                        </div>

                        <!-- السعر -->
                        <div>
                            <label class="block text-xs text-slate-600 dark:text-slate-400 mb-1">سعر المفرد</label>
                            <input type="text" id="product_price" value="0" oninput="formatPriceInput(this)" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-white focus:outline-none focus:border-indigo-500">
                        </div>
                    </div>
         <!-- نوع البيع -->
<div class="mb-3">
    <label class="block text-xs text-slate-600 dark:text-slate-400 mb-1">نوع البيع</label>
    <select id="sale_unit" onchange="changeSaleUnit()" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
    <option value="strip" selected style="background: #1e293b; color: #fff;">شريط (سعر مقسم)</option>
    <option value="packet" style="background: #1e293b; color: #fff;">باكيت (سعر كامل)</option>
</select>
</div>

<div class="flex justify-between items-center pt-2">
    <div class="w-32">
        <label class="block text-xs text-slate-600 dark:text-slate-400 mb-1">الكمية</label>
        <input type="number" id="product_qty" value="1" min="1" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500">
    </div>

    <button type="button" onclick="addItemToCart()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-md transition-all">
        + إضافة
    </button>
</div>

                <!-- Table Section -->
                <div class="overflow-x-auto border border-slate-200 dark:border-slate-800 rounded-xl mt-4">
                    <table class="w-full text-right text-xs">
                        <thead class="bg-slate-100 dark:bg-slate-950 text-slate-600 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="px-3 py-3">الاسم التجاري</th>
                                <th class="px-3 py-3">الاسم العلمي</th>
                                <th class="px-3 py-3">السعر المفرد</th>
                                <th class="px-3 py-3">الكمية</th>
                                <th class="px-3 py-3">المجموع</th>
                                <th class="px-3 py-3 text-center">حذف</th>
                            </tr>
                        </thead>
                        <tbody id="cart_items" class="divide-y divide-slate-200 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                            <tr>
                                <td colspan="6" class="text-center py-16 text-slate-400 dark:text-slate-500">
                                    لم يتم إضافة أي مواد للفاتورة بعد
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
let currentProductId = null; // متغير لتخزين ID المنتج المختار حالياً

// تنسيق إدخال الأسعار
function formatPriceInput(input) {
    let value = input.value.replace(/,/g, '').replace(/\D/g, '');
    input.value = value ? Number(value).toLocaleString('en-US') : '';
}

// إضافة مادة للسلة
function addItemToCart() {
    const tradeNameInput = document.getElementById('trade_name');
    const scientificNameInput = document.getElementById('scientific_name');
    const priceInput = document.getElementById('product_price');
    const qtyInput = document.getElementById('product_qty');
    const barcodeInput = document.getElementById('product_barcode');
    const unitSelect = document.getElementById('sale_unit');

    const tradeName = tradeNameInput ? tradeNameInput.value.trim() : '';
    const scientificName = scientificNameInput ? scientificNameInput.value.trim() : '';

    const priceRaw = priceInput ? priceInput.value.replace(/,/g, '') : '0';
    const price = parseFloat(priceRaw) || 0;

    const qty = qtyInput ? (parseInt(qtyInput.value) || 1) : 1;
    const saleUnit = unitSelect ? unitSelect.value : 'packet';

    if (!tradeName) {
        alert("يرجى إدخال الاسم التجاري للدواء");
        if (tradeNameInput) tradeNameInput.focus();
        return;
    }

    if (price <= 0) {
        alert("يرجى إدخال السعر بشكل صحيح");
        if (priceInput) priceInput.focus();
        return;
    }

    const subtotal = price * qty;

    // إضافة المنتج للسلة مع إرفاق product_id
    cart.push({
        product_id: currentProductId, // ربط ID المنتج
        trade_name: tradeName,
        scientific_name: scientificName,
        price: price,
        qty: qty,
        subtotal: subtotal,
        unit_type: saleUnit
    });

    updateCartTable();

    // تفريغ الحقول وإعادة التركيز
    if (tradeNameInput) tradeNameInput.value = '';
    if (scientificNameInput) scientificNameInput.value = '';
    if (barcodeInput) barcodeInput.value = '';
    if (priceInput) priceInput.value = '0';
    if (qtyInput) qtyInput.value = '1';
    
    // إعادة تعيين ID المنتج المؤقت
    currentProductId = null;

    if (barcodeInput) barcodeInput.focus();
}

// تحديث جدول السلة
function updateCartTable() {
    const tbody = document.getElementById('cart_items');
    if (!tbody) return;

    tbody.innerHTML = '';

    if (cart.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-16 text-slate-400 dark:text-slate-500">
                    لم يتم إضافة أي مواد للفاتورة بعد
                </td>
            </tr>
        `;
        calculateTotalFromCart();
        return;
    }

    cart.forEach((item, index) => {
        const row = `
            <tr class="border-b border-slate-100 dark:border-slate-800">
                <td class="px-3 py-3 font-semibold text-slate-800 dark:text-slate-200">${item.trade_name}</td>
                <td class="px-3 py-3 text-slate-500 dark:text-slate-400">${item.unit_type === 'strip' ? 'شريط' : 'باكيت'}</td>
                <td class="px-3 py-3">${item.price.toLocaleString('en-US')}</td>
                <td class="px-3 py-3 text-center font-bold">${item.qty}</td>
                <td class="px-3 py-3 font-bold text-emerald-600 dark:text-emerald-400">${item.subtotal.toLocaleString('en-US')}</td>
                <td class="px-3 py-3 text-center">
                    <button type="button" onclick="removeItem(${index})" class="text-rose-500 hover:text-rose-700 font-bold">
                        ✕
                    </button>
                </td>
            </tr>
        `;
        tbody.innerHTML += row;
    });

    calculateTotalFromCart();
}

// حذف عنصر من السلة
function removeItem(index) {
    cart.splice(index, 1);
    updateCartTable();
}

// حساب المجموع الكلي والصافي
function calculateTotalFromCart() {
    let grandTotal = 0;
    cart.forEach(item => {
        grandTotal += item.subtotal;
    });

    const discountInput = document.getElementById('discount_input');
    const discountType = document.getElementById('discount_type') ? document.getElementById('discount_type').value : 'fixed';

    const rawDiscountValue = discountInput ? (parseFloat(discountInput.value) || 0) : 0;
    let actualDiscountAmount = 0;

    if (discountType === 'percent') {
        actualDiscountAmount = (grandTotal * rawDiscountValue) / 100;
    } else {
        actualDiscountAmount = rawDiscountValue;
    }

    if (actualDiscountAmount > grandTotal) {
        actualDiscountAmount = grandTotal;
    }

    const finalAmount = grandTotal - actualDiscountAmount;

    const totalDisplay = document.getElementById('total_amount_display');
    const finalDisplay = document.getElementById('final_amount_display');

    if (totalDisplay) totalDisplay.innerText = grandTotal.toLocaleString('en-US');
    if (finalDisplay) finalDisplay.innerText = finalAmount.toLocaleString('en-US');

    const hiddenTotal = document.getElementById('hidden_total_amount');
    const hiddenDiscount = document.getElementById('hidden_discount');
    const hiddenFinal = document.getElementById('hidden_final_amount');

    if (hiddenTotal) hiddenTotal.value = grandTotal;
    if (hiddenDiscount) hiddenDiscount.value = actualDiscountAmount;
    if (hiddenFinal) hiddenFinal.value = finalAmount;
}

// ==========================================
// الربط التلقائي والبحث بالباركود والاسم
// ==========================================
const barcodeInput = document.getElementById('product_barcode');
const tradeNameInput = document.getElementById('trade_name');
const searchResultsBox = document.getElementById('pos-search-results');

// 1. الاستماع للماسح الضوئي في حقل الباركود
if (barcodeInput) {
    barcodeInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const query = this.value.trim();
            if (query.length > 0) {
                fetchProductAndFill(query, true); // true = إضافة تلقائية للسلة
            }
        }
    });
}

// 2. البحث الفوري أثناء الكتابة في اسم الدواء
if (tradeNameInput) {
    let searchTimeout;
    tradeNameInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            if (searchResultsBox) searchResultsBox.classList.add('hidden');
            return;
        }

        searchTimeout = setTimeout(() => {
            fetchProductAndFill(query, false);
        }, 250);
    });
}

// جلب البيانات بواسطة دالة API
function fetchProductAndFill(query, autoAdd = false) {
    fetch(`/products/pos-search?query=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            if (data.type === 'barcode' && data.product) {
                fillFields(data.product);
                if (searchResultsBox) searchResultsBox.classList.add('hidden');
                if (autoAdd) {
                    addItemToCart();
                }
            } else if (data.type === 'search' && data.products) {
                if (data.products.length === 1 && autoAdd) {
                    fillFields(data.products[0]);
                    addItemToCart();
                } else {
                    renderSearchResults(data.products);
                }
            }
        })
        .catch(err => console.error("Search error:", err));
}

// عرض نتائج البحث تحت حقل الاسم
function renderSearchResults(products) {
    if (!searchResultsBox) return;

    if (!products || products.length === 0) {
        searchResultsBox.classList.add('hidden');
        return;
    }

    let html = '';
    products.forEach(p => {
        const jsonString = JSON.stringify(p).replace(/'/g, "&apos;").replace(/"/g, "&quot;");
        html += `
            <div onclick='selectProductFromSearch(${jsonString})' 
                 class="p-2.5 hover:bg-slate-100 dark:hover:bg-slate-700 cursor-pointer border-b border-slate-100 dark:border-slate-700">
                <div class="font-bold text-slate-800 dark:text-white">${p.trade_name}</div>
                <div class="text-slate-400 text-[10px]">${p.scientific_name || ''}</div>
            </div>
        `;
    });

    searchResultsBox.innerHTML = html;
    searchResultsBox.classList.remove('hidden');
}

// عند اختيار دواء من القائمة المنبثقة
function selectProductFromSearch(product) {
    fillFields(product);
    if (searchResultsBox) searchResultsBox.classList.add('hidden');
    const qtyInput = document.getElementById('product_qty');
    if (qtyInput) qtyInput.focus();
}

// تعبئة البيانات في الحقول
let currentPacketPrice = 0;
let currentStripPrice = 0;

function fillFields(product) {
    // تخزين ID المنتج الحالي
    currentProductId = product.id;

    if (document.getElementById('product_barcode')) document.getElementById('product_barcode').value = product.barcode || '';
    if (document.getElementById('trade_name')) document.getElementById('trade_name').value = product.trade_name || '';
    if (document.getElementById('scientific_name')) document.getElementById('scientific_name').value = product.scientific_name || '';

    currentPacketPrice = Number(product.selling_price) || 0;

    // فحص المسميين (strips_capacity أو items_per_packet) لضمان القراءة الصحيحة
    const itemsPerPacket = Number(product.strips_capacity) || Number(product.items_per_packet) || 1;
    
    // حساب سعر الشريط المقسوم
    currentStripPrice = currentPacketPrice / itemsPerPacket;

    const unitSelect = document.getElementById('sale_unit');
    if (unitSelect) unitSelect.value = 'strip'; 

    updatePriceField();
}

// دالة تحديث السعر حسب الاختيار (باكيت أو شريط)
function changeSaleUnit() {
    updatePriceField();
}

function updatePriceField() {
    const unitSelect = document.getElementById('sale_unit');
    const priceInput = document.getElementById('product_price');
    if (!priceInput) return;

    let finalPrice = currentPacketPrice;

    if (unitSelect && unitSelect.value === 'strip') {
        finalPrice = currentStripPrice;
    }

    priceInput.value = finalPrice.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
}

// تفعيل استماع التغيير على القائمة المنسدلة
document.addEventListener('DOMContentLoaded', function() {
    const unitSelect = document.getElementById('sale_unit');
    if (unitSelect) {
        unitSelect.addEventListener('change', changeSaleUnit);
    }
});

// إخفاء قائمة البحث عند النقر خارجها
document.addEventListener('click', function(e) {
    if (searchResultsBox && !searchResultsBox.contains(e.target) && e.target !== tradeNameInput) {
        searchResultsBox.classList.add('hidden');
    }
});

// ==========================================
// تحويل عناصر السلة إلى حقول مخفية عند الحفظ
// ==========================================
const invoiceForm = document.getElementById('invoiceForm');

if (invoiceForm) {
    invoiceForm.addEventListener('submit', function (e) {
        // 1. التثبت من وجود مواد داخل السلة
        if (typeof cart !== 'undefined' && cart.length === 0) {
            e.preventDefault();
            alert('يرجى إضافة مواد إلى الفاتورة أولاً!');
            return false;
        }

        // 2. إزالة الحقول المخفية المضافة سابقاً منعاً للتكرار
        document.querySelectorAll('.cart-hidden-input').forEach(el => el.remove());

        // 3. تحويل cart إلى inputs مخفية
        cart.forEach((item, index) => {
            Object.keys(item).forEach(key => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.className = 'cart-hidden-input';
                hiddenInput.name = `items[${index}][${key}]`;
                hiddenInput.value = item[key] !== null ? item[key] : '';
                invoiceForm.appendChild(hiddenInput);
            });
        });
    });
}
</script>
@endsection
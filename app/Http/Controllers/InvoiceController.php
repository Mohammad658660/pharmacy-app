<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Debt;
use App\Models\Product;

class InvoiceController extends Controller
{
    public function index()
    {
        $sales = Sale::with('items')->latest()->paginate(10);
        return view('sales.history', compact('sales'));
    }

    public function print($id)
    {
        $sale = Sale::with('items')->findOrFail($id);
        return view('sales.print', compact('sale'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name'  => 'required|string',
            'customer_phone' => 'nullable|string',
            'payment_method' => 'required|string',
            'total_amount'   => 'required|numeric',
            'discount'       => 'nullable|numeric',
            'final_amount'   => 'required|numeric',
            'notes'          => 'nullable|string',
            'items'          => 'required|array',
        ]);

        DB::transaction(function () use ($data) {

            $invoiceNumber = 'INV-' . time() . rand(10, 99);
            $paidAmount = ($data['payment_method'] === 'credit') ? 0 : $data['final_amount'];
            $remainingAmount = ($data['payment_method'] === 'credit') ? $data['final_amount'] : 0;

            // 1. إنشاء الفاتورة
            $sale = Sale::create([
                'invoice_number'   => $invoiceNumber,
                'customer_name'    => $data['customer_name'],
                'customer_phone'   => $data['customer_phone'] ?? null,
                'total_amount'     => $data['total_amount'],
                'discount'         => $data['discount'] ?? 0,
                'final_amount'     => $data['final_amount'],
                'paid_amount'      => $paidAmount,
                'remaining_amount' => $remainingAmount,
                'payment_method'   => $data['payment_method'],
                'payment_type'     => ($data['payment_method'] === 'credit') ? 'debt' : 'cash',
                'notes'            => $data['notes'] ?? null,
            ]);

            // 2. حفظ عناصر الفاتورة وخصم الكميات من المخزون
            foreach ($data['items'] as $item) {
                $qty = (float)($item['qty'] ?? $item['quantity'] ?? 1);
                $price = (float)($item['price'] ?? 0);

                // حفظ عنصر الفاتورة
                $sale->items()->create([
                    'trade_name'      => $item['trade_name'] ?? '',
                    'scientific_name' => $item['scientific_name'] ?? null,
                    'price'           => $price,
                    'quantity'        => $qty,
                    'subtotal'        => $price * $qty,
                ]);

                // البحث عن المنتج
                $product = null;
                if (!empty($item['product_id'])) {
                    $product = Product::find($item['product_id']);
                }
                if (!$product && !empty($item['trade_name'])) {
                    $product = Product::where('trade_name', trim($item['trade_name']))->first();
                }

                // 3. معادلة خصم الكمية بالباكيت والشريط
                if ($product) {
                    $unitType = $item['unit_type'] ?? 'packet'; // 'packet' أو 'strip'
                    $itemsPerPacket = (int)($product->items_per_packet ?? 1);
                    if ($itemsPerPacket <= 0) $itemsPerPacket = 1;

                    // أ) تحويل إجمالي رصيد المخزن الحالي إلى "أشرطة"
                    $currentPackets = (int)($product->quantity_packets ?? 0);
                    $currentStrips  = (int)($product->quantity_strips ?? 0);
                    $totalStripsInStock = ($currentPackets * $itemsPerPacket) + $currentStrips;

                    // ب) حساب الكمية المباعة مقدّرة بـ "الأشرطة"
                    if ($unitType === 'strip') {
                        $soldInStrips = $qty;
                    } else {
                        $soldInStrips = $qty * $itemsPerPacket; // إذا بيع بالباكيت
                    }

                    // ج) حساب المتبقي من الأشرطة
                    $remainingTotalStrips = max(0, $totalStripsInStock - $soldInStrips);

                    // د) إعادة تحويل الأشرطة المتبقية إلى (باكيتات + أشرطة)
                    $product->quantity_packets = floor($remainingTotalStrips / $itemsPerPacket);
                    $product->quantity_strips  = $remainingTotalStrips % $itemsPerPacket;

                    $product->save();
                }
            }

            // 4. إنشاء سجل الدين إذا كانت طريقة الدفع آجل (credit)
            if ($data['payment_method'] === 'credit') {
                Debt::create([
                    'customer_name'    => $data['customer_name'],
                    'phone'            => $data['customer_phone'] ?? null,
                    'total_amount'     => $data['final_amount'],
                    'paid_amount'      => 0,
                    'remaining_amount' => $data['final_amount'],
                    'notes'            => $data['notes'] ?? null,
                    'status'           => 'pending',
                ]);
            }

        });

        return redirect()->back()->with('success', 'تم حفظ الفاتورة وخصم الكمية دقيقاً بالباكيت والشريط');
    }
}
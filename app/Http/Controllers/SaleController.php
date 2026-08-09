<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Debt;
use App\Models\Product;
use App\Models\ProductBatch;
use Illuminate\Http\Request;
use DB;

class SaleController extends Controller
{
    // 1. إضافـة دالـة المزامنة هنا
    public function syncProductsWithBatches()
    {
        $products = Product::all();

        foreach ($products as $product) {
            $nextBatch = ProductBatch::where('product_id', $product->id)
                ->where(function($q) {
                    $q->where('quantity_packets', '>', 0)
                      ->orWhere('quantity_strips', '>', 0)
                      ->orWhere('quantity', '>', 0);
                })
                ->orderBy('expiry_date', 'asc')
                ->first();

            if ($nextBatch) {
                $product->expiry_date = $nextBatch->expiry_date;
                if (!empty($nextBatch->cost_price) && $nextBatch->cost_price > 0) {
                    $product->cost_price = $nextBatch->cost_price;
                }
                $product->save();
            }
        }
    }
    public function index()
{
    // استدعاء المزامنة لتحديث التواريخ والأسعار فوراً
    $this->syncProductsWithBatches();

    $recentSales = Sale::latest()->get();
    return view('sales.index', compact('recentSales'));
}

    public function store(Request $request)
    {
        $request->validate([
            'items'        => 'required|array|min:1',
            'payment_type' => 'required|in:cash,debt,partial',
        ]);

        DB::transaction(function () use ($request) {
            $totalAmount = (float) str_replace(',', '', $request->total_amount);
            $discount    = (float) str_replace(',', '', $request->discount ?? 0);
            $finalAmount = $totalAmount - $discount;
            $paidAmount  = (float) str_replace(',', '', $request->paid_amount ?? 0);

            if ($request->payment_type == 'cash') {
                $paidAmount = $finalAmount;
            }

            $remainingAmount = $finalAmount - $paidAmount;

            // 1. إنشاء الفاتورة
            $sale = Sale::create([
                'invoice_number'   => 'INV-' . time(),
                'customer_name'    => $request->customer_name ?? 'زبون نقدي',
                'customer_phone'   => $request->customer_phone,
                'total_amount'     => $totalAmount,
                'discount'         => $discount,
                'final_amount'     => $finalAmount,
                'paid_amount'      => $paidAmount,
                'remaining_amount' => $remainingAmount > 0 ? $remainingAmount : 0,
                'payment_type'     => $request->payment_type,
                'notes'            => $request->notes,
            ]);

            // 2. حفظ مواد الفاتورة + خصم الكمية من الوجبات والمنتجات
            foreach ($request->items as $item) {
                $unitPrice = (float) str_replace(',', '', $item['price'] ?? 0);
                $qty       = (float) ($item['qty'] ?? 1);
                $subtotal  = $unitPrice * $qty;

                SaleItem::create([
                    'sale_id'         => $sale->id,
                    'trade_name'      => $item['trade_name'],
                    'scientific_name' => $item['scientific_name'] ?? null,
                    'quantity'        => $qty,
                    'unit_price'      => $unitPrice,
                    'subtotal'        => $subtotal,
                ]);

                // البحث عن المنتج لخصم المخزون
$productId = $item['product_id'] ?? $item['id'] ?? null;

$product = null;
if ($productId) {
    $product = Product::find($productId);
}
if (!$product && !empty($item['trade_name'])) {
    $product = Product::where('trade_name', $item['trade_name'])->first();
}

if ($product) {
    $unitType = $item['unit_type'] ?? $item['sale_unit'] ?? 'packet';
    
    // سعة الباكيت بالأشرطة
    $stripsPerPacket = $product->strips_capacity ?? $product->items_per_packet ?? 1;
    if ($stripsPerPacket <= 0) {
        $stripsPerPacket = 1;
    }

    // حساب إجمالي الكمية المباعة بالأشرطة
    $totalStripsToSell = ($unitType === 'strip') ? $qty : ($qty * $stripsPerPacket);

    // أ) جلب الوجبات المتبقية والمرتبطة بالمنتج بترتيب الأقرب انتهاءً
    $batches = ProductBatch::where('product_id', $product->id)
        ->orderBy('expiry_date', 'asc')
        ->get();

    $remainingStripsToDeduct = $totalStripsToSell;

    foreach ($batches as $batch) {
        if ($remainingStripsToDeduct <= 0) break;

        // دعم مسميات الأعمدة المختلفة للوجبات
        $batchPackets = $batch->quantity_packets ?? $batch->quantity ?? 0;
        $batchStrips  = $batch->quantity_strips ?? 0;

        $batchTotalStrips = ($batchPackets * $stripsPerPacket) + $batchStrips;

        if ($batchTotalStrips <= 0) continue;

        if ($batchTotalStrips <= $remainingStripsToDeduct) {
            $remainingStripsToDeduct -= $batchTotalStrips;
            
            if (isset($batch->quantity_packets)) $batch->quantity_packets = 0;
            if (isset($batch->quantity_strips))  $batch->quantity_strips  = 0;
            if (isset($batch->quantity))        $batch->quantity         = 0;
        } else {
            $newBatchStrips = $batchTotalStrips - $remainingStripsToDeduct;
            $remainingStripsToDeduct = 0;

            if (isset($batch->quantity_packets)) {
                $batch->quantity_packets = intdiv($newBatchStrips, $stripsPerPacket);
                $batch->quantity_strips  = $newBatchStrips % $stripsPerPacket;
            } else if (isset($batch->quantity)) {
                $batch->quantity = intdiv($newBatchStrips, $stripsPerPacket);
            }
        }
        $batch->save();
    }

    // ب) خصم الكمية من المنتج الرئيسي
    $productTotalStrips = ($product->quantity_packets * $stripsPerPacket) + ($product->quantity_strips ?? 0);
    $newProductStrips   = max(0, $productTotalStrips - $totalStripsToSell);

    $product->quantity_packets = intdiv($newProductStrips, $stripsPerPacket);
    $product->quantity_strips  = $newProductStrips % $stripsPerPacket;

    // ج) البحث عن أول وجبة متبقية فيها كمية لتحديث تاريخ الانتهاء وسعر التكلفة للمنتج الرئيسي
    $nextActiveBatch = ProductBatch::where('product_id', $product->id)
        ->where(function($q) {
            $q->where('quantity_packets', '>', 0)
              ->orWhere('quantity_strips', '>', 0)
              ->orWhere('quantity', '>', 0);
        })
        ->orderBy('expiry_date', 'asc')
        ->first();

    if ($nextActiveBatch) {
        $product->expiry_date = $nextActiveBatch->expiry_date;
        if (!empty($nextActiveBatch->cost_price) && $nextActiveBatch->cost_price > 0) {
            $product->cost_price = $nextActiveBatch->cost_price;
        }
    }

    $product->save();
}
            }

            // 3. إذا كان البيع بالدين أو جزئي، يتم إنشاء سجل في جدول الديون تلقائياً
            if ($request->payment_type != 'cash' && $remainingAmount > 0) {
                $itemNames = collect($request->items)->pluck('trade_name')->filter()->implode(', ');
                if (empty($itemNames)) {
                    $itemNames = collect($request->items)->pluck('name')->filter()->implode(', ');
                }

                $fullNotes = "عن فاتورة مبيعات (#{$sale->invoice_number}) - $itemNames";
                if ($request->notes) {
                    $fullNotes .= " - ملاحظة: " . $request->notes;
                }

                Debt::create([
                    'customer_name'    => $request->customer_name ?? 'زبون غير مسمى',
                    'phone'            => $request->customer_phone,
                    'total_amount'     => $finalAmount,
                    'paid_amount'      => $paidAmount,
                    'remaining_amount' => $remainingAmount,
                    'status'           => $paidAmount > 0 ? 'partial' : 'pending',
                    'notes'            => $fullNotes,
                ]);
            }
        });

        return redirect()->back()->with('success', 'تم حفظ الفاتورة بنجاح!');
    }
}
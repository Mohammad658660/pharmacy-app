<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Debt;
use Illuminate\Http\Request;
use DB;

class SaleController extends Controller
{
    public function index()
    {
        $recentSales = Sale::latest()->get();
        return view('sales.index', compact('recentSales'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
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

            // 2. حفظ مواد الفاتورة
            foreach ($request->items as $item) {
               SaleItem::create([
            'sale_id'         => $sale->id,
            'trade_name'      => $item['trade_name'],
            'scientific_name' => $item['scientific_name'],
            'quantity'        => $item['qty'],
            'unit_price'      => (float) str_replace(',', '', $item['price']),
            'subtotal'        => (float) str_replace(',', '', $item['price']) * $item['qty'],
        ]);
            }

            // 3. إذا كان البيع بالدين أو جزئي، يتم إنشاء سجل في جدول الديون تلقائياً!
            if ($request->payment_type != 'cash' && $remainingAmount > 0) {
                
                // تجميع أسماء الأدوية لوضعها في ملاحظات الدين
                $itemNames = collect($request->items)->pluck('name')->implode(', ');
                $fullNotes = "عن فاتورة مبيعات (#{$sale->invoice_number}): " . $itemNames;
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
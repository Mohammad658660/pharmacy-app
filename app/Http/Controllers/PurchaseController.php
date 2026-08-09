<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\ProductBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
public function destroy($id)
{
    $purchase = Purchase::findOrFail($id);
    $purchase->delete();

    return redirect()->back()->with('success', 'تم حذف سجل الشراء بنجاح');
}

public function destroyAll()
{
    Purchase::query()->delete();

    return redirect()->back()->with('success', 'تم حذف جميع سجلات المشتريات بنجاح');
}
    public function index()
    {
        $purchases = Purchase::with('product')->latest()->paginate(15);
        $products = Product::all();
        
        return view('purchases.index', compact('purchases', 'products'));
    }

public function store(Request $request)
{
    $validated = $request->validate([
        'product_id'       => 'required|exists:products,id',
        'quantity_packets' => 'required|integer|min:0',
        'quantity_strips'  => 'nullable|integer|min:0',
        'cost_price'       => 'required|numeric|min:0',
        'expiry_date'      => 'required|date',
        'batch_number'     => 'nullable|string',
    ]);

    $strips = $request->quantity_strips ?? 0;

    DB::transaction(function () use ($validated, $strips, $request) {
        // 1. تسجيل الشحنة في جدول المشتريات
        Purchase::create([
            'product_id'       => $validated['product_id'],
            'quantity_packets' => $validated['quantity_packets'],
            'quantity_strips'  => $strips,
            'cost_price'       => $validated['cost_price'],
            'expiry_date'      => $validated['expiry_date'],
        ]);

        // 2. إضافة الوجبة (Batch)
        ProductBatch::create([
            'product_id'       => $validated['product_id'],
            'batch_number'     => $request->batch_number,
            'quantity_packets' => $validated['quantity_packets'],
            'quantity_strips'  => $strips,
            'expiry_date'      => $validated['expiry_date'],
        ]);

        // 3. تحديث رصيد المنتج بالتحويل التلقائي
        $product = Product::findOrFail($validated['product_id']);
        
        // استخدام الدالة للتحقق من عدد الأشرطة للباكيت الواحد
        $stripsPerPacket = $product->strips_capacity;

        if ($stripsPerPacket > 0) {
            // حساب الإجمالي الحالي بالشريط + الإجمالي المضاف بالشريط
            $currentTotalStrips = ($product->quantity_packets * $stripsPerPacket) + $product->quantity_strips;
            $addedTotalStrips   = ($validated['quantity_packets'] * $stripsPerPacket) + $strips;
            
            $newTotalStrips = $currentTotalStrips + $addedTotalStrips;

            // عملية القسمة والباكيات المتبقية
            $product->quantity_packets = intdiv($newTotalStrips, $stripsPerPacket);
            $product->quantity_strips  = $newTotalStrips % $stripsPerPacket;
        } else {
            // في حال عدم وجود نظام أشرطة للمنتج
            $product->quantity_packets += $validated['quantity_packets'];
            $product->quantity_strips  += $strips;
        }

        $product->save();
    });

    return redirect()->back()->with('success', 'تم تحديث المخزون وإعادة تجميع الباكيتات بنجاح');
}
}
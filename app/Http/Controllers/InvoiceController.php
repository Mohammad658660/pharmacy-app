<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Debt;

class InvoiceController extends Controller
{
    public function index()
{
    // جلب جميع الفواتير مع أدوية كل فاتورة وتصفحها بالتنظيم (Paginate)
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

            $sale = Sale::create([
                'invoice_number'   => $invoiceNumber,
                'customer_name'    => $data['customer_name'],
                'customer_phone'   => $data['customer_phone'],
                'total_amount'     => $data['total_amount'],
                'discount'         => $data['discount'] ?? 0,
                'final_amount'     => $data['final_amount'],
                'paid_amount'      => $paidAmount,
                'remaining_amount' => $remainingAmount,
                'payment_method'   => $data['payment_method'],
                'payment_type'     => $data['payment_method'] === 'credit' ? 'debt' : 'cash',
                'notes'            => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
    $sale->items()->create([
        'trade_name'      => $item['trade_name'],
        'scientific_name' => $item['scientific_name'] ?? null,
        'price'           => $item['price'],
        'quantity'        => $item['qty'] ?? $item['quantity'], // مرونة لتقبل qty أو quantity
        'subtotal'        => (float)$item['price'] * (int)($item['qty'] ?? $item['quantity']),
    ]);
}

         if ($data['payment_method'] === 'credit') {
    Debt::create([
        'customer_name'    => $data['customer_name'],
        'phone'            => $data['customer_phone'] ?? null,
        'total_amount'     => $data['final_amount'],    // المبلغ الكلي للدين
        'paid_amount'      => 0,                       // الواصل (صفر حالياً)
        'remaining_amount' => $data['final_amount'],    // المتبقي (نفس المبلغ الكلي)
        'notes'            => $data['notes'] ?? null,
        'status'           => 'pending',
    ]);
}
        });

        return redirect()->back()->with('success', 'تم حفظ الفاتورة بنجاح!');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use Illuminate\Http\Request;

class DebtController extends Controller
{



    /**
     * عرض الديون النشطة فقط (غير المسددة بالكامل)
     */
    public function index()
    {
        $debts = Debt::where('status', '!=', 'paid')
                    ->latest()
                    ->get();

        return view('debts.index', compact('debts'));
    }

    /**
     * حفظ دين جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'total_amount'  => 'required',
        ]);

        // تنظيف المبالغ من الفوارز
        $totalAmount = (float) str_replace(',', '', $request->total_amount);
        $paidAmount  = (float) str_replace(',', '', $request->paid_amount ?? 0);
        $remaining   = $totalAmount - $paidAmount;

        // تحديد الحالة
        $status = 'pending';
        if ($paidAmount >= $totalAmount) {
            $status = 'paid';
            $remaining = 0;
        } elseif ($paidAmount > 0) {
            $status = 'partial';
        }

        Debt::create([
            'customer_name'    => $request->customer_name,
            'phone' => $request->phone ?? $request->customer_phone,
            'total_amount'     => $totalAmount,
            'paid_amount'      => $paidAmount,
            'remaining_amount' => $remaining,
            'status'           => $status,
            'notes'            => $request->notes,
        ]);

        return redirect()->back()->with('success', 'تم تسجيل الدين بنجاح!');
    }

  public function pay(Request $request, $id)
{
    $request->validate([
        'amount' => 'required',
    ]);

    $debt = Debt::findOrFail($id);

    $payAmount = (float) str_replace(',', '', $request->amount);

    if ($payAmount <= 0) {
        return redirect()->back()->with('error', 'يرجى إدخال مبلغ تسديد صحيح!');
    }

    $newPaid = $debt->paid_amount + $payAmount;
    $newRemaining = $debt->total_amount - $newPaid;

    // إذا أصبح المتبقي صفراً أو أقل، تتحول الحالة تلقائياً إلى paid وتنتقل للأرشيف
    $status = $newRemaining <= 0 ? 'paid' : 'partial';

    $debt->update([
        'paid_amount' => $newPaid,
        'remaining_amount' => $newRemaining > 0 ? $newRemaining : 0,
        'status' => $status,
    ]);

    return redirect()->back()->with('success', 'تم تسديد الدفعة بنجاح');
}
    /**
     * تعديل بيانات الدين
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'total_amount'  => 'required',
        ]);

        $debt = Debt::findOrFail($id);

        $totalAmount = (float) str_replace(',', '', $request->total_amount);
        $paidAmount  = (float) str_replace(',', '', $request->paid_amount ?? 0);
        $remaining   = $totalAmount - $paidAmount;

        $status = 'pending';
        if ($paidAmount >= $totalAmount) {
            $status = 'paid';
            $remaining = 0;
        } elseif ($paidAmount > 0) {
            $status = 'partial';
        }

        $debt->update([
            'customer_name'    => $request->customer_name,
            'phone' => $request->phone ?? $request->customer_phone,
            'total_amount'     => $totalAmount,
            'paid_amount'      => $paidAmount,
            'remaining_amount' => $remaining,
            'status'           => $status,
            'notes'            => $request->notes,
        ]);

        return redirect()->back()->with('success', 'تم تعديل بيانات الدين بنجاح!');
    }

    /**
     * عرض أرشيف الديون المسددة بالكامل
     */
// 1️⃣ دالة جلب أرشيف الديون المسددة
// 1️⃣ دالة جلب أرشيف الديون المسددة
public function archive()
{
    // جلب كافة الديون التي اكتمل تسديدها أو أصبحت حالتها paid
    $debts = Debt::where('remaining_amount', '<=', 0)
                 ->orWhere('status', 'paid')
                 ->orderBy('updated_at', 'desc')
                 ->get();

    return view('debts.archive', compact('debts'));
}

// 2️⃣ دالة التسديد المصححة (لتسجيل المبلغ المسدد والمتبقي معاً)
public function payInstallment(Request $request, $id)
{
    $request->validate([
        'amount' => 'required|numeric|min:0.01',
    ]);

    $debt = Debt::findOrFail($id);

    // زيادة المبلغ المسدد وخصم المتبقي
    $debt->paid_amount += $request->amount;
    $debt->remaining_amount = max(0, $debt->total_amount - $debt->paid_amount);

    // إذا تم تسديد كامل المبلغ تغير حالة الدين
    if ($debt->remaining_amount <= 0) {
        $debt->remaining_amount = 0;
        $debt->status = 'paid';
    }

    $debt->save();

    return redirect()->back()->with('success', 'تم تسديد الدفعة بنجاح');
}
public function destroy($id)
{
    $debt = Debt::findOrFail($id);
    $debt->delete();

    return redirect()->back()->with('success', 'تم حذف السجل بنجاح');
}
}
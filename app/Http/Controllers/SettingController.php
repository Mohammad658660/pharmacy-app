<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Setting; // أو استخدم DB::table('settings')
class SettingController extends Controller
{
    // عرض صفحة الإعدادات
   public function index()
{
    return view('settings.index');
}

public function usersIndex()
{
    $users = \App\Models\User::all();
    return view('settings.users', compact('users'));
}

    // إضافة موظف/مستخدم جديد
public function storeUser(Request $request)
{
    $request->validate([
        'name'     => 'required|string|max:255',
        'username' => 'required|string|unique:users,username',
        'password' => 'required|string|min:1',
        'role'     => 'required|string',
    ]);

 User::create([
    'name'     => $request->name,
    'username' => $request->username,
    'password' => Hash::make($request->password),
    'role' => $request->role, // مع التأكد إن الـ HTML خياراته value="employee" و value="admin"
]);

    return redirect()->back()->with('success', 'تم إنشاء الحساب بنجاح!');
}

    // تغيير كلمة المرور الحالية
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:6|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة.']);
        }

        $user->update([
            'password' => bcrypt($request->new_password)
        ]);

        return back()->with('success', 'تم تغيير كلمة المرور بنجاح!');
    }

public function pharmacyIndex()
{
    // جلب أول سجل للإعدادات أو إنشائه إن لم يكن موجوداً
    $setting = DB::table('settings')->first();
    
    return view('settings.pharmacy', compact('setting'));
}

public function updatePharmacy(Request $request)
{
    $request->validate([
        'pharmacy_name'  => 'required|string|max:255',
        'phone'          => 'nullable|string',
        'address'        => 'nullable|string',
        'invoice_footer' => 'nullable|string',
        'logo'           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $data = [
        'pharmacy_name'  => $request->pharmacy_name,
        'phone'          => $request->phone,
        'address'        => $request->address,
        'invoice_footer' => $request->invoice_footer,
    ];

    // رفع الشعار إذا تم اختياره
    if ($request->hasFile('logo')) {
        $logoPath = $request->file('logo')->store('logos', 'public');
        $data['logo'] = $logoPath;
    }

    $existing = DB::table('settings')->first();

    if ($existing) {
        DB::table('settings')->where('id', $existing->id)->update($data);
    } else {
        DB::table('settings')->insert($data);
    }

    return redirect()->back()->with('success', 'تم تحديث معلومات الصيدلية بنجاح!');
}
}
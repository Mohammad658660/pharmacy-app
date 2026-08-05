<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SettingController;
// ----------------------------------------------------
// 1️⃣ مسارات تسجيل الدخول والخروج (عامة وبدون حماية)
// ----------------------------------------------------
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// مسارات الإعدادات وإدارة المستخدمين
Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
Route::get('/settings/users', [SettingController::class, 'usersIndex'])->name('settings.users.index');
Route::post('/settings/users', [SettingController::class, 'storeUser'])->name('settings.users.store');
Route::post('/settings/password', [SettingController::class, 'updatePassword'])->name('settings.password.update');
// ----------------------------------------------------
// 2️⃣ مسارات النظام المحمية (تستوجب تسجيل الدخول)
// ----------------------------------------------------
Route::middleware(['auth'])->group(function () {

    // الواجهة الرئيسية
    Route::get('/', function () {
        return view('welcome');
    })->name('home');

    // مسارات المبيعات
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');

    // مسارات الفواتير
    Route::get('/sales/{id}/print', [InvoiceController::class, 'print'])->name('sales.print');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/sales-history', [InvoiceController::class, 'index'])->name('sales.history');

    // مسارات الديون
    Route::get('/debts', [DebtController::class, 'index'])->name('debts.index');
    Route::post('/debts', [DebtController::class, 'store'])->name('debts.store');
    Route::post('/debts/{id}/pay', [DebtController::class, 'payInstallment'])->name('debts.pay');
    Route::put('/debts/{id}', [DebtController::class, 'update'])->name('debts.update');
    Route::get('/debts/archive', [DebtController::class, 'archive'])->name('debts.archive');
    Route::delete('/debts/{id}', [DebtController::class, 'destroy'])->name('debts.destroy');
    Route::get('/settings/pharmacy', [SettingController::class, 'pharmacyIndex'])->name('settings.pharmacy.index');
    Route::post('/settings/pharmacy', [SettingController::class, 'updatePharmacy'])->name('settings.pharmacy.update');
Route::middleware(['auth'])->group(function () {
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');
});
    });
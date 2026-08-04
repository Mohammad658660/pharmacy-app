<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
 public function up(): void
{
    Schema::create('settings', function (Blueprint $table) {
        $table->id();
        $table->string('pharmacy_name')->nullable()->default('صيدلية الشفاء');
        $table->string('phone')->nullable();
        $table->string('address')->nullable();
        $table->string('logo')->nullable();
        $table->text('invoice_footer')->nullable(); // ملاحظة أسفل الفاتورة
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
 public function down(): void
{
    Schema::dropIfExists('settings');
}
};

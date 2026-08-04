<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
 {
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('trade_name'); // اسم الدواء التجاري
        $table->string('scientific_name')->nullable(); // الاسم العلمي
        $table->string('barcode')->unique()->nullable(); // الباركود
        $table->decimal('cost_price', 10, 2)->default(0); // سعر الكلفة
        $table->decimal('selling_price', 10, 2); // سعر البيع
        $table->integer('quantity_packets')->default(0); // الكمية بالعلب
        $table->integer('min_quantity')->default(5); // الحد الأدنى
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

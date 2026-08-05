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
            
            // الأسماء والمواد الفعالة
            $table->string('trade_name'); // اسم الدواء التجاري (أو الإنجليزي)
            $table->string('name_ar')->nullable(); // الاسم العربي من الإكسل
            $table->string('scientific_name')->nullable(); // الاسم العلمي / المادة الفعالة
            
            // تفاصيل إضافية من ملف الإكسل
            $table->string('company')->nullable(); // الشركة
            $table->string('category')->nullable(); // الفئة
            $table->string('form')->nullable(); // الشكل الصيدلاني (أقراص، شراب، إلخ)
            
            // الباركود والأسعار والكميات
            $table->string('barcode')->nullable()->unique(); // الباركود
            $table->decimal('cost_price', 10, 2)->default(0); // سعر الكلفة
            $table->decimal('selling_price', 10, 2)->default(0); // سعر البيع للصيدلية
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
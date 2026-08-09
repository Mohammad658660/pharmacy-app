<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('batch_number')->nullable(); // رقم الوجبة إن وجد
            $table->integer('quantity_packets')->default(0); // عدد الباكيتات في هذه الوجبة
            $table->integer('quantity_strips')->default(0);  // عدد الشريط المتبقية في هذه الوجبة
            $table->date('expiry_date'); // تاريخ انتهائها
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_batches');
    }
};
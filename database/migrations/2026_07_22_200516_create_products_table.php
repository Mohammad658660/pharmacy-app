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
    $table->text('trade_name')->nullable();
    $table->text('name_ar')->nullable();
    $table->text('scientific_name')->nullable();
    $table->text('company')->nullable();
    $table->text('category')->nullable();
    $table->text('form')->nullable();
    $table->decimal('cost_price', 10, 2)->default(0);
    $table->decimal('selling_price', 10, 2)->default(0);
    $table->integer('quantity_packets')->default(0);
    $table->integer('min_quantity')->default(5);
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
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('products', function (Blueprint $table) {
        if (!Schema::hasColumn('products', 'expiry_date')) {
            $table->date('expiry_date')->nullable();
        }
        if (!Schema::hasColumn('products', 'min_quantity')) {
            $table->integer('min_quantity')->default(5);
        }
        if (!Schema::hasColumn('products', 'damaged_quantity')) {
            $table->integer('damaged_quantity')->default(0);
        }
    });
}
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columnsToDrop = [];
            
            if (Schema::hasColumn('products', 'expiry_date')) {
                $columnsToDrop[] = 'expiry_date';
            }
            if (Schema::hasColumn('products', 'damaged_quantity')) {
                $columnsToDrop[] = 'damaged_quantity';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
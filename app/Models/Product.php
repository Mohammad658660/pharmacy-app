<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'barcode',
        'trade_name',
        'name_ar',
        'scientific_name',
        'company',
        'category',
        'form',
        'cost_price',
        'selling_price',
        'quantity_packets',
        'quantity_strips',
        'items_per_packet',
        'min_quantity',
        'damaged_quantity',
        'expiry_date',
        'quantity_packets',
        'quantity_strips',
        'items_per_packet',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
    ];

    /**
     * الأدوية منتهية الصلاحية
     */
/**
 * الأدوية منتهية الصلاحية
 */
public function scopeExpired($query)
{
    return $query->whereNotNull('expiry_date')
                 ->where('expiry_date', '<=', today());
}

/**
 * الأدوية القريبة من الانتهاء
 */
public function scopeNearExpiry($query, $days = 90)
{
    return $query->whereNotNull('expiry_date')
                 ->where('expiry_date', '>', today())
                 ->where('expiry_date', '<=', today()->addDays($days));
}

/**
 * الأدوية ذات الكميات القليلة (نواقص المخزون)
 */
public function scopeLowStock($query)
{
    return $query->whereColumn('quantity_packets', '<=', 'min_quantity')
                 ->where('quantity_packets', '>', 0);
}

    /**
     * المواد والأدوية التالفة
     */
    public function scopeDamaged($query)
    {
        return $query->where('damaged_quantity', '>', 0);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    'min_quantity',
];
}
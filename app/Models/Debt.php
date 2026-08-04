<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'phone',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'notes',
        'due_date',
        'status',
    ];
    
}
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        if (Product::count() === 0) {
            Product::create([
                'trade_name' => '1 2 3 (ONE TWO THREE) 20 F.C.TABS.',
                'scientific_name' => 'PARACETAMOL + PSEUDOEPHEDRINE',
                'barcode' => '6222001401080',
                'cost_price' => 0.00,
                'selling_price' => 10.00,
                'quantity_packets' => 10,
                'min_quantity' => 5
            ]);

            Product::create([
                'trade_name' => '1 2 3 EXTRA 20 F.C.TABS.',
                'scientific_name' => 'PARACETAMOL + PSEUDOEPHEDRINE',
                'barcode' => '6222001401081',
                'cost_price' => 30.44,
                'selling_price' => 37.00,
                'quantity_packets' => 7,
                'min_quantity' => 5
            ]);

            Product::create([
                'trade_name' => 'VITAMIN C 1 GM',
                'scientific_name' => 'ASCORBIC ACID',
                'barcode' => '6222001401082',
                'cost_price' => 15.00,
                'selling_price' => 26.00,
                'quantity_packets' => 0,
                'min_quantity' => 5
            ]);
        }

        $products = Product::all();
        return view('products.index', compact('products'));
    }
}
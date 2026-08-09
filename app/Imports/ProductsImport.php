<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;

class ProductsImport implements ToModel
{
    public function model(array $row)
    {
        // تحويل المفاتيح إلى قيم متسلسلة تلقائياً (0, 1, 2, 3...) بغض النظر عن مفاتيح الإكسل
        $values = array_values($row);

        $tradeName  = trim((string)($values[0] ?? '')); // العمود A: اسم انجليزي
        $nameAr     = trim((string)($values[1] ?? '')); // العمود B: اسم عربي
        $scientific = trim((string)($values[2] ?? '')); // العمود C: مادة فعالة
        $company    = trim((string)($values[3] ?? '')); // العمود D: الشركة
        $category   = trim((string)($values[4] ?? '')); // العمود E: الفئة
        $form       = trim((string)($values[5] ?? '')); // العمود F: شكل صيدلاني
        $priceRaw   = trim((string)($values[6] ?? '0'));// العمود G: سعر جديد

        // تجاوز صف العناوين أو الصفوف الفارغة تماماً
        if (
            (empty($tradeName) && empty($nameAr) && empty($scientific)) ||
            $tradeName === 'اسم انجليزي' ||
            str_contains($tradeName, 'اسم انجليزي') ||
            $tradeName === 'trade_name'
        ) {
            return null;
        }

        $sellingPrice = is_numeric($priceRaw) ? (float)$priceRaw : 0;

        return new Product([
            'trade_name'       => $tradeName ?: ($nameAr ?: 'بدون اسم'),
            'name_ar'          => $nameAr ?: null,
            'scientific_name'  => $scientific ?: null,
            'company'          => $company ?: null,
            'category'         => $category ?: null,
            'form'             => $form ?: null,
            'selling_price'    => $sellingPrice,
            'cost_price'       => 0,
            'quantity_packets' => 0,
            'items_per_packet' => 1,
            'min_quantity'     => 0,
        ]);
    }
}
<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // البحث عن الاسم التجاري (الإنجليزي) وتجاهل الأسطر الفارغة
        $tradeName = $row['asm_anglyzy'] 
            ?? $row['trade_name'] 
            ?? $row['name_en'] 
            ?? null;

        if (empty($tradeName)) {
            return null;
        }

        // قراءة باقي البيانات بحسب أسماء أعمدة شيت الإكسل
        $nameAr         = $row['asm_aarby'] ?? $row['asm_3rby'] ?? $row['name_ar'] ?? null;
        $scientificName = $row['mad_faaal'] ?? $row['madp_f3alp'] ?? $row['scientific_name'] ?? null;
        $company        = $row['alshrk'] ?? $row['alshrkp'] ?? $row['company'] ?? null;
        $category       = $row['alfy'] ?? $row['alfp'] ?? $row['category'] ?? null;
        $form           = $row['shkl_sydlany'] ?? $row['form'] ?? null;
        $price          = $row['saar_gdyd'] ?? $row['s3r_sydlany'] ?? $row['selling_price'] ?? 0;

      return new Product([
            'trade_name'      => $row['trade_name'] ?? $row['اسم_الدواء'] ?? $row['trade_name_en'] ?? null,
            'name_ar'         => $row['name_ar'] ?? $row['الاسم_العربي'] ?? null,
            'scientific_name' => $row['scientific_name'] ?? $row['المادة_الفعالة'] ?? null,
            'company'         => $row['company'] ?? $row['الشركة'] ?? null,
            'category'        => $row['category'] ?? $row['التصنيف'] ?? null,
            'form'            => $row['form'] ?? $row['الشكل_الصيدلاني'] ?? null,
            'cost_price'      => $row['cost_price'] ?? $row['سعر_التكلفة'] ?? 0,
            'selling_price'   => $row['selling_price'] ?? $row['سعر_البيع'] ?? 0,
            'quantity_packets'=> $row['quantity_packets'] ?? $row['الكمية'] ?? 0,
            'min_quantity'    => $row['min_quantity'] ?? $row['الحد_الأدنى'] ?? 5,
        ]);
    }
}
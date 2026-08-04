@php
    $setting = \Illuminate\Support\Facades\DB::table('settings')->first();
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فاتورة رقم #{{ $sale->invoice_number }}</title>
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            width: 76mm;
            margin: 0 auto;
            padding: 8mm 4mm;
            font-size: 12px;
            color: #000;
        }
        .text-center { text-align: center; }
        .justify-between { display: flex; justify-content: space-between; margin: 4px 0; }
        .border-b { border-bottom: 1px dashed #000; padding-bottom: 6px; margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; margin: 8px 0; }
        th, td { text-align: right; padding: 4px 0; font-size: 11px; }
        .btn-print {
            background: #2563eb; color: #fff; border: none; padding: 6px 12px; 
            border-radius: 4px; cursor: pointer; font-weight: bold; width: 100%; margin-bottom: 10px;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print">
        <button onclick="window.print()" class="btn-print">🖨️ تأكيد الطباعة</button>
    </div>

    <div class="text-center border-b" style="padding-bottom: 8px; margin-bottom: 8px;">
    <!-- الشعار إن وجد -->
    @if(!empty($setting->logo))
        <img src="{{ asset('storage/' . $setting->logo) }}" style="max-height: 50px; margin: 0 auto 6px auto; display: block;">
    @endif

    <!-- اسم الصيدلية المجلوب من الإعدادات -->
    <h3 style="margin:0; font-size: 16px; font-weight: bold;">
        {{ $setting->pharmacy_name ?? 'صيدلية الشفاء' }}
    </h3>

    <!-- العنوان ورقم الهاتف -->
    @if(!empty($setting->address) || !empty($setting->phone))
        <p style="margin:3px 0; font-size: 10px;">
            {{ $setting->address }} {{ !empty($setting->phone) ? ' - ' . $setting->phone : '' }}
        </p>
    @endif

    <!-- رقم الفاتورة والتاريخ -->
    <p style="margin:4px 0 0 0; font-size: 11px;">
        <strong>رقم الفاتورة:</strong> #{{ $sale->invoice_number }}
    </p>
    <p style="margin:2px 0 0 0; font-size: 10px;">
        <strong>التاريخ:</strong> {{ $sale->created_at->format('Y-m-d h:i A') }}
    </p>
</div>

    <div style="margin: 6px 0; font-size: 11px;">
        <strong>العميل:</strong> {{ $sale->customer_name ?? 'زبون عام' }}
    </div>

    <div class="border-b">
        <table>
            <thead>
                <tr style="border-bottom: 1px solid #000;">
                    <th>المادة</th>
                    <th style="text-align: center;">العدد</th>
                    <th style="text-align: left;">السعر</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td>{{ $item->trade_name }}</td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: left;">{{ number_format($item->subtotal) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="justify-between">
        <span>الإجمالي:</span>
        <span>{{ number_format($sale->total_amount) }} د.ع</span>
    </div>
    @if($sale->discount > 0)
    <div class="justify-between">
        <span>الخصم:</span>
        <span>{{ number_format($sale->discount) }} د.ع</span>
    </div>
    @endif
    <div class="justify-between" style="font-weight: bold; font-size: 13px; border-top: 1px solid #000; padding-top: 4px; margin-top: 6px;">
        <span>الصافي:</span>
        <span>{{ number_format($sale->final_amount) }} د.ع</span>
    </div>

   
@if(!empty($setting->invoice_footer))
    <div class="text-center" style="margin-top: 10px; padding-top: 6px; border-top: 1px dashed #000; font-size: 10px;">
        {{ $setting->invoice_footer }}
    </div>
@endif
</body>
</html>
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReceiptScannerController extends Controller
{
    public function scan(Request $request)
    {
        // 1. التحقق من صحة الملف المرفوع
        $request->validate([
            'receipt' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240', // 10MB كحد أقصى
        ]);

        try {
            $imagePath = $request->file('receipt')->path();
            $imageData = base64_encode(file_get_contents($imagePath));
            $mimeType = $request->file('receipt')->getMimeType();

            $apiKey = config('services.gemini.key');

            if (!$apiKey) {
                return response()->json(['error' => 'مفتاح Gemini API غير معرف في الملفات.'], 500);
            }

            // 2. صياغة الـ Prompt للذكاء الاصطناعي
            $prompt = "أنت مساعد متخصص في تحليل وصلات وقوائم مشتريات الأدوية والمستلزمات الطبية.
قم بقراءة صورة الوصل المرفقة واستخراج البيانات منها ودقة عالية.
أرجع النتيجة بصيغة JSON فقط دون أي نصوص إضافية أو ماركداون.

الهيكل المطلوب للـ JSON:
{
    \"supplier_name\": \"اسم المورد أو الشركة (إذا وجد، وإلا null)\",
    \"invoice_number\": \"رقم القائمة أو الوصل (إذا وجد، وإلا null)\",
    \"items\": [
        {
            \"name\": \"اسم الدواء أو المنتج\",
            \"quantity\": 0, (عدد الأشرطة أو العلب كعدد صحيح),
            \"cost_price\": 0 (سعر التكلفة المفرد بالدينار العراقي كعدد صحيح)
        }
    ]
}";

            // 3. إرسال الطلب إلى Gemini API
$endpoint = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";                   $response = Http::post($endpoint, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                            [
                                'inline_data' => [
                                    'mime_type' => $mimeType,
                                    'data' => $imageData
                                ]
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'response_mime_type' => 'application/json', // إجبار Gemini على إرجاع JSON صريح
                    'temperature' => 0.1, // تقليل التخمين لزيادة الدقة
                ]
            ]);

          if ($response->failed()) {
    $errorBody = $response->json();
    $errorMessage = $errorBody['error']['message'] ?? $response->body();
    
    return response()->json([
        'error' => 'خطأ من Gemini: ' . $errorMessage
    ], 500);
}

            // 4. معالجة النتيجة وإرجاعها للواجهة
            $result = $response->json();
            $jsonText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
            
            $parsedData = json_decode($jsonText, true);

            return response()->json([
                'success' => true,
                'data' => $parsedData
            ]);

        } catch (\Exception $e) {
            Log::error('Receipt Scan Error: ' . $e->getMessage());
            return response()->json(['error' => 'تعذر تحليل الوصل: ' . $e->getMessage()], 500);
        }
    }
}
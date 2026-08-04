@extends('layouts.app')

@section('content')
    <!-- هنا يوضع جدول المنتجات والمحتوى الخاص بهذه الصفحة فقط -->
    <div class="max-w-7xl mx-auto">
        <!-- الجدول وأزرار الإضافة التي كانت لديك -->
    </div>
    <script>
    // إرجاع تنسيق الفوارز للمبالغ أثناء الكتابة
    document.querySelectorAll('input[name="total_amount"], input[name="paid_amount"]').forEach(input => {
        input.addEventListener('input', function(e) {
            let value = this.value.replace(/,/g, '');
            if (!isNaN(value) && value.length > 0) {
                this.dataset.realValue = value;
            }
        });
    });
</script>
@endsection
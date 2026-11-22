@extends('layouts.storefront')
{{-- تأكيد إنشاء الطلب ويعرض الرقم والملخص دون وصف الدفع بأنه مؤكد إذا لم تتم المراجعة. --}}

@section('title', 'تم إرسال الطلب | بصمة الحرفيين')

@section('content')
<section class="shell confirmation-page"><div class="confirmation-mark">✓</div><span class="eyebrow">وصل طلبك إلى المتجر</span><h1>شكرًا لاختيارك<br><em>صُنّاع اليمن.</em></h1><p>رقم طلبك <strong>{{ $order['reference'] }}</strong>. الحالة الحالية: {{ $order['status'] }}.</p><div class="confirmation-summary"><div><span>المنتجات</span><strong>{{ count($order['items']) }} منتجات</strong></div><div><span>قيمة المنتجات</span><strong>{{ number_format($order['subtotal']) }} ر.ي</strong></div><div><span>التوصيل</span><strong>يؤكده المتجر</strong></div><div><span>طريقة الدفع</span><strong>{{ $order['payment_method'] }}</strong></div></div><div class="confirmation-actions"><a class="button button-dark" href="{{ route('account.orders.show', $order['reference']) }}">متابعة الطلب <span>←</span></a><a class="quiet-link" href="{{ route('products.index') }}">مواصلة التسوق</a></div></section>
@endsection
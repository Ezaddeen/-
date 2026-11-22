@extends('layouts.storefront')
{{-- يعرض طلبات المتجر المرتبطة بعناصر البائع الحالي فقط. --}}

@section('title', 'طلبات المتجر | بصمة الحرفيين')

@section('content')
<section class="seller-heading shell"><div><span class="eyebrow">متابعة المبيعات</span><h1>طلبات <em>المتجر</em></h1><p>راجع المنتجات والعناوين وحدّث حالة التجهيز والتوصيل.</p></div></section>
<section class="seller-layout shell"><aside>@include('components.seller-nav')</aside><div class="seller-main">
    @if (count($orders))<div class="seller-order-list">@foreach ($orders as $order)<a class="seller-order-row" href="{{ route('seller.orders.show', $order['reference']) }}"><span class="seller-order-avatar">{{ mb_substr($order['address']['label'], 0, 1) }}</span><span class="seller-order-copy"><strong>{{ $order['reference'] }}</strong><small>{{ $order['address']['governorate'] }} · {{ $order['address']['city'] }} · {{ count($order['items']) }} منتجات</small></span><span class="order-status">{{ $order['status'] }}</span><strong>{{ number_format($order['total']) }} ر.ي</strong><b>←</b></a>@endforeach</div>@else<div class="seller-empty seller-empty-large"><span>↻</span><h2>لا توجد طلبات مرتبطة بمتجرك</h2><p>ستظهر الطلبات هنا بعد اختيار منتجات متجرك.</p></div>@endif
</div></section>
@endsection
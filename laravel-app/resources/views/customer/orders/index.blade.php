@extends('layouts.storefront')
{{-- يعرض سجل طلبات العميل مرتبا بالأحدث مع الرابط إلى التفاصيل. --}}

@section('title', 'طلباتي | بصمة الحرفيين')

@section('content')
<section class="account-heading shell"><div><span class="eyebrow">مشترياتك</span><h1>سجل <em>الطلبات</em></h1><p>تابع حالة طلباتك وتفاصيلها.</p></div><a class="button button-outline" href="{{ route('products.index') }}">اكتشف المنتجات <span>←</span></a></section>
<section class="account-layout shell"><aside>@include('components.customer-nav')</aside><div class="account-main">
    @if (count($orders))<div class="order-list">@foreach ($orders as $order)<article class="order-card"><div class="order-card-top"><div><small>رقم الطلب</small><strong>{{ $order['reference'] }}</strong></div><span class="order-status">{{ $order['status'] }}</span></div><div class="order-card-bottom"><span>{{ $order['created_at'] }}</span><span>{{ count($order['items']) }} منتجات</span><strong>{{ number_format($order['total']) }} ر.ي</strong><a class="quiet-link" href="{{ route('account.orders.show', $order['reference']) }}">التفاصيل <span>←</span></a></div></article>@endforeach</div>
    @else<div class="empty-state"><span>↻</span><h2>لا توجد طلبات حتى الآن</h2><p>ستظهر طلباتك هنا بعد إتمامها.</p><a class="button button-dark" href="{{ route('products.index') }}">تصفّح المنتجات <span>←</span></a></div>@endif
</div></section>
@endsection
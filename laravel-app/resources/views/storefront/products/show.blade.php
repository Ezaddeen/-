@extends('layouts.storefront')
{{-- يعرض بيانات منتج واحد والصور والسعر والمخزون والحرفي وإضافة للسلة. --}}

@section('title', $product['name'].' | بصمة الحرفيين')

@section('content')
<div class="shell breadcrumbs"><a href="{{ route('home') }}">الرئيسية</a><span>/</span><a href="{{ route('products.index') }}">المنتجات</a><span>/</span><span>{{ $product['name'] }}</span></div>
<section class="shell product-detail">
    <div class="detail-image"><img src="{{ $product['image'] }}" alt="{{ $product['name'] }}"><span class="made-local">صورة توضيحية</span></div>
    <div class="detail-copy">
        <span class="eyebrow">{{ $product['category'] }} · {{ $product['city'] }}</span>
        <h1>{{ $product['name'] }}</h1>
        <div class="detail-rating"><span>★ {{ $product['rating'] }}</span><span>تقييمات المشترين</span><i></i><span>{{ $product['stock'] }} قطع متوفرة</span></div>
        <strong class="detail-price">{{ number_format($product['price']) }} <small>ريال يمني</small></strong>
        <p class="detail-description">{{ $product['description'] }}</p>
        <div class="detail-maker"><span class="maker-monogram">{{ mb_substr($product['artisan'], 0, 1) }}</span><div><small>من متجر</small><a href="{{ route('artisans.show', $product['artisan_slug']) }}">{{ $product['artisan'] }} · {{ $product['city'] }}</a></div><a class="maker-arrow" href="{{ route('artisans.show', $product['artisan_slug']) }}" aria-label="زيارة صفحة المتجر">←</a></div>
        <form class="add-cart-form" method="POST" action="{{ route('cart.add', $product['slug']) }}">@csrf<label for="quantity">الكمية</label><select id="quantity" name="quantity">@for ($quantity = 1; $quantity <= min(5, $product['stock']); $quantity++)<option value="{{ $quantity }}">{{ $quantity }}</option>@endfor</select><button class="button button-dark" type="submit">أضف إلى السلة <span>＋</span></button></form>
        <div class="local-delivery"><span>⌖</span><p>تختلف رسوم التوصيل حسب المنطقة، وتظهر في ملخص الطلب.</p></div>
        <div class="product-story"><span class="eyebrow">تفاصيل المنتج</span><p>{{ $product['story'] }}</p></div>
    </div>
</section>
@endsection
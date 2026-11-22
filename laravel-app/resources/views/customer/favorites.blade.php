@extends('layouts.storefront')
{{-- يعرض المنتجات المحفوظة في مفضلة المستخدم وحالة القائمة الفارغة. --}}

@section('title', 'المفضلة | بصمة الحرفيين')

@section('content')
<section class="account-heading shell"><div><span class="eyebrow">قطع لفتت انتباهك</span><h1>قائمة <em>المفضلة</em></h1><p>احتفظ بالمنتجات التي ترغب في الرجوع إليها.</p></div><a class="button button-outline" href="{{ route('products.index') }}">تصفّح المنتجات <span>←</span></a></section>
<section class="shell favorites-page"><div class="product-grid favorites-grid">@foreach ($products as $product)<div class="favorite-page-item" data-favorite-card="{{ $product['slug'] }}">@include('components.product-card', ['product' => $product])</div>@endforeach</div><div class="empty-state favorites-empty" hidden><span>♡</span><h2>لم تحفظ منتجات بعد</h2><p>اضغط رمز القلب على أي منتج لإضافته إلى هذه القائمة.</p><a class="button button-dark" href="{{ route('products.index') }}">اكتشف المنتجات <span>←</span></a></div></section>
@endsection
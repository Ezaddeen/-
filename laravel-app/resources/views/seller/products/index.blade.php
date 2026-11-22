@extends('layouts.storefront')
{{-- يعرض قائمة منتجات البائع ونشرها ومخزونها وأزرار الإضافة والتعديل. --}}

@section('title', 'منتجات متجري | بصمة الحرفيين')

@section('content')
<section class="seller-heading shell"><div><span class="eyebrow">إدارة المتجر</span><h1>منتجات <em>متجري</em></h1><p>حدّث تفاصيل المنتجات والكمية المتاحة.</p></div><a class="button button-dark" href="{{ route('seller.products.create') }}">＋ إضافة منتج</a></section>
<section class="seller-layout shell"><aside>@include('components.seller-nav')</aside><div class="seller-main"><div class="seller-table-head"><span>{{ count($products) }} منتجات</span><a class="quiet-link" href="{{ route('seller.profile') }}">عرض صفحة المتجر <span>↗</span></a></div>
    @if (count($products))<div class="seller-product-table"><div class="seller-table-labels"><span>المنتج</span><span>السعر</span><span>الكمية</span><span>التصنيف</span><span>الإجراء</span></div>@foreach ($products as $product)<article class="seller-product-row"><div class="seller-product-name"><img src="{{ $product['image'] }}" alt="{{ $product['name'] }}"><span><strong>{{ $product['name'] }}</strong><small>{{ $product['city'] }}</small></span></div><strong>{{ number_format($product['price']) }} <small>ر.ي</small></strong><span class="stock-count {{ $product['stock'] < 4 ? 'stock-low' : '' }}">{{ $product['stock'] }} متاح</span><span class="seller-category">{{ $product['category'] }}</span><a class="edit-link" href="{{ route('seller.products.edit', $product['slug']) }}">تعديل <span>←</span></a></article>@endforeach</div>@else<div class="empty-state"><span>◇</span><h2>أضف أول منتجات متجرك</h2><p>أدخل صورة المنتج ووصفه وسعره لعرضه في السوق.</p><a class="button button-dark" href="{{ route('seller.products.create') }}">إضافة منتج</a></div>@endif
    </div></section>
@endsection
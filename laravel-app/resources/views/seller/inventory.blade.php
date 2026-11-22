@extends('layouts.storefront')
{{-- يعرض مخزون البائع الحالي والمنتجات المنخفضة وحالة التوفر. --}}

@section('title', 'مخزون المتجر | بصمة الحرفيين')

@section('content')
<section class="seller-heading shell"><div><span class="eyebrow">إدارة المنتجات</span><h1>مخزون <em>المتجر</em></h1><p>تابع الكميات المتاحة وحدد المنتجات التي تحتاج إلى إعادة تجهيز.</p></div><a class="button button-dark" href="{{ route('seller.products.create') }}">＋ إضافة منتج</a></section>
<section class="seller-layout shell"><aside>@include('components.seller-nav')</aside><div class="seller-main">
    <div class="seller-metrics seller-inventory-metrics">
        <div><span>إجمالي المنتجات</span><strong>{{ count($products) }}</strong><small>ضمن قائمة متجرك</small></div>
        <div><span>إجمالي القطع المتاحة</span><strong>{{ number_format($totalStock) }}</strong><small>حسب الكميات المسجلة</small></div>
        <div><span>منخفضة المخزون</span><strong>{{ count($lowStockProducts) }}</strong><small>3 قطع أو أقل</small></div>
    </div>
    <section class="seller-panel seller-inventory-panel">
        <div class="seller-section-head"><div><span class="eyebrow">حالة المنتجات</span><h2>الكميات الحالية</h2></div><span class="seller-muted-note">للتعديل، افتح بطاقة المنتج</span></div>
        @if (count($products))
            <div class="seller-inventory-table">
                <div class="seller-inventory-labels"><span>المنتج</span><span>التصنيف</span><span>السعر</span><span>المتاح</span><span>الحالة</span><span></span></div>
                @foreach ($products as $product)
                    @php($isLowStock = $product['stock'] <= 3)
                    <article class="seller-inventory-row">
                        <a class="seller-product-name" href="{{ route('seller.products.edit', $product['slug']) }}"><img src="{{ $product['image'] }}" alt="{{ $product['name'] }}"><span><strong>{{ $product['name'] }}</strong><small>{{ $product['city'] }}</small></span></a>
                        <span class="seller-category">{{ $product['category'] }}</span><strong>{{ number_format($product['price']) }} <small>ر.ي</small></strong><strong class="seller-stock-number {{ $isLowStock ? 'is-low' : '' }}">{{ number_format($product['stock']) }} <small>قطعة</small></strong>
                        <span class="seller-stock-badge {{ $product['stock'] === 0 ? 'is-empty' : ($isLowStock ? 'is-low' : 'is-ready') }}">{{ $product['stock'] === 0 ? 'نفد' : ($isLowStock ? 'كمية منخفضة' : 'متوفر') }}</span><a class="edit-link" href="{{ route('seller.products.edit', $product['slug']) }}" aria-label="تعديل {{ $product['name'] }}">تعديل <span>←</span></a>
                    </article>
                @endforeach
            </div>
        @else
            <div class="seller-empty seller-empty-large"><span>▤</span><h2>لا توجد منتجات في المخزون</h2><p>أضف منتجًا لتبدأ متابعة الكميات المتاحة.</p><a class="button button-dark" href="{{ route('seller.products.create') }}">إضافة منتج</a></div>
        @endif
    </section>
    <p class="seller-demo-note">تُعرض الكميات من بيانات المتجر الحالية، ولا توجد حركات جرد أو مزامنة خارجية في هذه المرحلة.</p>
</div></section>
@endsection
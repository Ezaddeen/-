@extends('layouts.storefront')
{{-- يعرض سلة الجلسة والكميات والإجمالي وخيارات المتابعة والحذف. --}}

@section('title', 'سلة المشتريات | بصمة الحرفيين')

@section('content')
<section class="page-intro shell"><span class="eyebrow">خطوة باتجاه الحكاية</span><h1>سلة <em>مشترياتك</em></h1><p>راجع اختياراتك والكميات قبل المتابعة.</p></section>
<section class="shell cart-layout">
    @if (count($items))
        <div class="cart-items">@foreach ($items as $item)<article class="cart-item"><a class="cart-item-image" href="{{ route('products.show', $item['slug']) }}"><img src="{{ $item['image'] }}" alt="{{ $item['name'] }}"></a><div class="cart-item-copy"><span>{{ $item['artisan'] }} · {{ $item['city'] }}</span><h2><a href="{{ route('products.show', $item['slug']) }}">{{ $item['name'] }}</a></h2><form class="cart-quantity-form" method="POST" action="{{ route('cart.quantity', $item['slug']) }}">@csrf @method('PUT')<label>الكمية <input name="quantity" type="number" min="1" max="{{ $item['stock'] }}" value="{{ $item['quantity'] }}" required></label><button class="quiet-link" type="submit">تحديث</button></form></div><strong>{{ number_format($item['price'] * $item['quantity']) }} <small>ر.ي</small></strong><form method="POST" action="{{ route('cart.remove', $item['slug']) }}">@csrf @method('DELETE')<button class="remove-item" aria-label="حذف {{ $item['name'] }}">×</button></form></article>@endforeach</div>
        <aside class="cart-summary"><span class="eyebrow">ملخص السلة</span><div><span>قيمة المنتجات</span><strong>{{ number_format($total) }} ر.ي</strong></div><div><span>التوصيل</span><span>حسب المنطقة</span></div><hr><div class="summary-total"><span>قيمة المنتجات</span><strong>{{ number_format($total) }} ر.ي</strong></div><a class="button button-dark" href="{{ route('checkout') }}">متابعة إلى إتمام الطلب <span>←</span></a><a class="quiet-link" href="{{ route('products.index') }}">متابعة التسوق <span>←</span></a></aside>
    @else
        <div class="empty-state cart-empty"><span>سلتك</span><h2>ما زالت السلة فارغة</h2><p>ابدأ باكتشاف قطع مصنوعة يدويًا من حرفيي اليمن.</p><a class="button button-dark" href="{{ route('products.index') }}">اكتشف المنتجات <span>←</span></a></div>
    @endif
</section>
@endsection
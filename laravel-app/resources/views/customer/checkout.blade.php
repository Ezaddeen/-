@extends('layouts.storefront')
{{-- يعرض عنوان التوصيل وطريقة الدفع والملاحظة وإجمالي الطلب وملخصه. --}}

@section('title', 'إتمام الطلب | بصمة الحرفيين')

@section('content')
<section class="page-intro shell"><span class="eyebrow">خطوة أخيرة</span><h1>إتمام <em>الطلب</em></h1><p>راجع عنوانك وطريقة الدفع قبل إرسال الطلب للمتجر.</p></section>
<section class="shell checkout-layout">
    <form class="checkout-form" action="{{ route('checkout.store') }}" method="POST">@csrf
        @if ($errors->has('cart'))<div class="notice" role="alert">{{ $errors->first('cart') }}</div>@endif
        <section class="checkout-block"><div class="checkout-block-heading"><span>01</span><div><h2>عنوان التوصيل</h2><p>اختر المكان الأنسب لوصول طلبك.</p></div><a class="quiet-link" href="{{ route('account.addresses') }}">إدارة العناوين</a></div>
            @error('address_id')<p class="field-error">{{ $message }}</p>@enderror
            <div class="checkout-addresses">@foreach ($addresses as $address)<label class="checkout-address"><input type="radio" name="address_id" value="{{ $address['id'] }}" @checked(old('address_id', collect($addresses)->firstWhere('is_default', true)['id'] ?? $addresses[0]['id'] ?? '') === $address['id']) required><span class="radio-mark"></span><span class="checkout-address-copy"><strong>{{ $address['label'] }} @if ($address['is_default'])<small>افتراضي</small>@endif</strong><span>{{ $address['governorate'] }} · {{ $address['city'] }} · {{ $address['district'] }}</span><span>أقرب معلم: {{ $address['landmark'] }}</span><span>{{ $address['phone'] }}</span></span></label>@endforeach</div>
            <a class="add-address-link" href="{{ route('account.addresses') }}">＋ إضافة عنوان جديد</a>
        </section>
        <section class="checkout-block"><div class="checkout-block-heading"><span>02</span><div><h2>طريقة الدفع</h2><p>اختر الطريقة التي تناسبك.</p></div></div>
            @error('payment_method')<p class="field-error">{{ $message }}</p>@enderror
            <div class="payment-options"><label class="payment-option"><input type="radio" name="payment_method" value="cash_on_delivery" @checked(old('payment_method', 'cash_on_delivery') === 'cash_on_delivery') required><span class="radio-mark"></span><span><strong>الدفع عند الاستلام</strong><small>ادفع عند وصول طلبك واستلامه.</small></span><b>ر.ي</b></label><label class="payment-option"><input type="radio" name="payment_method" value="contact_seller" @checked(old('payment_method') === 'contact_seller')><span class="radio-mark"></span><span><strong>التنسيق مع المتجر</strong><small>يتواصل معك المتجر لترتيب طريقة الدفع.</small></span><b>↔</b></label></div>
        </section>
        <section class="checkout-block"><div class="checkout-block-heading"><span>03</span><div><h2>ملاحظة للمتجر</h2><p>أضف تفاصيل تساعد على تجهيز طلبك.</p></div></div><textarea class="checkout-note" name="customer_note" rows="3" maxlength="300" placeholder="ملاحظة اختيارية">{{ old('customer_note') }}</textarea></section>
        <button class="button button-dark checkout-submit" type="submit">إرسال الطلب <span>←</span></button>
    </form>
    <aside class="checkout-summary"><span class="eyebrow">محتويات الطلب</span><div class="checkout-summary-items">@foreach ($items as $item)<div class="checkout-summary-item"><img src="{{ $item['image'] }}" alt="{{ $item['name'] }}"><div><strong>{{ $item['name'] }}</strong><small>{{ $item['quantity'] }} × {{ number_format($item['price']) }} ر.ي</small></div><b>{{ number_format($item['quantity'] * $item['price']) }} ر.ي</b></div>@endforeach</div><div class="checkout-total-line"><span>قيمة المنتجات</span><strong>{{ number_format($subtotal) }} ر.ي</strong></div><div class="checkout-total-line"><span>رسوم التوصيل التقديرية</span><strong>{{ number_format($deliveryFee) }} ر.ي</strong></div><div class="checkout-total-line checkout-total-final"><span>الإجمالي التقديري</span><strong>{{ number_format($subtotal + $deliveryFee) }} ر.ي</strong></div><p class="checkout-footnote">تحتسب رسوم التوصيل حسب المحافظة المحددة وتثبت نهائيًا مع إنشاء الطلب.</p></aside>
</section>
@endsection
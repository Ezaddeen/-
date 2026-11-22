@extends('layouts.storefront')
{{-- يعرض تقرير المبيعات والطلبات والمنتجات خلال الفترة المختارة للبائع. --}}

@section('title', 'تقارير المتجر | بصمة الحرفيين')

@section('content')
<section class="seller-heading shell"><div><span class="eyebrow">قراءة أداء المتجر</span><h1>تقارير <em>المتجر</em></h1><p>راجع الطلبات المكتملة والمنتجات التي حققت مبيعات ضمن الفترة المحددة.</p></div></section>
<section class="seller-layout shell"><aside>@include('components.seller-nav')</aside><div class="seller-main">
    <form class="seller-report-filter" method="GET" action="{{ route('seller.reports') }}">
        <div><label for="from">من تاريخ</label><input id="from" name="from" type="date" value="{{ $filters['from'] ?? '' }}"></div>
        <div><label for="to">إلى تاريخ</label><input id="to" name="to" type="date" value="{{ $filters['to'] ?? '' }}"></div>
        <button class="button button-dark" type="submit">تطبيق الفترة</button>
        @if ($filters !== [])<a class="quiet-link" href="{{ route('seller.reports') }}">مسح التصفية</a>@endif
    </form>
    <div class="seller-metrics seller-report-metrics">
        <div><span>المبيعات المكتملة</span><strong>{{ number_format($sales) }} <small>ر.ي</small></strong><small>من الطلبات المسلّمة</small></div>
        <div><span>الطلبات المكتملة</span><strong>{{ count($completedOrders) }}</strong><small>ضمن الفترة المحددة</small></div>
        <div><span>متوسط الطلب المكتمل</span><strong>{{ number_format($averageOrder) }} <small>ر.ي</small></strong><small>قيمة تقريبية لكل طلب</small></div>
    </div>
    <section class="seller-panel seller-report-panel">
        <div class="seller-section-head"><div><span class="eyebrow">أداء المنتجات</span><h2>الأكثر مبيعًا</h2></div><a class="quiet-link" href="{{ route('seller.products.index') }}">كل المنتجات <span>←</span></a></div>
        @if (count($bestProducts))
            <div class="seller-report-table"><div class="seller-report-labels"><span>المنتج</span><span>الكمية المباعة</span><span>قيمة المبيعات</span></div>@foreach ($bestProducts as $product)<div class="seller-report-row"><strong>{{ $product['name'] }}</strong><span>{{ number_format($product['quantity']) }} قطعة</span><strong>{{ number_format($product['sales']) }} <small>ر.ي</small></strong></div>@endforeach</div>
        @else
            <div class="seller-empty seller-empty-large"><span>▥</span><h2>لا توجد مبيعات مكتملة في هذه الفترة</h2><p>ستظهر المنتجات هنا بعد تسليم الطلبات المرتبطة بمتجرك.</p></div>
        @endif
    </section>
    <section class="seller-panel seller-report-panel">
        <div class="seller-section-head"><div><span class="eyebrow">متابعة الطلبات</span><h2>ملخص الحالات</h2></div><a class="quiet-link" href="{{ route('seller.orders.index') }}">الطلبات <span>←</span></a></div>
        @if (count($orders))
            <div class="seller-report-statuses">@foreach (array_count_values(array_column($orders, 'status')) as $status => $count)<a href="{{ route('seller.orders.index') }}"><span>{{ $status }}</span><strong>{{ $count }}</strong></a>@endforeach</div>
        @else
            <div class="seller-empty seller-empty-compact"><span>↻</span><strong>لا توجد طلبات ضمن الفترة المحددة</strong></div>
        @endif
    </section>
    <p class="seller-demo-note">تعتمد هذه المؤشرات على الطلبات الموجودة في جلسة المتصفح؛ لا تمثل سجلًا ماليًا دائمًا أو محاسبة نهائية.</p>
</div></section>
@endsection
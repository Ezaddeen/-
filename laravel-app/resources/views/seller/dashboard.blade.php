@extends('layouts.storefront')
{{-- لوحة البائع تعرض الطلبات والمبيعات والمخزون والتنبيهات ومبيعات الأسبوع. --}}

@section('title', 'إدارة المتجر | بصمة الحرفيين')

@section('content')
<section class="seller-heading shell">
    <div><span class="eyebrow">مساحة أصحاب المشاريع</span><h1>أهلًا بكم في <em>{{ $profile['name'] }}</em></h1><p>{{ $profile['city'] }} · {{ $profile['craft'] }}</p></div>
    <a class="button button-dark" href="{{ route('seller.products.create') }}">＋ إضافة منتج</a>
</section>
<section class="seller-layout shell">
    <aside>@include('components.seller-nav')</aside>
    <div class="seller-main">
        <div class="seller-metrics seller-dashboard-metrics">
            <a href="{{ route('seller.products.index') }}"><span>المنتجات المعروضة</span><strong>{{ count($products) }}</strong><small>إدارة منتجاتك <b>←</b></small></a>
            <a href="{{ route('seller.orders.index') }}"><span>طلبات تحتاج متابعة</span><strong>{{ ($orderStatusCounts['بانتظار قبول البائع'] ?? 0) + ($orderStatusCounts['قيد التجهيز'] ?? 0) }}</strong><small>افتح قائمة الطلبات <b>←</b></small></a>
            <a href="{{ route('seller.inventory') }}"><span>منتجات منخفضة المخزون</span><strong>{{ count($lowStockProducts) }}</strong><small>مراجعة المخزون <b>←</b></small></a>
            <div><span>المبيعات المكتملة</span><strong>{{ number_format($sales) }} <small>ر.ي</small></strong><small>من الطلبات المسلّمة</small></div>
        </div>

        <div class="seller-dashboard-grid">
            <section class="seller-panel seller-sales-panel">
                <div class="seller-section-head"><div><span class="eyebrow">أداء المتجر</span><h2>المبيعات خلال آخر 7 أيام</h2></div><a class="quiet-link" href="{{ route('seller.reports') }}">كل التقارير <span>←</span></a></div>
                @php($maxDailySales = max(array_column($dailySales, 'sales') ?: [0]))
                <div class="seller-chart" role="img" aria-label="مخطط المبيعات خلال آخر سبعة أيام">
                    @foreach ($dailySales as $day)
                        <div class="seller-chart-column"><strong>{{ $day['sales'] > 0 ? number_format($day['sales']) : '' }}</strong><span class="seller-chart-track"><i style="height: {{ $maxDailySales > 0 ? max(5, (int) round($day['sales'] / $maxDailySales * 100)) : 5 }}%"></i></span><small>{{ $day['label'] }}</small></div>
                    @endforeach
                </div>
                @if ($sales === 0)<p class="seller-chart-note">ستظهر المبيعات هنا بعد تسليم الطلبات وتسجيلها في هذه الجلسة.</p>@endif
            </section>
            <section class="seller-panel seller-shortcuts-panel">
                <div class="seller-section-head"><div><span class="eyebrow">إدارة سريعة</span><h2>اختصارات المتجر</h2></div></div>
                <a class="seller-shortcut" href="{{ route('seller.products.create') }}"><span>＋</span><span><strong>إضافة منتج</strong><small>اعرض قطعة جديدة في السوق</small></span><b>←</b></a>
                <a class="seller-shortcut" href="{{ route('seller.inventory') }}"><span>▤</span><span><strong>مراجعة المخزون</strong><small>{{ count($lowStockProducts) }} منتجات منخفضة الكمية</small></span><b>←</b></a>
                <a class="seller-shortcut" href="{{ route('seller.profile') }}"><span>◉</span><span><strong>تحديث ملف المشروع</strong><small>الحرفة والمنطقة وبيانات التواصل</small></span><b>←</b></a>
            </section>
        </div>

        <section class="seller-panel seller-activity-panel">
            <div class="seller-section-head"><div><span class="eyebrow">آخر النشاط</span><h2>الطلبات الأخيرة</h2></div><a class="quiet-link" href="{{ route('seller.orders.index') }}">كل الطلبات <span>←</span></a></div>
            @if (count($orders))
                <div class="seller-order-list">@foreach (array_slice(array_values($orders), 0, 5) as $order)<a class="seller-order-row" href="{{ route('seller.orders.show', $order['reference']) }}"><span class="seller-order-avatar">{{ mb_substr($order['address']['label'], 0, 1) }}</span><span class="seller-order-copy"><strong>{{ $order['reference'] }}</strong><small>{{ $order['address']['city'] }} · {{ count($order['items']) }} منتجات</small></span><span class="order-status">{{ $order['status'] }}</span><strong class="seller-order-total">{{ number_format($order['total']) }} ر.ي</strong><b>←</b></a>@endforeach</div>
            @else
                <div class="seller-empty"><span>↻</span><strong>ستظهر طلبات متجرك هنا</strong><small>تابع الطلبات الجديدة من هذه الصفحة.</small></div>
            @endif
        </section>

        <section class="seller-panel seller-stock-panel">
            <div class="seller-section-head"><div><span class="eyebrow">تنبيهات الكمية</span><h2>منتجات تحتاج مراجعة</h2></div><a class="quiet-link" href="{{ route('seller.inventory') }}">إدارة المخزون <span>←</span></a></div>
            @if (count($lowStockProducts))
                <div class="seller-low-stock-list">@foreach (array_slice($lowStockProducts, 0, 4) as $product)<a href="{{ route('seller.products.edit', $product['slug']) }}"><span>{{ $product['name'] }}</span><strong>{{ $product['stock'] }} متاح</strong><b>←</b></a>@endforeach</div>
            @else
                <div class="seller-empty seller-empty-compact"><span>✓</span><strong>لا توجد تنبيهات مخزون حاليًا</strong><small>ستظهر هنا المنتجات التي وصلت إلى 3 قطع أو أقل.</small></div>
            @endif
        </section>
    </div>
</section>
@endsection
<nav class="seller-nav" aria-label="إدارة المتجر">
    <a class="{{ request()->routeIs('seller.dashboard') ? 'active' : '' }}" href="{{ route('seller.dashboard') }}"><span>▦</span> نظرة عامة</a>
    <a class="{{ request()->routeIs('seller.products.*') ? 'active' : '' }}" href="{{ route('seller.products.index') }}"><span>◇</span> منتجاتي</a>
    <a class="{{ request()->routeIs('seller.orders.*') ? 'active' : '' }}" href="{{ route('seller.orders.index') }}"><span>↻</span> الطلبات</a>
    <a class="{{ request()->routeIs('seller.inventory') ? 'active' : '' }}" href="{{ route('seller.inventory') }}"><span>▤</span> المخزون</a>
    <a class="{{ request()->routeIs('seller.reports') ? 'active' : '' }}" href="{{ route('seller.reports') }}"><span>▥</span> التقارير</a>
    <a class="{{ request()->routeIs('seller.profile') ? 'active' : '' }}" href="{{ route('seller.profile') }}"><span>◉</span> ملف المشروع</a>
    <a href="{{ route('home') }}"><span>←</span> العودة إلى السوق</a>
</nav>
{{-- تنقل جانبي مشترك لمساحة البائع والمنتجات والمخزون والطلبات والتقارير والملف. --}}
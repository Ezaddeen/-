<nav class="account-nav" aria-label="قائمة حسابي">
    <a class="{{ request()->routeIs('account.dashboard') ? 'active' : '' }}" href="{{ route('account.dashboard') }}"><span>⌂</span> نظرة عامة</a>
    <a class="{{ request()->routeIs('account.profile') ? 'active' : '' }}" href="{{ route('account.profile') }}"><span>◉</span> بياناتي</a>
    <a class="{{ request()->routeIs('account.addresses') ? 'active' : '' }}" href="{{ route('account.addresses') }}"><span>⌖</span> عناويني</a>
    <a class="{{ request()->routeIs('account.favorites') ? 'active' : '' }}" href="{{ route('account.favorites') }}"><span>♡</span> المفضلة</a>
    <a class="{{ request()->routeIs('account.orders*') ? 'active' : '' }}" href="{{ route('account.orders') }}"><span>↻</span> طلباتي</a>
    <a href="{{ route('cart.show') }}"><span>▤</span> سلة المشتريات</a>
    <a href="{{ route('products.index') }}"><span>←</span> متابعة التسوق</a>
</nav>
{{-- تنقل جانبي مشترك لصفحات حساب العميل ونظرة عامةه ومفضلةه وعناوينه وطلباته. --}}
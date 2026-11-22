<!DOCTYPE html>
{{-- قالب الواجهة العامة الموحد ويضم الشريط العلوي والبحث والتنقل والسلة والتذييل. --}}
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#183c32">
    <title>@yield('title', 'بصمة الحرفيين | حرف ومنتجات يمنية')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/brand-emblem.svg') }}">
    <meta name="description" content="اكتشف منتجات الحرفيين والأسر المنتجة من مختلف مناطق اليمن.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Readex+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/interactions.css') }}">
    <link rel="stylesheet" href="{{ asset('css/account.css') }}">
    <link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/seller.css') }}">
    <link rel="stylesheet" href="{{ asset('css/seller-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/seller-application.css') }}">
    <link rel="stylesheet" href="{{ asset('css/brand.css') }}">
</head>
<body>
    <header class="site-header">
        <div class="header-main shell">
            <a class="brand" href="{{ route('home') }}" aria-label="بصمة الحرفيين، الصفحة الرئيسية">
                <img class="brand-mark" src="{{ asset('images/brand-emblem.svg') }}" alt="">
                <span class="brand-copy"><strong>بصمة الحرفيين</strong><small>حرف ومنتجات يمنية</small></span>
            </a>
            <form class="header-search" action="{{ route('products.index') }}" method="GET" role="search">
                <span class="search-symbol" aria-hidden="true">⌕</span>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="ابحث عن قطعة أو حرفة أو منطقة" aria-label="ابحث في المنتجات">
                <button type="submit">بحث</button>
            </form>
            <div class="header-actions">
                @auth
                    <a class="text-action" href="{{ route('account.dashboard') }}">حسابي</a>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="text-action" type="submit">خروج</button></form>
                @else
                    <a class="text-action" href="{{ route('login') }}">دخول</a>
                @endauth
                <a class="icon-action cart-link" href="{{ route('cart.show') }}" aria-label="السلة">السلة <span>{{ array_sum(session('cart_items', [])) }}</span></a>
            </div>
        </div>
        <nav class="main-nav" aria-label="التنقل الرئيسي">
            <div class="shell nav-inner">
                <a href="{{ route('products.index') }}">كل المنتجات</a>
                <a href="{{ route('products.index', ['category' => 'فخار وخزف']) }}">فخار وخزف</a>
                <a href="{{ route('products.index', ['category' => 'نسيج وتطريز']) }}">نسيج وتطريز</a>
                <a href="{{ route('products.index', ['category' => 'فضيات وحلي']) }}">فضيات وحلي</a>
                <a href="{{ route('products.index', ['category' => 'بن وعسل']) }}">بن وعسل</a>
                @auth
                    @if (auth()->user()->is_admin)
                        <a class="nav-seller-entry" href="{{ url('/admin') }}">لوحة المدير</a>
                    @elseif (auth()->user()->is_seller)
                        <a class="nav-seller-entry" href="{{ route('seller.dashboard') }}">مساحة البائع</a>
                    @else
                        <a class="nav-seller-entry" href="{{ route('seller-application.show') }}">ابدأ البيع</a>
                    @endif
                @else
                    <a class="nav-seller-entry" href="{{ route('seller-application.show') }}">ابدأ البيع</a>
                @endauth
                <a class="nav-region" href="{{ route('products.index') }}">من صنعاء إلى حضرموت <span>←</span></a>
            </div>
        </nav>
    </header>

    @if (session('status'))
        <div class="shell"><div class="notice" role="status">{{ session('status') }}</div></div>
    @endif

    <main>@yield('content')</main>

    <footer class="site-footer">
        <div class="shell footer-grid">
            <div class="footer-brand"><a class="brand brand-light" href="{{ route('home') }}"><img class="brand-mark" src="{{ asset('images/brand-emblem.svg') }}" alt=""><span class="brand-copy"><strong>بصمة الحرفيين</strong><small>حرف ومنتجات يمنية</small></span></a><p>مساحة تجمع صُنّاع اليمن بعملاء يقدّرون الحرفة والحكاية خلف كل قطعة.</p></div>
            <div><h2>تسوّق</h2><a href="{{ route('products.index') }}">كل المنتجات</a><a href="{{ route('products.index', ['category' => 'فخار وخزف']) }}">فخار وخزف</a><a href="{{ route('products.index', ['category' => 'نسيج وتطريز']) }}">نسيج وتطريز</a></div>
            <div><h2>مساعدة</h2><a href="{{ route('login') }}">حسابي</a><a href="{{ route('cart.show') }}">سلة المشتريات</a><a href="mailto:hello@basmat-anamil.example">تواصل معنا</a></div>
            <div class="footer-note"><span class="footer-stamp">صُنع<br>بمحبة</span><p>من أيدي الحرفيين<br>في اليمن</p></div>
        </div>
        <div class="shell footer-bottom"><span>© {{ date('Y') }} بصمة الحرفيين</span><span>حكايات حرفية من محافظات اليمن</span></div>
    </footer>
    <script src="{{ asset('js/storefront.js') }}?v=3" defer></script>
</body>
</html>
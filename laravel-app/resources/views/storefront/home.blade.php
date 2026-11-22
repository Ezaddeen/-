@extends('layouts.storefront')
{{-- الصفحة الرئيسية تعرض الفئات والمنتجات والحرفي المختار في أقسام المتجر. --}}

@section('title', 'بصمة الحرفيين | حرف ومنتجات يمنية')

@section('content')
<section class="hero shell">
    <div class="hero-copy">
        <span class="eyebrow"><i></i> من أرض الحكايات والحِرف</span>
        <h1>كل قطعة<br>لها <em>حكاية.</em></h1>
        <p>اكتشفوا ما تصنعه أيادي اليمن؛ قطع تحمل أثر المكان ودفء أصحابها، من صنعاء القديمة إلى وديان حضرموت.</p>
        <div class="hero-actions"><a class="button button-dark" href="{{ route('products.index') }}">اكتشف المنتجات <span>←</span></a><a class="quiet-link" href="{{ route('artisans.show', $artisan['slug']) }}">تعرّف على الحرفيين <span>↖</span></a></div>
        <div class="hero-proof"><div class="proof-avatars"><span>ن</span><span>س</span><span>م</span></div><p><strong>أيدٍ يمنية تصنعها</strong><br>وكل قطعة لها صاحب حكاية</p></div>
    </div>
    <div class="hero-art" role="img" aria-label="فخار مصنوع يدويًا في ورشة حرفية يمنية">
        <img src="https://images.unsplash.com/photo-1565193566173-7a0ee3dbe261?auto=format&fit=crop&w=1500&q=90" alt="أوانٍ فخارية يدوية في ورشة حرفية">
        <div class="hero-caption"><span>حرفة من صنعاء</span><strong>الطين، حين يصبح أثرًا</strong></div>
        <div class="hero-seal"><span>من</span><strong>اليمن</strong><span>بكل محبة</span></div>
    </div>
    <div class="hero-index"><span>01</span><i></i><span>04</span></div>
</section>

<section class="category-section shell section-space">
    <div class="section-heading"><div><span class="eyebrow">اختَر حكايتك</span><h2>حِرف من <em>كل مكان</em></h2></div><a class="quiet-link" href="{{ route('products.index') }}">جميع الحرف <span>←</span></a></div>
    <div class="category-list">
        @foreach ($categories as $index => $category)
            <a class="category-item category-tone-{{ $index + 1 }}" href="{{ route('products.index', ['category' => $category['name']]) }}"><span class="category-icon">{{ $category['icon'] }}</span><strong>{{ $category['name'] }}</strong><small>{{ $category['count'] }} قطعة</small><span class="category-arrow">↙</span></a>
        @endforeach
    </div>
</section>

<section class="products-section section-space">
    <div class="shell">
        <div class="section-heading"><div><span class="eyebrow">مختارة لكم هذا الأسبوع</span><h2>قطع تستحق <em>أن تُروى</em></h2></div><a class="quiet-link" href="{{ route('products.index') }}">تصفّح السوق <span>←</span></a></div>
        <div class="product-grid">@foreach ($products as $product) @include('components.product-card', ['product' => $product]) @endforeach</div>
    </div>
</section>

<section class="artisan-feature shell section-space">
    <div class="artisan-photo"><img src="{{ $artisan['image'] }}" alt="حرفة الفخار في ورشة طين" loading="lazy"><span class="photo-tag">أيدي تصنع، وحكايات تبقى</span></div>
    <div class="artisan-story"><span class="eyebrow">وراء كل قطعة إنسان</span><h2>هنا تبدأ الحكاية<br>من <em>يدٍ تعرف.</em></h2><p>{{ $artisan['story'] }}</p><div class="artisan-location"><span>⌖</span> {{ $artisan['city'] }} · {{ $artisan['craft'] }}</div><a class="button button-outline" href="{{ route('artisans.show', $artisan['slug']) }}">ادخل عالم {{ $artisan['name'] }} <span>←</span></a></div>
</section>

<section class="regional-band"><div class="shell regional-inner"><span class="regional-mark">ي</span><div><span class="eyebrow">من محافظات اليمن</span><h2>حرفة المكان،<br><em>تصل إليك.</em></h2></div><p>اكتشف منتجات من صنعاء وإب وحضرموت وتهامة، وتعرّف على الحرفة من مصدرها.</p><a href="{{ route('products.index') }}" aria-label="تصفح المنتجات حسب المنطقة">←</a></div></section>
@endsection
@extends('layouts.storefront')
{{-- يعرض كتالوج المنتجات مع البحث والتصنيف والترتيب وحالة النتائج. --}}

@section('title', 'المنتجات | بصمة الحرفيين')

@section('content')
<section class="page-intro shell"><span class="eyebrow">سوق الحرفة اليمنية</span><h1>قطع من <em>أيدي اليمن</em></h1><p>كل منتج هنا صُنع بعناية، وكل صانع له حكاية تستحق أن تُعرف.</p></section>
<section class="shell catalog-layout">
    <aside class="catalog-filter"><h2>تصفّح حسب الحرفة</h2><a class="filter-link {{ $selectedCategory === '' ? 'active' : '' }}" href="{{ route('products.index') }}">كل المنتجات <span>{{ count($products) }}</span></a>@foreach ($categories as $category)<a class="filter-link {{ $selectedCategory === $category['name'] ? 'active' : '' }}" href="{{ route('products.index', ['category' => $category['name']]) }}">{{ $category['name'] }}</a>@endforeach<div class="filter-note"><span>⌖</span><p>نعرض منتجات من مناطق مختلفة في اليمن.</p></div></aside>
    <div class="catalog-results"><div class="catalog-toolbar"><span>{{ count($products) }} منتجات {{ $query ? 'لـ «'.$query.'»' : '' }}</span><form action="{{ route('products.index') }}" method="GET"><label for="sort">ترتيب:</label><select id="sort" name="sort" onchange="this.form.submit()"><option value="featured" @selected($sort === 'featured')>مختارة لكم</option><option value="price" @selected($sort === 'price')>السعر: الأقل أولًا</option></select>@if ($selectedCategory)<input type="hidden" name="category" value="{{ $selectedCategory }}">@endif @if ($query)<input type="hidden" name="q" value="{{ $query }}">@endif</form></div>
        @if (count($products))<div class="product-grid catalog-grid">@foreach ($products as $product) @include('components.product-card', ['product' => $product]) @endforeach</div>@else<div class="empty-state"><span>⌕</span><h2>لم نعثر على منتجات مطابقة</h2><p>جرّب كلمة بحث أخرى أو تصفح كل الحرف.</p><a class="button button-dark" href="{{ route('products.index') }}">عرض كل المنتجات</a></div>@endif
    </div>
</section>
@endsection
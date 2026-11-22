@extends('layouts.storefront')
{{-- يعرض صفحة الحرفي أو المتجر ونبذه والمنتجات المرتبطه به. --}}

@section('title', $artisan['name'].' | بصمة الحرفيين')

@section('content')
<section class="artisan-cover"><img src="{{ $artisan['image'] }}" alt="حرفة {{ $artisan['name'] }}"><div class="shell cover-overlay"><span class="eyebrow">حكايات تُصنع هنا</span><h1>{{ $artisan['name'] }}</h1><p>{{ $artisan['craft'] }} <i></i> {{ $artisan['city'] }}</p></div></section>
<section class="shell artisan-profile"><div class="profile-heading"><span class="profile-stamp">بصمة<br>يمنية</span><div><span class="eyebrow">صانع محلي</span><h2>حرفة لها <em>جذور.</em></h2></div><p>{{ $artisan['story'] }}</p></div><div class="section-heading"><div><span class="eyebrow">من إنتاج الورشة</span><h2>قطع من <em>صنع اليد</em></h2></div><span class="quiet-count">{{ count($products) }} منتجات</span></div><div class="product-grid">@foreach ($products as $product) @include('components.product-card', ['product' => $product]) @endforeach</div>@if (!count($products))<div class="empty-state compact-empty"><p>ستتوفر منتجات جديدة قريبًا.</p></div>@endif</section>
@endsection
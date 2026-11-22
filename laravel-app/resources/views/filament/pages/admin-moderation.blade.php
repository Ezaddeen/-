{{-- صفحة مراجعة المنتجات وإخفاء المحتوى المخالف أو إظهاره. --}}
<div class="admin-management-page">
@include('filament.pages.admin-management-styles')
<div class="admin-page-heading"><div><span>مراجعة المحتوى</span><h1>المنتجات والمحتوى المخالف</h1></div></div>
<div class="admin-table"><div class="admin-table-head"><span>المنتج</span><span>الحالة</span><span>الإجراء</span></div>
@forelse ($products as $product)<article class="admin-table-row"><div><strong>{{ $product['name'] }}</strong><small>{{ $product['category'] }} · {{ $product['artisan'] }}</small></div><span>{{ $product['hidden'] ? 'مخفي' : ($product['reported'] ? 'مبلغ عنه' : 'ظاهر') }}</span><button type="button" wire:click="toggleHidden({{ $product['id'] }})">{{ $product['hidden'] ? 'إظهار المنتج' : 'إخفاء المنتج' }}</button></article>@empty<p class="admin-empty">لا توجد منتجات مسجلة.</p>@endforelse</div></div>

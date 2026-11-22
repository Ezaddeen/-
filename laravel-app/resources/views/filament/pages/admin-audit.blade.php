{{-- صفحة سجل العمليات الحساسة التي نفذها مدير المنصة. --}}
<div class="admin-management-page">
@include('filament.pages.admin-management-styles')
<div class="admin-page-heading"><div><span>العمليات الحساسة</span><h1>سجل التدقيق</h1></div></div>
<div class="admin-table"><div class="admin-table-head"><span>المستخدم والعملية</span><span>التفاصيل</span><span>التاريخ</span></div>
@forelse($logs as $log)<article class="admin-table-row"><div><strong>{{ $log['user'] }}</strong><small>{{ $log['action'] }}</small></div><span>{{ $log['description'] }}</span><small>{{ $log['created_at'] }}</small></article>@empty<p class="admin-empty">لا توجد عمليات مسجلة في سجل التدقيق.</p>@endforelse</div></div>

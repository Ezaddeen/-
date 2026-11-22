{{-- صفحة حسابات المستخدمين والعملاء والبائعين والمديرين. --}}
<div class="admin-management-page">
@include('filament.pages.admin-management-styles')
<div class="admin-page-heading"><div><span>الحسابات المسجلة</span><h1>المستخدمون والعملاء</h1></div></div>
<div class="admin-table"><div class="admin-table-head admin-table-head-users"><span>المستخدم</span><span>الدور</span><span>التاريخ</span><span>الإجراء</span></div>
@foreach ($users as $user)<article class="admin-table-row admin-table-row-users"><div><strong>{{ $user['name'] }}</strong><small>{{ $user['email'] }} · {{ $user['phone'] ?: 'بدون هاتف' }}</small></div><span>{{ $user['admin'] ? 'مدير المنصة' : ($user['seller'] ? 'بائع' : 'عميل') }}</span><small>{{ $user['created_at'] }}</small><div>@if(!$user['admin'])<button type="button" wire:click="toggleSeller({{ $user['id'] }})">{{ $user['seller'] ? 'إزالة دور البائع' : 'منح دور البائع' }}</button>@else<small>محمي</small>@endif</div></article>@endforeach</div></div>

{{-- صفحة مراجعة طلبات إنشاء المتاجر واعتمادها أو رفضها. --}}
<div class="admin-management-page">
@include('filament.pages.admin-management-styles')
<div class="admin-page-heading"><div><span>مراجعة المتاجر</span><h1>المتاجر وطلبات الانضمام</h1></div></div>
<div class="admin-table"><div class="admin-table-head"><span>المتجر</span><span>الحالة</span><span>الإجراءات</span></div>
@forelse ($applications as $application)
<article class="admin-table-row"><div><strong>{{ $application['name'] }}</strong><small>{{ $application['applicant'] }} · {{ $application['craft'] }}</small><small>{{ $application['region'] }} · {{ $application['phone'] }}</small></div><span class="status status-{{ $application['status'] }}">{{ $application['status'] }}</span>
<div class="admin-actions">@if($application['status'] === 'pending')<button type="button" wire:click="approve({{ $application['id'] }})">اعتماد</button><details><summary>رفض</summary><input type="text" wire:model="reason" placeholder="سبب الرفض"><button type="button" wire:click="reject({{ $application['id'] }})">حفظ</button></details>@else<small>{{ $application['note'] ?: 'تمت المراجعة' }}</small>@endif</div></article>
@empty<p class="admin-empty">لا توجد طلبات متاجر.</p>@endforelse</div></div>

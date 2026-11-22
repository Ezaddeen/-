{{-- صفحة الشكاوى والبلاغات ورد الإدارة عليها وتحديث حالتها. --}}
<div class="admin-management-page">
@include('filament.pages.admin-management-styles')
<div class="admin-page-heading"><div><span>الدعم والمطالبات</span><h1>الشكاوى والبلاغات</h1></div></div>
<div class="admin-table"><div class="admin-table-head"><span>البلاغ أو الشكوى</span><span>الحالة</span><span>المعالجة</span></div>
@forelse($complaints as $complaint)<article class="admin-table-row"><div><strong>{{ $complaint['subject'] }}</strong><small>{{ $complaint['customer'] ?: 'زائر' }} · {{ $complaint['message'] }}</small></div><span>{{ $complaint['status'] }}</span><div class="admin-actions"><input type="text" wire:model="response" placeholder="رد الإدارة" value="{{ $complaint['response'] }}"><button type="button" wire:click="respond({{ $complaint['id'] }})">حفظ الرد</button></div></article>@empty<p class="admin-empty">لا توجد شكاوى أو بلاغات مسجلة.</p>@endforelse</div></div>

{{-- صفحة إعدادات اسم المنصة والدعم والعملة والنصوص العامة. --}}
<div class="admin-management-page">
@include('filament.pages.admin-management-styles')
<div class="admin-page-heading"><div><span>تكوين المنصة</span><h1>الإعدادات</h1></div></div>
<form class="admin-settings-form" wire:submit.prevent="save"><label>اسم المنصة<input type="text" wire:model="platformName"></label><label>البريد للدعم<input type="email" wire:model="supportEmail"></label><label>رقم الدعم<input type="text" wire:model="supportPhone"></label><label>العملة<input type="text" wire:model="currency"></label><label>نص الخصوصية<textarea rows="5" wire:model="privacyText"></textarea></label><label>نص شروط الاستخدام<textarea rows="5" wire:model="termsText"></textarea></label><button type="submit">حفظ الإعدادات</button></form></div>

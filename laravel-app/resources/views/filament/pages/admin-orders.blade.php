{{-- صفحة الطلبات العامة وتغيير حالة الطلبات المسجلة. --}}
<div class="admin-management-page">
@include('filament.pages.admin-management-styles')
<div class="admin-page-heading"><div><span>متابعة الطلبات</span><h1>الطلبات ومراجعتها</h1></div></div>
<div class="admin-table"><div class="admin-table-head"><span>الطلب</span><span>الحالة والدفع والشحن</span><span>الإجراء</span></div>
@forelse ($orders as $order)<article class="admin-table-row"><div><strong>{{ $order['reference'] }}</strong><small>{{ $order['customer'] }} · {{ $order['created_at'] }}</small></div><div><span>{{ $order['status'] }}</span><small>{{ $order['payment_status'] }} · {{ $order['shipment_status'] }}</small></div><select wire:change="setStatus({{ $order['id'] }}, $event.target.value)"><option value="">تغيير الحالة</option>@foreach(['بانتظار قبول البائع','مقبول','قيد التجهيز','جاهز للشحن/الاستلام','تم الشحن','تم التسليم','مرفوض','ملغى','تعذر التسليم'] as $status)<option value="{{ $status }}">{{ $status }}</option>@endforeach</select></article>@empty<p class="admin-empty">لا توجد طلبات مسجلة.</p>@endforelse</div></div>

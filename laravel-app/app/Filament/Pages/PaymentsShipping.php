<?php

namespace App\Filament\Pages;

// يتيح مراجعة حالات الدفع والشحن ورقم التتبع دون تنفيذ دفعة مالية حقيقية.
use App\Models\AuditLog;
use App\Models\Order;
use Filament\Pages\Page;

class PaymentsShipping extends Page
{
    protected static ?string $navigationLabel = 'المدفوعات والشحن';

    protected static ?string $title = 'المدفوعات والشحن';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.admin-payments';

    public array $orders = [];

    // يحضر الطلبات وحقول الدفع والشحن ورقم التتبع.
    public function mount(): void
    {
        $this->orders = Order::query()->latest()->get()->map(fn (Order $order): array => [
            'id' => $order->id,
            'reference' => $order->reference,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'shipment_status' => $order->shipment_status,
            'tracking_number' => $order->tracking_number,
            'total' => $order->total,
        ])->all();
    }

    // يسجل نتيجة مراجعة الدفعة ولا ينفذ عملية مالية خارجية.
    public function updatePayment(int $id, string $status): void
    {
        abort_unless(in_array($status, ['بانتظار المراجعة', 'عند الاستلام', 'بانتظار التواصل', 'تم التأكيد', 'مرفوض'], true), 422);
        $order = Order::query()->findOrFail($id);
        $order->update(['payment_status' => $status]);
        AuditLog::create(['user_id' => auth()->id(), 'action' => 'review_payment', 'entity_type' => Order::class, 'entity_id' => $id, 'description' => 'مراجعة دفعة الطلب '.$order->reference.' وحالة الدفع '.$status]);
        $this->mount();
    }

    // يحدث حالة الشحنة ورقم التتبع ويحفظ العملية في سجل التدقيق.
    public function updateShipment(int $id, string $status, string $tracking = ''): void
    {
        abort_unless(in_array($status, ['لم يتم الشحن', 'تم التجهيز', 'تم الشحن', 'تم التسليم', 'تعذر التسليم'], true), 422);
        $order = Order::query()->findOrFail($id);
        $order->update(['shipment_status' => $status, 'tracking_number' => trim($tracking) ?: null]);
        AuditLog::create(['user_id' => auth()->id(), 'action' => 'update_shipment', 'entity_type' => Order::class, 'entity_id' => $id, 'description' => 'تحديث شحنة الطلب '.$order->reference.' إلى '.$status]);
        $this->mount();
    }
}

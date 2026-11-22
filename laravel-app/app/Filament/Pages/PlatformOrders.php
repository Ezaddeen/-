<?php

namespace App\Filament\Pages;

// يعرض طلبات المنصة ويتيح لمدير المنصة مراجعة الحالات وتسجيل التحولات.
use App\Models\AuditLog;
use App\Models\Order;
use Filament\Pages\Page;

class PlatformOrders extends Page
{
    protected static ?string $navigationLabel = 'الطلبات ومراجعتها';

    protected static ?string $title = 'الطلبات ومراجعتها';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.admin-orders';

    public array $orders = [];

    // يحضر الطلبات الأخيرة وبيانات العميل والحالات المالية والشحنية.
    public function mount(): void
    {
        $this->orders = Order::query()->with('user')->latest()->get()->map(fn (Order $order): array => [
            'id' => $order->id,
            'reference' => $order->reference,
            'customer' => $order->user?->name,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'shipment_status' => $order->shipment_status,
            'total' => $order->total,
            'created_at' => $order->created_at->format('Y/m/d H:i'),
        ])->all();
    }

    // يغير حالة طلب مسجل ويحفظ التحول في تاريخ الطلب وسجل التدقيق.
    public function setStatus(int $id, string $status): void
    {
        $allowed = ['بانتظار قبول البائع', 'مقبول', 'قيد التجهيز', 'جاهز للشحن/الاستلام', 'تم الشحن', 'تم التسليم', 'مرفوض', 'ملغى', 'تعذر التسليم'];
        abort_unless(in_array($status, $allowed, true), 422);
        $order = Order::query()->findOrFail($id);
        $history = $order->history ?? [];
        $history[] = ['status' => $status, 'at' => now()->format('Y-m-d H:i'), 'actor' => 'مدير المنصة'];
        $order->update(['status' => $status, 'history' => $history]);
        AuditLog::create(['user_id' => auth()->id(), 'action' => 'change_order_status', 'entity_type' => Order::class, 'entity_id' => $id, 'description' => 'تغيير حالة الطلب '.$order->reference.' إلى '.$status]);
        $this->mount();
    }
}

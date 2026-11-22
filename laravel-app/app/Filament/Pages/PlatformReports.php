<?php

namespace App\Filament\Pages;

// يجمع مؤشرات التقرير ويصدر بيانات الطلبات بصيغة CSV للمدير.
use App\Models\Order;
use App\Models\SellerApplication;
use App\Models\User;
use Filament\Pages\Page;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PlatformReports extends Page
{
    protected static ?string $navigationLabel = 'التقارير والتصدير';

    protected static ?string $title = 'التقارير والتصدير';

    protected static ?int $navigationSort = 7;

    protected string $view = 'filament.pages.admin-reports';

    public array $summary = [];

    // يحضر مؤشرات التقرير العام للمنصة.
    public function mount(): void
    {
        $this->summary = [
            'users' => User::query()->count(),
            'sellers' => User::query()->where('is_seller', true)->count(),
            'pending_stores' => SellerApplication::query()->where('status', 'pending')->count(),
            'orders' => Order::query()->count(),
            'orders_total' => (int) Order::query()->sum('total'),
            'delivered' => Order::query()->where('status', 'تم التسليم')->count(),
        ];
    }

    // يصدر الطلبات المسجلة إلى ملف CSV قابل للتنزيل.
    public function exportOrders(): StreamedResponse
    {
        return response()->streamDownload(function (): void {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['المرجع', 'العميل', 'الحالة', 'حالة الدفع', 'حالة الشحن', 'الإجمالي', 'التاريخ']);
            Order::query()->with('user')->latest()->each(function (Order $order) use ($output): void {
                fputcsv($output, [$order->reference, $order->user?->name, $order->status, $order->payment_status, $order->shipment_status, $order->total, $order->created_at->format('Y-m-d H:i')]);
            });
            fclose($output);
        }, 'platform-orders.csv');
    }
}

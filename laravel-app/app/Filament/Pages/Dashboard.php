<?php

namespace App\Filament\Pages;

// يعرّض لوحة المدير الرئيسية ومؤشرات المنصة والطلبات ومراجعة المتاجر.
use App\Models\AuditLog;
use App\Models\Order;
use App\Models\Product;
use App\Models\SellerApplication;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\DB;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'لوحة التحكم';

    protected static ?string $title = 'لوحة التحكم';

    public string $period = '30';

    public string $rejectionReason = '';

    public array $metrics = [];

    public array $orderSeries = [];

    public array $recentOrders = [];

    public array $pendingApplications = [];

    public array $statusSummary = [];

    protected string $view = 'filament.pages.platform-dashboard';

    // يحمل مؤشرات Dashboard عند فتح الصفحة ويعاد استدعاؤه بعد المراجعات.
    public function mount(): void
    {
        $this->refreshData();
    }

    // يغير فترة التقرير ويحسب بيانات الأيام الجديدة.
    public function setPeriod(string $period): void
    {
        $this->period = in_array($period, ['7', '30', '90'], true) ? $period : '30';
        $this->refreshData();
    }

    // اعتماد طلب المتجر وتفعيل حساب البائع وحفظ سجل التدقيق.
    public function approveApplication(int $applicationId): void
    {
        DB::transaction(function () use ($applicationId): void {
            $application = SellerApplication::query()->lockForUpdate()->findOrFail($applicationId);
            abort_unless($application->status === 'pending', 409);

            $application->update([
                'status' => 'approved',
                'review_note' => null,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);
            $application->user()->update(['is_seller' => true]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'approve_seller_application',
                'entity_type' => SellerApplication::class,
                'entity_id' => $application->id,
                'description' => 'اعتماد طلب إنشاء متجر: '.$application->shop_name,
            ]);
        });

        $this->refreshData();
        Notification::make()->title('تم اعتماد متجر البائع')->success()->send();
    }

    // رفض طلب المتجر وتسجيل سبب المراجعة وسحب صلاحية البائع إن كانت ممنوحة.
    public function rejectApplication(int $applicationId, string $reason): void
    {
        $reason = trim($reason);
        abort_if(mb_strlen($reason) < 5, 422);

        DB::transaction(function () use ($applicationId, $reason): void {
            $application = SellerApplication::query()->lockForUpdate()->findOrFail($applicationId);
            abort_unless($application->status === 'pending', 409);

            $application->update([
                'status' => 'rejected',
                'review_note' => $reason,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);
            $application->user()->update(['is_seller' => false]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'reject_seller_application',
                'entity_type' => SellerApplication::class,
                'entity_id' => $application->id,
                'description' => 'رفض طلب إنشاء متجر: '.$application->shop_name.' — '.$reason,
            ]);
        });

        $this->refreshData();
        Notification::make()->title('تم رفض الطلب وإضافة سبب المراجعة')->success()->send();
    }

    // يحدد عدد أعمدة محتوى لوحة التحكم حسب حجم الشاشة.
    public function getColumns(): int|array
    {
        return ['md' => 2, 'xl' => 3];
    }

    // يجمع مؤشرات الطلبات والرسوم البيانية والطلبات الحديثة وحالاتها.
    private function refreshData(): void
    {
        $days = (int) $this->period;
        $from = now()->subDays($days)->startOfDay();

        $orders = Order::query()->where('created_at', '>=', $from);
        $this->metrics = [
            'users' => User::query()->count(),
            'customers' => User::query()->where('is_admin', false)->where('is_seller', false)->count(),
            'sellers' => User::query()->where('is_seller', true)->count(),
            'pending_sellers' => SellerApplication::query()->where('status', 'pending')->count(),
            'products' => Product::query()->count(),
            'period_orders' => (clone $orders)->count(),
            'period_total' => (int) (clone $orders)->sum('total'),
            'pending_payments' => Order::query()->whereIn('payment_status', ['بانتظار المراجعة', 'بانتظار التواصل'])->count(),
        ];

        $series = [];
        for ($offset = $days - 1; $offset >= 0; $offset--) {
            $date = now()->subDays($offset)->toDateString();
            $series[$date] = 0;
        }

        foreach (Order::query()->where('created_at', '>=', $from)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as aggregate')
            ->groupBy('date')
            ->pluck('aggregate', 'date') as $date => $count) {
            if (isset($series[(string) $date])) {
                $series[(string) $date] = (int) $count;
            }
        }

        $this->orderSeries = [
            'labels' => array_map(
                fn (string $date): string => date('j/n', strtotime($date)),
                array_keys($series),
            ),
            'values' => array_values($series),
        ];

        $this->recentOrders = Order::query()->latest()->limit(8)->get()
            ->map(fn (Order $order): array => [
                'reference' => $order->reference,
                'customer' => $order->user?->name ?? 'عميل غير معروف',
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'total' => $order->total,
                'created_at' => $order->created_at->format('Y/m/d H:i'),
            ])
            ->all();

        $this->pendingApplications = SellerApplication::query()
            ->with('user')
            ->where('status', 'pending')
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn (SellerApplication $application): array => [
                'id' => $application->id,
                'shop_name' => $application->shop_name,
                'craft' => $application->craft,
                'city' => $application->city,
                'governorate' => $application->governorate,
                'applicant' => $application->user?->name,
                'created_at' => $application->created_at->format('Y/m/d'),
            ])
            ->all();

        $this->statusSummary = Order::query()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->map(fn ($count): int => (int) $count)
            ->all();
    }
}

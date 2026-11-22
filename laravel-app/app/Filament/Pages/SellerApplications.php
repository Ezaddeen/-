<?php

namespace App\Filament\Pages;

// يتيح لمدير المنصة مراجعة طلبات إنشاء المتاجر واعتمادها أو رفضها.
use App\Models\AuditLog;
use App\Models\SellerApplication;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class SellerApplications extends Page
{
    protected static ?string $navigationLabel = 'المتاجر وطلبات الانضمام';

    protected static ?string $title = 'المتاجر وطلبات الانضمام';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.admin-seller-applications';

    public array $applications = [];

    public string $reason = '';

    // يحمل طلبات المتاجر وحالاتها عند فتح الصفحة.
    public function mount(): void
    {
        $this->refreshApplications();
    }

    // يعتمد طلب متجر ويمنح مقدم الطلب دور بائع ويحفظ القرار.
    public function approve(int $id): void
    {
        DB::transaction(function () use ($id): void {
            $application = SellerApplication::query()->lockForUpdate()->findOrFail($id);
            abort_unless($application->status === 'pending', 409);
            $application->update(['status' => 'approved', 'review_note' => null, 'reviewed_by' => auth()->id(), 'reviewed_at' => now()]);
            $application->user()->update(['is_seller' => true]);
            AuditLog::create(['user_id' => auth()->id(), 'action' => 'approve_store', 'entity_type' => SellerApplication::class, 'entity_id' => $id, 'description' => 'اعتماد متجر '.$application->shop_name]);
        });
        $this->refreshApplications();
    }

    // يرفض طلب متجر ويسجل سبب رفضه ويمنع تفعيل دور البائع.
    public function reject(int $id): void
    {
        abort_if(mb_strlen(trim($this->reason)) < 5, 422);
        DB::transaction(function () use ($id): void {
            $application = SellerApplication::query()->lockForUpdate()->findOrFail($id);
            abort_unless($application->status === 'pending', 409);
            $application->update(['status' => 'rejected', 'review_note' => trim($this->reason), 'reviewed_by' => auth()->id(), 'reviewed_at' => now()]);
            $application->user()->update(['is_seller' => false]);
            AuditLog::create(['user_id' => auth()->id(), 'action' => 'reject_store', 'entity_type' => SellerApplication::class, 'entity_id' => $id, 'description' => 'رفض متجر '.$application->shop_name]);
        });
        $this->reason = '';
        $this->refreshApplications();
    }

    // يعيد تحميل قائمة الطلبات بعد تنفيذ قرار الإدارة.
    private function refreshApplications(): void
    {
        $this->applications = SellerApplication::query()->with('user')->latest()->get()->map(fn (SellerApplication $app): array => [
            'id' => $app->id,
            'name' => $app->shop_name,
            'applicant' => $app->user?->name,
            'craft' => $app->craft,
            'region' => $app->governorate.'، '.$app->city,
            'phone' => $app->phone,
            'about' => $app->about,
            'status' => $app->status,
            'note' => $app->review_note,
        ])->all();
    }
}

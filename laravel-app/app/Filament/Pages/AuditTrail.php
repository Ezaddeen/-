<?php

namespace App\Filament\Pages;

// يعرض آخر العمليات الحساسة التي نفذها مديرو المنصة.
use App\Models\AuditLog;
use Filament\Pages\Page;

class AuditTrail extends Page
{
    protected static ?string $navigationLabel = 'سجل التدقيق';

    protected static ?string $title = 'سجل التدقيق';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.admin-audit';

    public array $logs = [];

    // يحمل أحدث عمليات التدقيق وآثارها الإدارية.
    public function mount(): void
    {
        $this->logs = AuditLog::query()->with('user')->latest()->limit(200)->get()->map(fn (AuditLog $log): array => [
            'id' => $log->id,
            'user' => $log->user?->name ?? 'النظام',
            'action' => $log->action,
            'description' => $log->description,
            'entity_type' => $log->entity_type,
            'entity_id' => $log->entity_id,
            'created_at' => $log->created_at->format('Y/m/d H:i'),
        ])->all();
    }
}

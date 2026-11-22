<?php

namespace App\Filament\Pages;

// يعرض الشكاوى والبلاغات ويحفظ رد الإدارة وحالتها المعالجة.
use App\Models\Complaint;
use Filament\Pages\Page;

class Complaints extends Page
{
    protected static ?string $navigationLabel = 'الشكاوى والبلاغات';

    protected static ?string $title = 'الشكاوى والبلاغات';

    protected static ?int $navigationSort = 9;

    protected string $view = 'filament.pages.admin-complaints';

    public array $complaints = [];

    public string $response = '';

    // يحضر الشكاوى والبلاغات وبيانات العملاء إن توفروا.
    public function mount(): void
    {
        $this->complaints = Complaint::query()->with('user')->latest()->get()->map(fn (Complaint $complaint): array => [
            'id' => $complaint->id,
            'subject' => $complaint->subject,
            'message' => $complaint->message,
            'status' => $complaint->status,
            'customer' => $complaint->user?->name,
            'response' => $complaint->admin_response,
        ])->all();
    }

    // يحفظ رد مدير المنصة ويضع الشكوى في حالة تمت المعالجة.
    public function respond(int $id): void
    {
        abort_if(mb_strlen(trim($this->response)) < 3, 422);
        Complaint::query()->findOrFail($id)->update([
            'status' => 'تمت المعالجة',
            'assigned_to' => auth()->id(),
            'admin_response' => trim($this->response),
        ]);
        $this->response = '';
        $this->mount();
    }
}

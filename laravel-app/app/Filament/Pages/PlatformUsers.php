<?php

namespace App\Filament\Pages;

// يعرض حسابات المستخدمين والعملاء والبائعين ويتيح تعديل دور البائع دون إتاحة إدارة المدير.
use App\Models\User;
use Filament\Pages\Page;

class PlatformUsers extends Page
{
    protected static ?string $navigationLabel = 'المستخدمون والعملاء';

    protected static ?string $title = 'المستخدمون والعملاء';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.admin-users';

    public array $users = [];

    // يحضر بيانات المستخدمين للعملاء والبائعين والمديرين مع دورهم.
    public function mount(): void
    {
        $this->users = User::query()->latest()->get()->map(fn (User $user): array => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'admin' => $user->is_admin,
            'seller' => $user->is_seller,
            'created_at' => $user->created_at->format('Y/m/d'),
        ])->all();
    }

    // يضيف أو يزيل دور البائع دون السماح بتعديل حساب المدير.
    public function toggleSeller(int $id): void
    {
        $user = User::query()->findOrFail($id);
        abort_if($user->is_admin, 403);
        $user->update(['is_seller' => ! $user->is_seller]);
        $this->mount();
    }
}

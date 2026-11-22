<?php

namespace App\Filament\Pages;

// يحفظ إعدادات اسم المنصة والدعم والعملة والنصوص العامة في قاعدة البيانات.
use App\Models\AuditLog;
use App\Models\PlatformSetting;
use Filament\Pages\Page;

class PlatformSettings extends Page
{
    protected static ?string $navigationLabel = 'الإعدادات';

    protected static ?string $title = 'إعدادات المنصة';

    protected static ?int $navigationSort = 8;

    protected string $view = 'filament.pages.admin-settings';

    public string $platformName = 'بصمة الحرفيين';

    public string $supportEmail = '';

    public string $supportPhone = '';

    public string $currency = 'ر.ي';

    public string $privacyText = '';

    public string $termsText = '';

    // يحمل إعدادات المنصة المحفوظة إلى حقول الصفحة.
    public function mount(): void
    {
        $values = PlatformSetting::query()->pluck('value', 'key')->all();
        $this->platformName = $values['platform_name'] ?? 'بصمة الحرفيين';
        $this->supportEmail = $values['support_email'] ?? '';
        $this->supportPhone = $values['support_phone'] ?? '';
        $this->currency = $values['currency'] ?? 'ر.ي';
        $this->privacyText = $values['privacy_text'] ?? '';
        $this->termsText = $values['terms_text'] ?? '';
    }

    // يحفظ إعدادات الاسم والدعم والعملة والنصوص ويضمها لسجل التدقيق.
    public function save(): void
    {
        foreach ([
            'platform_name' => $this->platformName,
            'support_email' => $this->supportEmail,
            'support_phone' => $this->supportPhone,
            'currency' => $this->currency,
            'privacy_text' => $this->privacyText,
            'terms_text' => $this->termsText,
        ] as $key => $value) {
            PlatformSetting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }
        AuditLog::create(['user_id' => auth()->id(), 'action' => 'update_settings', 'entity_type' => 'PlatformSetting', 'entity_id' => null, 'description' => 'تحديث إعدادات المنصة']);
        $this->mount();
    }
}

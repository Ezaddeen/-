<?php

namespace App\Models;

// يمثل هذا الصف عنوان توصيل تابعًا لمستخدم ويحدد موقعه وعنوانه الافتراضي.
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $fillable = [
        'label',
        'governorate',
        'city',
        'district',
        'landmark',
        'phone',
        'note',
        'is_default',
    ];

    // يحول حالة العنوان الافتراضي إلى قيمة منطقية واضحة.
    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }

    // يربط عنوان التوصيل بحساب مالكه.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // يجهز بيانات العنوان للنماذج وصفحات الدفع والطلب.
    public function toViewData(): array
    {
        return [
            'id' => (string) $this->id,
            'label' => $this->label,
            'governorate' => $this->governorate,
            'city' => $this->city,
            'district' => $this->district,
            'landmark' => $this->landmark,
            'phone' => $this->phone,
            'note' => $this->note,
            'is_default' => $this->is_default,
        ];
    }
}

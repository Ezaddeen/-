<?php

namespace App\Models;

// يمثل هذا الصف طلب إنشاء متجر ويخزن بيانات المتجر وقرار الإدارة ومعلومات المراجعة.
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerApplication extends Model
{
    protected $fillable = [
        'shop_name',
        'store_slug',
        'logo_url',
        'banner_url',
        'website',
        'craft',
        'governorate',
        'city',
        'about',
        'phone',
        'status',
        'review_note',
        'reviewed_by',
        'reviewed_at',
    ];

    // يحول تاريخ مراجعة الطلب إلى كائن تاريخ يسهّل عرضه.
    protected function casts(): array
    {
        return ['reviewed_at' => 'datetime'];
    }

    // يربط طلب المتجر بالمستخدم الذي قدمه.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // يربط قرار المراجعة بحساب المدير الذي اعتمده أو رفضه.
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}

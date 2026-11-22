<?php

namespace App\Models;

// يحفظ هذا الصف تقييم عميل لمنتج مشتراة داخل طلب مكتمل.
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = ['user_id', 'product_id', 'order_id', 'rating', 'comment'];

    // يحول درجة التقييم إلى عدد صحيح.
    protected function casts(): array
    {
        return ['rating' => 'integer'];
    }

    // يربط التقييم بالمستخدم الذي كتبته.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // يربط التقييم بالمنتج الذي قيّمه المستخدم.
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // يربط التقييم بالطلب الذي أثبت أن الشراء أو التسليم تم.
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}

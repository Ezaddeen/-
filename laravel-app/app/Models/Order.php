<?php

namespace App\Models;

// يمثل هذا الصف الطلب ويحفظ لقطة المنتجات والعنوان وحالات الدفع والشحن والسجل الزمني.
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'reference',
        'status',
        'payment_method',
        'customer_note',
        'payment_status',
        'shipment_status',
        'tracking_number',
        'cancel_reason',
        'items',
        'address',
        'subtotal',
        'delivery_fee',
        'total',
        'history',
    ];

    protected function casts(): array
    {
        return [
            'items' => 'array',
            'address' => 'array',
            'history' => 'array',
            'subtotal' => 'integer',
            'delivery_fee' => 'integer',
            'total' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function toViewData(): array
    {
        return [
            'reference' => $this->reference,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'customer_note' => $this->customer_note,
            'items' => $this->items,
            'address' => $this->address,
            'subtotal' => $this->subtotal,
            'delivery_fee' => $this->delivery_fee,
            'total' => $this->total,
            'history' => $this->history,
        ];
    }
}

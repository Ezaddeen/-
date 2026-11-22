<?php

namespace App\Models;

// يحفظ هذا الصف سجلًا إداريًا يوضح العملية والسجل المتأثر والمستخدم الذي نفذها.
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = ['user_id', 'action', 'entity_type', 'entity_id', 'description'];

    // يربط سجل التدقيق بالمستخدم المسؤول إن كان موجودا.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

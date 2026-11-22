<?php

namespace App\Models;

// يمثل هذا الصف شكوى أو بلاغًا من مستخدم ويخزن حالة المعالجة ورد الإدارة.
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complaint extends Model
{
    protected $fillable = ['user_id', 'subject', 'message', 'status', 'assigned_to', 'admin_response'];

    // يربط الشكوى بالمستخدم المبلّغ إن كان مسجلا.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // يربط الشكوى بالمدير المسؤول عن المعالجة.
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}

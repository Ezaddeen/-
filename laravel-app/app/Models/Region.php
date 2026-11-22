<?php

namespace App\Models;

// يمثل هذا الصف محافظة أو منطقة تخدمها المنصة ويحفظ حالة تفعيلها وترتيبها.
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = ['name', 'is_active', 'sort_order'];

    // يحول حالة المنطقة وترتيبها إلى نوعيها المعتمدين.
    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }
}

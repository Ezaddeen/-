<?php

namespace App\Models;

// يمثل هذا الصف تصنيفًا إداريًا للمنتجات ويحفظ حالة نشاطه وترتيبه.
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'is_active', 'sort_order'];

    // يحول نشاط التصنيف وترتيبه إلى قيم منطقية وأعداد.
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}

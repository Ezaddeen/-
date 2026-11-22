<?php

namespace App\Models;

// يخزن هذا الصف إعدادات المنصة العامة كمفاتيح وقيم قابلة للتغيير.
use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    protected $fillable = ['key', 'value'];
}

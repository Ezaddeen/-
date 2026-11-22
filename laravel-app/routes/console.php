<?php

// يعرّف هذا الملف أوامر Artisan التنفيذية المجدولة أو التفاعلية للمشروع.

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

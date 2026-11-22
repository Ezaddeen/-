<?php

// يسجل مزودي التطبيق والخدمات واللوحة الإدارية.

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
];

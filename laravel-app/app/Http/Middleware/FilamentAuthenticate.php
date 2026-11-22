<?php

namespace App\Http\Middleware;

// يوجه هذا الـMiddleware الزائر الذي يطلب لوحة Filament إلى صفحة الدخول الموحدة بالمتجر.

use Filament\Http\Middleware\Authenticate as BaseAuthenticate;

class FilamentAuthenticate extends BaseAuthenticate
{
    protected function redirectTo($request): ?string
    {
        return route('login');
    }
}

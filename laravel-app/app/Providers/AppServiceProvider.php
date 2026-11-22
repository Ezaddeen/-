<?php

namespace App\Providers;

// يسجل هذا المزود صلاحيات الوصول العامة للوحة البائع ومراجعة طلبات المتاجر.

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('access-seller-workspace', fn (User $user): bool => $user->is_admin || $user->is_seller);
    }
}

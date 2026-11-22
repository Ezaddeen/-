<?php

namespace Database\Seeders;

// ينشئ أو يحدث حساب مدير المنصة التجريبي من إعدادات البيئة.
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    // ينشئ المدير التجريبي أو يحدث بياناته دون إنشاء نسخة مكررة.
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@basmat-anamil.com');
        $password = env('ADMIN_PASSWORD', 'Admin12345!');

        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => env('ADMIN_NAME', 'مدير بصمة الحرفيين'),
                'password' => Hash::make($password),
                'is_admin' => true,
                'email_verified_at' => now(),
            ],
        );
    }
}

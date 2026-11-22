<?php

namespace App\Http\Controllers;

// يدير هذا الملف تسجيل الدخول والتسجيل والخروج ويوجه كل دور إلى مساحة نظامه.

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // يتحقق من بيانات الدخول ويرمم الجلسة ويوجه الحساب حسب دوره.
    public function storeLogin(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'بيانات الدخول غير صحيحة.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();
        $this->forgetAccountData($request);

        $user = $request->user();

        if ($user->is_admin) {
            return redirect('/admin');
        }

        if ($user->is_seller) {
            return redirect()->route('seller.dashboard');
        }

        return redirect()->intended(route('account.dashboard'));
    }

    // ينشئ حساب عميل جديد بعد تحقق الاسم والبريد وكلمة المرور والموافقة.
    public function storeRegistration(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'terms' => ['accepted'],
        ], [
            'terms.accepted' => 'يجب الموافقة على شروط الاستخدام وسياسة الخصوصية.',
        ]);

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        $this->forgetAccountData($request);

        return redirect()->route('account.dashboard');
    }

    // يسجل خروج المستخدم وينهي جلسة الحساب وحذفه من بيانات الجلسة المؤقتة.
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $this->forgetAccountData($request);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    // يمسح بيانات حساب المتجر والعميل المؤقتة عند الخروج أو تسجيل الدخول.
    private function forgetAccountData(Request $request): void
    {
        $request->session()->forget([
            'account_profile',
            'account_addresses',
            'customer_orders',
            'seller_products',
            'seller_profile',
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    // يفحص إنشاء حساب عميل جديد وتسجيله آليا.
    public function test_customer_can_register_and_is_signed_in(): void
    {
        $response = $this->post('/register', [
            'name' => 'ريم محمد',
            'email' => 'reem@example.com',
            'password' => 'strong-password',
            'password_confirmation' => 'strong-password',
            'terms' => '1',
        ]);

        $response->assertRedirect('/account');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'reem@example.com',
            'is_admin' => false,
            'is_seller' => false,
        ]);
        $this->assertNotSame('strong-password', User::query()->firstOrFail()->password);
    }

    // يرفض البريد المكرر أو تأكيد كلمة مرور غير مطابق.
    public function test_registration_rejects_duplicate_email_and_unconfirmed_password(): void
    {
        User::factory()->create(['email' => 'reem@example.com']);

        $this->from('/register')->post('/register', [
            'name' => 'ريم محمد',
            'email' => 'reem@example.com',
            'password' => 'strong-password',
            'password_confirmation' => 'different-password',
            'terms' => '1',
        ])->assertRedirect('/register')
            ->assertSessionHasErrors(['email', 'password']);
    }

    // يفحص دخول العميل وخروجه وتوجيهه إلى حسابه.
    public function test_customer_can_log_in_and_log_out(): void
    {
        $user = User::factory()->create(['password' => 'strong-password']);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'strong-password',
        ])->assertRedirect('/account');

        $this->assertAuthenticatedAs($user);
        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }

    // يفحص توجيه المدير إلى لوحة الإدارة بعد الدخول.
    public function test_administrator_is_redirected_to_the_admin_panel_after_login(): void
    {
        $admin = User::factory()->create([
            'password' => 'strong-password',
            'is_admin' => true,
        ]);

        $this->post('/login', [
            'email' => $admin->email,
            'password' => 'strong-password',
        ])
            ->assertRedirect('/admin');

        $this->assertAuthenticatedAs($admin);
    }

    // يفحص توجيه البائع المعتمد إلى مساحة البائع.
    public function test_seller_is_redirected_to_the_seller_workspace_after_login(): void
    {
        $seller = User::factory()->create([
            'password' => 'strong-password',
            'is_seller' => true,
        ]);

        $this->post('/login', [
            'email' => $seller->email,
            'password' => 'strong-password',
        ])
            ->assertRedirect('/seller');

        $this->assertAuthenticatedAs($seller);
    }

    // يرفض بيانات دخول غير صحيحة ويعيد المستخدم لصفحة الدخول.
    public function test_invalid_credentials_are_rejected(): void
    {
        User::factory()->create(['email' => 'reem@example.com']);

        $this->from('/login')->post('/login', [
            'email' => 'reem@example.com',
            'password' => 'wrong-password',
        ])->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    // يفحص حماية مسارات العميل والبائع حسب الدور والصلاحية.
    public function test_customer_and_seller_routes_require_the_right_access(): void
    {
        $this->get('/account')->assertRedirect('/login');
        $this->get('/seller')->assertRedirect('/login');

        $customer = User::factory()->create();
        $this->actingAs($customer)->get('/seller')->assertForbidden();

        $seller = User::factory()->create(['is_seller' => true]);
        $this->actingAs($seller)->get('/seller')->assertOk();
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    // يتأكد أن الضيف ينقل إلى صفحة تسجيل الدخول الموحدة.
    public function test_guest_is_prompted_to_log_in_to_the_admin_panel(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    // يتأكد أن المستخدم العادي لا يملك حق دخول لوحة المدير.
    public function test_regular_user_cannot_access_the_admin_panel(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    // يتأكد أن المدير المعتمد يمكنه فتح لوحة التحكم.
    public function test_administrator_can_access_the_admin_dashboard(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('إدارة بصمة الحرفيين');
    }

    // يتأكد أن عناوين الإدارة القديمة اختفت وأن الصفحات الجديدة تعمل.
    public function test_old_admin_crud_resources_are_removed(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        foreach ([
            '/admin/users',
            '/admin/products',
            '/admin/orders',
            '/admin/categories',
            '/admin/audit-logs',
        ] as $uri) {
            $this->actingAs($admin)->get($uri)->assertNotFound();
        }

        foreach ([
            '/admin/seller-applications',
            '/admin/platform-users',
            '/admin/platform-orders',
            '/admin/payments-shipping',
            '/admin/content-moderation',
            '/admin/categories-regions',
            '/admin/platform-reports',
            '/admin/platform-settings',
            '/admin/complaints',
            '/admin/audit-trail',
        ] as $uri) {
            $this->actingAs($admin)->get($uri)->assertOk();
        }
    }
}

<?php

namespace Tests\Feature;

use App\Models\SellerApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_submit_one_application_and_cannot_resubmit_while_pending(): void
    {
        $customer = User::factory()->create();
        $this->actingAs($customer)->get('/seller-application')->assertOk();

        $this->actingAs($customer)->post('/seller-application', $this->applicationData())
            ->assertRedirect('/seller-application')
            ->assertSessionHas('status');

        $application = SellerApplication::query()->sole();
        $this->assertSame('pending', $application->status);
        $this->assertFalse($customer->fresh()->is_seller);

        $this->actingAs($customer)->from('/seller-application')->post('/seller-application', $this->applicationData([
            'shop_name' => 'متجر مختلف',
        ]))->assertRedirect('/seller-application')->assertSessionHasErrors('application');

        $this->assertSame(1, SellerApplication::query()->count());
        $this->assertSame('مشغل ريم', $application->fresh()->shop_name);
    }

    // يجهز بيانات طلب متجر موحدة للاختبارات ويقبل تعديلات الاختبار.
    private function applicationData(array $overrides = []): array
    {
        return array_merge([
            'shop_name' => 'مشغل ريم',
            'craft' => 'نسيج يدوي',
            'governorate' => 'صنعاء',
            'city' => 'صنعاء القديمة',
            'about' => 'مشروع عائلي يصنع منتجات نسيجية يدوية بجودة محلية.',
            'phone' => '+967 700000000',
        ], $overrides);
    }
}

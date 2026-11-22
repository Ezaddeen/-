<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Support\DemoCatalog;
use Database\Seeders\DemoCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontPagesTest extends TestCase
{
    use RefreshDatabase;

    // يجهز قاعدة الاختبار ويزرع كتالوج العرض قبل كل سيناريو.
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DemoCatalogSeeder::class);
    }

    // يتأكد أن صفحات المتجر تظهر عربية واتجاهها من اليمين إلى اليسار.
    public function test_storefront_pages_render_in_arabic(): void
    {
        foreach (['/', '/products', '/cart', '/login', '/register'] as $uri) {
            $this->get($uri)
                ->assertOk()
                ->assertSee('lang="ar"', false)
                ->assertSee('dir="rtl"', false)
                ->assertDontSee('تجريبي')
                ->assertDontSee('قيد التطوير');
        }
    }

    // يفحص رابط بدء البيع ومساحة البائع في التنقل.
    public function test_storefront_navigation_links_to_the_seller_workspace(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('ابدأ البيع')
            ->assertSee(route('seller-application.show'));
    }

    // يفحص عرض صفحات تفاصيل المنتج والحرفي.
    public function test_product_detail_and_artisan_pages_render(): void
    {
        $this->get('/products/blue-rim-plates')
            ->assertOk()
            ->assertSee('أطباق خزفية بحواف زرقاء');

        $this->get('/artisans/warshat-teen')
            ->assertOk()
            ->assertSee('ورشة طين');
    }

    // يفحص تصفية كتالوج المنتجات وترتيبه بالسعر.
    public function test_product_catalog_filters_by_category_and_sorts_by_price(): void
    {
        $this->get('/products?category=فخار وخزف&sort=price')
            ->assertOk()
            ->assertSee('أطباق خزفية بحواف زرقاء')
            ->assertDontSee('قهوة لاتيه برسمة فنية')
            ->assertDontSee('شريحة بطيخ أحمر طازجة');
    }

    // يطابق روابط صور ووصف بيانات العرض مع اسم كل منتج.
    public function test_demo_product_images_and_descriptions_match_the_products(): void
    {
        $catalog = app(DemoCatalog::class);
        $products = collect($catalog->products())->keyBy('slug');

        $this->assertStringContainsString('photo-1578749556568', $products['blue-rim-plates']['image']);
        $this->assertStringContainsString('أطباق خزفية', $products['blue-rim-plates']['description']);
        $this->assertStringContainsString('photo-1791323452838', $products['sanaani-shawl']['image']);
        $this->assertStringContainsString('نسيج', $products['sanaani-shawl']['description']);
        $this->assertStringContainsString('photo-1630081015918', $products['fresh-watermelon']['image']);
        $this->assertStringContainsString('بطيخ', $products['fresh-watermelon']['description']);
        $this->assertStringContainsString('photo-1535632066927', $products['silver-earrings']['image']);
        $this->assertStringContainsString('أقراط', $products['silver-earrings']['description']);
        $this->assertStringContainsString('photo-1495474472287', $products['latte-art-coffee']['image']);
        $this->assertStringContainsString('أكواب قهوة', $products['latte-art-coffee']['description']);
    }

    // يتأكد أن مزرع الكتالوج يحفظ المنتجات ويستخدمها الواجهة دون استبدال تعديل مخزون موجود.
    public function test_seeded_catalog_products_are_persisted_and_used_by_the_storefront(): void
    {
        $this->assertDatabaseCount('products', 5);

        Product::query()->where('slug', 'blue-rim-plates')->update(['stock' => 2]);
        $this->seed(DemoCatalogSeeder::class);
        $this->assertSame(2, Product::query()->where('slug', 'blue-rim-plates')->firstOrFail()->stock);

        $this->get('/')
            ->assertOk()
            ->assertSee('أطباق خزفية بحواف زرقاء');
        $this->get('/products/fresh-watermelon')
            ->assertOk()
            ->assertSee('شريحة بطيخ أحمر طازجة');
    }

    // يفحص إضافة منتج للسلة وحذفه منها.
    public function test_demo_cart_can_add_and_remove_a_product(): void
    {
        $this->post('/cart/blue-rim-plates')
            ->assertRedirect('/cart');

        $this->get('/cart')
            ->assertOk()
            ->assertSee('أطباق خزفية بحواف زرقاء');

        $this->delete('/cart/blue-rim-plates')
            ->assertRedirect('/cart');

        $this->get('/cart')
            ->assertOk()
            ->assertSee('ما زالت السلة فارغة');
    }

    // يفحص عرض صفحات الحساب والملف والعناوين والمفضلة.
    public function test_customer_account_pages_render(): void
    {
        $this->actingAs(User::factory()->create());

        foreach (['/account', '/account/profile', '/account/addresses', '/account/favorites'] as $uri) {
            $this->get($uri)
                ->assertOk()
                ->assertSee('dir="rtl"', false);
        }
    }

    // يفحص إضافة عنوان يمني وتعيين عنوان افتراضي وحذفه لعزل بيانات المستخدم.
    public function test_customer_can_add_and_set_a_yemeni_delivery_address(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post('/account/addresses', [
            'label' => 'العمل',
            'governorate' => 'تعز',
            'city' => 'تعز',
            'district' => 'المظفر',
            'landmark' => 'بجوار السوق',
            'phone' => '+967 700 000 000',
            'note' => 'الاتصال عند الوصول',
        ])->assertRedirect('/account/addresses');

        $response = $this->get('/account/addresses')
            ->assertOk()
            ->assertSee('العمل')
            ->assertSee('تعز');

        $firstAddress = $user->addresses()->firstOrFail();
        $this->assertTrue($firstAddress->is_default);
        $this->post('/account/addresses', [
            'label' => 'العمل',
            'governorate' => 'تعز',
            'city' => 'تعز',
            'district' => 'القاهرة',
            'landmark' => 'بجوار الجامعة',
            'phone' => '+967 700 000 001',
        ])->assertRedirect('/account/addresses');
        $addressId = $user->addresses()->latest('id')->firstOrFail()->id;
        $this->put("/account/addresses/{$addressId}/default")
            ->assertRedirect('/account/addresses');

        $this->assertTrue($user->addresses()->findOrFail($addressId)->is_default);
        $this->assertFalse($firstAddress->fresh()->is_default);

        $this->post('/logout')->assertRedirect('/');
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $this->get('/account/addresses')->assertOk()->assertSee('العمل');

        $this->actingAs(User::factory()->create())
            ->delete("/account/addresses/{$addressId}")
            ->assertNotFound();

        $this->actingAs($user)
            ->delete("/account/addresses/{$addressId}")
            ->assertRedirect('/account/addresses');
        $this->assertTrue($firstAddress->fresh()->is_default);

        $this->actingAs(User::factory()->create())
            ->put("/account/addresses/{$addressId}/default")
            ->assertNotFound();
    }

    // يفحص تحديث بيانات ملف العميل وحفظها في حساب المستخدم.
    public function test_customer_profile_changes_are_saved_to_the_user_record(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put('/account/profile', [
            'name' => 'ريم الجديدة',
            'email' => 'reem.new@example.com',
            'phone' => '+967 700 000 000',
        ])->assertRedirect('/account/profile');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'ريم الجديدة',
            'email' => 'reem.new@example.com',
            'phone' => '+967 700 000 000',
        ]);
    }

    // يفحص إنشاء الطلب وحجز المخزون ومتابعة حالة سجل الطلب.
    public function test_customer_can_place_and_track_an_order(): void
    {
        $user = User::factory()->create();
        $address = $this->createAddress($user);
        $this->actingAs($user);

        $this->post('/cart/blue-rim-plates')->assertRedirect('/cart');
        $this->get('/checkout')
            ->assertOk()
            ->assertSee('أطباق خزفية بحواف زرقاء');

        $this->post('/checkout', [
            'address_id' => $address->id,
            'payment_method' => 'cash_on_delivery',
            'customer_note' => 'الاتصال قبل الوصول',
        ])->assertRedirect();

        $order = $user->orders()->firstOrFail()->toViewData();
        $reference = $order['reference'];

        $this->assertSame(8500, $order['items'][0]['price']);
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'reference' => $reference,
            'total' => 10500,
        ]);
        $this->assertDatabaseHas('products', [
            'slug' => 'blue-rim-plates',
            'stock' => 3,
        ]);
        $this->assertSame([], session('cart_items', []));
        $this->get("/orders/{$reference}/confirmation")
            ->assertOk()
            ->assertSee($reference);
        $this->get('/account/orders')
            ->assertOk()
            ->assertSee($reference);
        $this->get("/account/orders/{$reference}")
            ->assertOk()
            ->assertSee('الاتصال قبل الوصول');
        $this->post('/logout')->assertRedirect('/');
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect('/account');
        $this->get('/account/orders')->assertOk()->assertSee($reference);
        $this->get('/account/orders/BA-UNKNOWN')
            ->assertNotFound();

        $otherCustomer = User::factory()->create();
        $this->actingAs($otherCustomer)->get("/account/orders/{$reference}")->assertNotFound();
    }

    // يمنع إتمام الطلب إذا كانت السلة فارغة.
    public function test_checkout_redirects_when_the_cart_is_empty(): void
    {
        $this->get('/checkout')->assertRedirect('/login');
        $this->actingAs(User::factory()->create())
            ->get('/checkout')
            ->assertRedirect('/cart');
    }

    // يشترط عنوان توصيل مملوك للمستخدم ويحظر عنوان حساب آخر.
    public function test_checkout_requires_a_saved_address_and_rejects_another_users_address(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post('/cart/blue-rim-plates');

        $this->get('/checkout')
            ->assertRedirect('/account/addresses')
            ->assertSessionHas('status', 'أضف عنوان توصيل قبل إتمام الطلب.');

        $anotherUserAddress = $this->createAddress(User::factory()->create());
        $this->post('/checkout', [
            'address_id' => $anotherUserAddress->id,
            'payment_method' => 'cash_on_delivery',
        ])->assertSessionHasErrors('address_id');

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(['blue-rim-plates' => 1], session('cart_items'));
    }

    // يرفض الطلب إذا تجاوزت الكمية المخزون ولا ينشئ سجل طلب جديد.
    public function test_checkout_rejects_unavailable_stock_without_creating_an_order(): void
    {
        $user = User::factory()->create();
        $address = $this->createAddress($user);
        $this->actingAs($user);
        $this->post('/cart/blue-rim-plates', ['quantity' => 5]);

        $this->post('/checkout', [
            'address_id' => $address->id,
            'payment_method' => 'cash_on_delivery',
        ])->assertSessionHasErrors('cart');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('products', ['slug' => 'blue-rim-plates', 'stock' => 4]);
        $this->assertSame(['blue-rim-plates' => 5], session('cart_items'));
    }

    // يفحص صفحات البائع واتجاهها وحالاتها العربية.
    public function test_seller_pages_render_in_arabic(): void
    {
        $this->actingAs(User::factory()->create(['is_seller' => true]));

        foreach ([
            '/seller',
            '/seller/products',
            '/seller/products/create',
            '/seller/orders',
            '/seller/inventory',
            '/seller/reports',
            '/seller/profile',
        ] as $uri) {
            $this->get($uri)
                ->assertOk()
                ->assertSee('dir="rtl"', false);
        }

        $this->get('/seller/products/create')
            ->assertSee('data-product-image-input', false)
            ->assertSee('data-product-image-preview', false);
    }

    // يفحص إنشاء البائع منتجا ومنع تعديل منتج لبائع آخر.
    public function test_seller_can_create_a_product_and_cannot_edit_another_seller_product(): void
    {
        $seller = User::factory()->create(['name' => 'ورشة طين', 'is_seller' => true]);
        $this->actingAs($seller);

        $this->post('/seller/products', [
            'name' => 'صحن خزفي صغير',
            'category' => 'فخار وخزف',
            'price' => 6500,
            'city' => 'صنعاء القديمة',
            'description' => 'صحن خزفي صغير بتصميم هادئ للاستخدام اليومي.',
            'image' => 'https://images.unsplash.com/photo-1578749556568-bc2c40e68b61',
            'stock' => 3,
        ])->assertRedirect('/seller/products');

        $product = $seller->products()->firstOrFail()->toViewData();
        $this->assertSame('ورشة طين', $product['artisan']);
        $this->assertNotEmpty($product['image']);
        $this->assertDatabaseHas('products', [
            'seller_user_id' => $seller->id,
            'slug' => $product['slug'],
            'image' => 'https://images.unsplash.com/photo-1578749556568-bc2c40e68b61',
            'stock' => 3,
        ]);
        $this->get('/seller/products/silver-earrings/edit')->assertNotFound();

        $otherSeller = User::factory()->create(['is_seller' => true]);
        $this->actingAs($otherSeller)
            ->get("/seller/products/{$product['slug']}/edit")
            ->assertNotFound();

        $this->actingAs($seller)
            ->put("/seller/products/{$product['slug']}", [
                'name' => 'صحن خزفي أزرق',
                'category' => 'فخار وخزف',
                'price' => 7000,
                'city' => 'صنعاء القديمة',
                'description' => 'صحن خزفي أزرق مصنوع يدويًا للاستعمال اليومي.',
                'image' => 'https://images.unsplash.com/photo-1578749556568-bc2c40e68b61',
                'stock' => 5,
            ])->assertRedirect('/seller/products');

        $this->assertSame('صحن خزفي أزرق', $seller->products()->firstOrFail()->name);
    }

    // يرفض حفظ منتج البائع إذا لم يرفق رابط صورة صالح.
    public function test_seller_product_requires_an_image_url(): void
    {
        $this->actingAs(User::factory()->create(['is_seller' => true]));

        $this->post('/seller/products', [
            'name' => 'كوب خزفي صغير',
            'category' => 'فخار وخزف',
            'price' => 4200,
            'city' => 'صنعاء القديمة',
            'description' => 'كوب خزفي مصنوع يدويًا للاستعمال اليومي.',
            'stock' => 2,
        ])->assertSessionHasErrors('image');
    }

    // يفحص عرض تنبيهات المخزون المنخفض في صفحة مخزون البائع.
    public function test_seller_inventory_page_shows_low_stock_from_demo_products(): void
    {
        $this->actingAs(User::factory()->create(['is_seller' => true]));

        $this->post('/seller/products', [
            'name' => 'كوب خزفي صغير',
            'category' => 'فخار وخزف',
            'price' => 4200,
            'city' => 'صنعاء القديمة',
            'description' => 'كوب خزفي مصنوع يدويًا للاستعمال اليومي.',
            'image' => 'https://images.unsplash.com/photo-1578749556568-bc2c40e68b61',
            'stock' => 2,
        ])->assertRedirect('/seller/products');

        $this->get('/seller/inventory')
            ->assertOk()
            ->assertSee('مخزون المتجر')
            ->assertSee('كوب خزفي صغير')
            ->assertSee('كمية منخفضة')
            ->assertSee('2 <small>قطعة</small>', false);
    }

    // يفحص أن البائع يرى ويحدث طلبات منتجاته فقط.
    public function test_seller_can_only_manage_orders_for_products_they_own(): void
    {
        $seller = User::factory()->create(['name' => 'ورشة الطين', 'is_seller' => true]);
        $this->actingAs($seller)->post('/seller/products', [
            'name' => 'صحن أزرق يدوي',
            'category' => 'فخار وخزف',
            'price' => 6500,
            'city' => 'صنعاء القديمة',
            'description' => 'صحن أزرق يدوي من الخزف بتفاصيل واضحة.',
            'image' => 'https://images.unsplash.com/photo-1578749556568-bc2c40e68b61',
            'stock' => 3,
        ])->assertRedirect('/seller/products');
        $product = $seller->products()->firstOrFail();

        $customer = User::factory()->create();
        $address = $this->createAddress($customer);
        $this->actingAs($customer);
        $this->post("/cart/{$product->slug}");
        $this->post('/checkout', [
            'address_id' => $address->id,
            'payment_method' => 'cash_on_delivery',
        ])->assertRedirect();
        $order = $customer->orders()->firstOrFail();

        $this->actingAs($seller)
            ->get("/seller/orders/{$order->reference}")
            ->assertOk()
            ->assertSee($product->name);

        $otherSeller = User::factory()->create(['is_seller' => true]);
        $this->actingAs($otherSeller)
            ->get("/seller/orders/{$order->reference}")
            ->assertNotFound();

        $this->assertSame(2, $product->fresh()->stock);
    }

    // يفحص تقرير المبيعات وحساب الطلبات المكتملة والفترة المختارة.
    public function test_seller_report_uses_completed_demo_orders_and_date_filter(): void
    {
        $customer = User::factory()->create();
        $address = $this->createAddress($customer);
        $this->actingAs($customer);
        $this->post('/cart/blue-rim-plates');
        $this->post('/checkout', [
            'address_id' => $address->id,
            'payment_method' => 'cash_on_delivery',
        ]);
        $reference = Order::query()->firstOrFail()->reference;
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        foreach (['قيد التجهيز', 'جاهز للاستلام من المندوب', 'تم الشحن', 'تم التسليم'] as $status) {
            $this->patch("/seller/orders/{$reference}", ['status' => $status])->assertRedirect();
        }

        $today = now()->toDateString();
        $this->get("/seller/reports?from={$today}&to={$today}")
            ->assertOk()
            ->assertSee('المبيعات المكتملة')
            ->assertSee('8,500')
            ->assertSee('أطباق خزفية بحواف زرقاء');
    }

    // يفحص ظهور حالة الطلب المحدثة للبائع في صفحة العميل.
    public function test_seller_order_status_updates_are_visible_to_the_customer(): void
    {
        $customer = User::factory()->create();
        $address = $this->createAddress($customer);
        $this->actingAs($customer);
        $this->post('/cart/blue-rim-plates');
        $this->post('/checkout', [
            'address_id' => $address->id,
            'payment_method' => 'cash_on_delivery',
        ]);
        $order = Order::query()->firstOrFail();
        $reference = $order->reference;
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        $this->patch("/seller/orders/{$reference}", ['status' => 'قيد التجهيز'])
            ->assertRedirect("/seller/orders/{$reference}");

        $this->assertSame('قيد التجهيز', $order->fresh()->status);
        $this->actingAs($customer);
        $this->get("/account/orders/{$reference}")
            ->assertOk()
            ->assertSee('قيد التجهيز');
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $this->patch("/seller/orders/{$reference}", ['status' => 'تم التسليم'])
            ->assertSessionHasErrors('status');
    }

    private function createAddress(User $user, array $attributes = []): Address
    {
        return $user->addresses()->create([
            'label' => 'المنزل',
            'governorate' => 'أمانة العاصمة',
            'city' => 'صنعاء',
            'district' => 'حدة',
            'landmark' => 'بجوار جامع الرحمن',
            'phone' => '+967 700 000 000',
            'note' => 'الاتصال قبل الوصول',
            'is_default' => true,
            ...$attributes,
        ]);
    }
}

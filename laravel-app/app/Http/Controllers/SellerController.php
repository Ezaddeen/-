<?php

namespace App\Http\Controllers;

// يدير هذا المتحكم مساحة البائع والمنتجات والمخزون والطلبات والتقارير والملف الشخصي.

use App\Models\Order;
use App\Models\Product;
use App\Support\DemoCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SellerController extends Controller
{
    private const ORDER_STATUSES = [
        'بانتظار قبول البائع',
        'قيد التجهيز',
        'جاهز للاستلام من المندوب',
        'تم الشحن',
        'تم التسليم',
    ];

    // يحتوي كتالوج العرض على التصنيفات والبيانات المرجعية المستخدمة في نماذج البائع.
    public function __construct(private readonly DemoCatalog $catalog) {}

    // يعرض مؤشرات الطلبات والمبيعات والمنتجات المنخفضة المخزون للبائع.
    public function dashboard(Request $request): View
    {
        $products = $this->sellerProducts($request);
        $orders = $this->sellerOrders($request);
        $lowStockProducts = array_values(array_filter($products, fn (array $product): bool => $product['stock'] <= 3));

        return view('seller.dashboard', [
            'profile' => $this->sellerProfile($request),
            'products' => $products,
            'orders' => $orders,
            'sales' => array_sum(array_map(fn (array $order): int => $order['status'] === 'تم التسليم' ? $order['total'] : 0, $orders)),
            'lowStockProducts' => $lowStockProducts,
            'orderStatusCounts' => array_count_values(array_column($orders, 'status')),
            'dailySales' => $this->dailySales($orders),
        ]);
    }

    // يعرض حالات مخزون منتجات البائع مع تنبيهات الكمية المنخفضة.
    public function inventory(Request $request): View
    {
        $products = $this->sellerProducts($request);

        return view('seller.inventory', [
            'products' => $products,
            'lowStockProducts' => array_values(array_filter($products, fn (array $product): bool => $product['stock'] <= 3)),
            'totalStock' => array_sum(array_column($products, 'stock')),
        ]);
    }

    // يعرض تقرير المبيعات والمنتجات والطلبات ضمن فترة التاريخ المختارة.
    public function reports(Request $request): View
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);
        $orders = $this->sellerOrders($request);
        $orders = array_filter($orders, function (array $order) use ($filters): bool {
            $date = substr($order['created_at'] ?? '', 0, 10);

            return $date !== ''
                && (! isset($filters['from']) || $date >= $filters['from'])
                && (! isset($filters['to']) || $date <= $filters['to']);
        });
        $completedOrders = array_filter($orders, fn (array $order): bool => $order['status'] === 'تم التسليم');
        $bestProducts = [];

        foreach ($completedOrders as $order) {
            foreach ($order['items'] as $item) {
                $slug = $item['slug'];
                $bestProducts[$slug] ??= ['name' => $item['name'], 'quantity' => 0, 'sales' => 0];
                $bestProducts[$slug]['quantity'] += $item['quantity'];
                $bestProducts[$slug]['sales'] += $item['price'] * $item['quantity'];
            }
        }

        usort($bestProducts, fn (array $left, array $right): int => $right['quantity'] <=> $left['quantity']);
        $sales = array_sum(array_map(fn (array $order): int => $order['total'], $completedOrders));

        return view('seller.reports', [
            'orders' => $orders,
            'completedOrders' => $completedOrders,
            'bestProducts' => $bestProducts,
            'sales' => $sales,
            'averageOrder' => count($completedOrders) > 0 ? (int) round($sales / count($completedOrders)) : 0,
            'filters' => $filters,
        ]);
    }

    // يعرض منتجات حساب البائع المسجل فقط.
    public function products(Request $request): View
    {
        return view('seller.products.index', ['products' => $this->sellerProducts($request)]);
    }

    // يعرض نموذج إضافة منتج جديد مع التصنيفات وبيانات المتجر.
    public function createProduct(Request $request): View
    {
        return view('seller.products.form', [
            'product' => null,
            'categories' => $this->catalog->categories(),
            'profile' => $this->sellerProfile($request),
        ]);
    }

    // يتحقق من بيانات المنتج ويحفظه مملوكًا لحساب البائع الحالي.
    public function storeProduct(Request $request): RedirectResponse
    {
        $product = $this->validateProduct($request);
        $slug = 'piece-'.Str::lower(Str::random(8));
        $profile = $this->sellerProfile($request);
        $request->user()->products()->create($this->normalizeProduct($product, $slug, $profile, $request->user()->id));

        return redirect()->route('seller.products.index')->with('status', 'تمت إضافة المنتج إلى قائمة متجرك.');
    }

    // يعرض نموذج تعديل منتج بعد التحقق من ملكية البائع له.
    public function editProduct(Request $request, string $slug): View
    {
        $product = $this->findSellerProduct($request, $slug);
        abort_if($product === null, 404);

        return view('seller.products.form', [
            'product' => $product,
            'categories' => $this->catalog->categories(),
            'profile' => $this->sellerProfile($request),
        ]);
    }

    // يحدث منتجا موجودا بعد التحقق من صلاحيته وملكيته للبائع.
    public function updateProduct(Request $request, string $slug): RedirectResponse
    {
        $existing = $this->findSellerProduct($request, $slug);
        abort_if($existing === null, 404);

        $product = $this->validateProduct($request);
        $productRecord = $request->user()->products()->where('slug', $slug)->firstOrFail();
        $productRecord->update($this->normalizeProduct($product, $slug, $this->sellerProfile($request), $request->user()->id));

        return redirect()->route('seller.products.index')->with('status', 'تم تحديث بيانات المنتج.');
    }

    // يعرض الطلبات التي تحتوي عناصر تعود للبائع الحالي.
    public function orders(Request $request): View
    {
        return view('seller.orders.index', ['orders' => $this->sellerOrders($request)]);
    }

    // يعرض تفاصيل طلب البائع ويطبق قائمة الحالات المسموحة.
    public function orderDetails(Request $request, string $reference): View
    {
        $order = $this->sellerOrders($request)[$reference] ?? null;
        abort_if($order === null, 404);

        return view('seller.orders.show', [
            'order' => $order,
            'statuses' => self::ORDER_STATUSES,
            'nextStatus' => $this->nextStatus($order['status']),
        ]);
    }

    // ينقل حالة الطلب إلى الحالة التالية المسموحة ويخزن تاريخ التحول.
    public function updateOrderStatus(Request $request, string $reference): RedirectResponse
    {
        $order = $this->sellerOrders($request)[$reference] ?? null;
        abort_if($order === null, 404);

        $nextStatus = $this->nextStatus($order['status']);
        abort_if($nextStatus === null, 422);
        $validated = $request->validate(['status' => ['required', 'in:'.$nextStatus]]);

        $persistedOrder = Order::query()->where('reference', $reference)->firstOrFail();
        $history = $persistedOrder->history;
        $history[] = ['status' => $validated['status'], 'at' => now()->format('Y-m-d H:i')];
        $persistedOrder->update(['status' => $validated['status'], 'history' => $history]);

        return redirect()->route('seller.orders.show', $reference)->with('status', 'تم تحديث حالة الطلب.');
    }

    // يعرض بيانات ملف المتجر المعتمد أو نموذج البيانات المؤقتة.
    public function profile(Request $request): View
    {
        return view('seller.profile', ['profile' => $this->sellerProfile($request)]);
    }

    // يحدث ملف المتجر المعتمد ويحفظه في طلب الانضمام.
    public function updateProfile(Request $request): RedirectResponse
    {
        $profile = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'craft' => ['required', 'string', 'max:100'],
            'governorate' => ['required', 'string', 'max:80'],
            'city' => ['required', 'string', 'max:100'],
            'about' => ['required', 'string', 'min:20', 'max:600'],
            'phone' => ['required', 'string', 'min:7', 'max:20', 'regex:/^[0-9+()\-\s]+$/'],
            'logo_url' => ['nullable', 'url', 'max:500'],
            'banner_url' => ['nullable', 'url', 'max:500'],
            'website' => ['nullable', 'url', 'max:255'],
        ]);
        $application = $request->user()->sellerApplication;
        if ($application?->status === 'approved') {
            $application->update([
                'shop_name' => $profile['name'],
                'craft' => $profile['craft'],
                'governorate' => $profile['governorate'],
                'city' => $profile['city'],
                'about' => $profile['about'],
                'phone' => $profile['phone'],
                'logo_url' => $profile['logo_url'] ?? null,
                'banner_url' => $profile['banner_url'] ?? null,
                'website' => $profile['website'] ?? null,
            ]);
        } else {
            $request->session()->put('seller_profile', $profile);
        }

        return redirect()->route('seller.profile')->with('status', 'تم تحديث ملف المشروع.');
    }

    // يجلب منتجات البائع من قاعدة البيانات ويجهزها للعرض.
    private function sellerProducts(Request $request): array
    {
        return $request->user()->products()->latest('id')->get()
            ->map(fn (Product $product): array => $product->toViewData())->all();
    }

    // يبحث عن منتج لبائع واحد ولا يسمح بالوصول لمنتجات بائع آخر.
    private function findSellerProduct(Request $request, string $slug): ?array
    {
        foreach ($this->sellerProducts($request) as $product) {
            if ($product['slug'] === $slug) {
                return $product;
            }
        }

        return null;
    }

    // يتحقق من حقول المنتج والسعر والصورة والمخزون والقيم المسموحة.
    private function validateProduct(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'category' => ['required', 'string', 'in:'.implode(',', array_column($this->catalog->categories(), 'name'))],
            'price' => ['required', 'integer', 'min:1', 'max:100000000'],
            'city' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'min:10', 'max:500'],
            'image' => ['required', 'url', 'max:500'],
            'stock' => ['required', 'integer', 'min:0', 'max:100000'],
        ]);
    }

    // يجهز بيانات المنتج للحفظ ويربطه بمالك المتجر وبياناته الأساسية.
    private function normalizeProduct(array $product, string $slug, array $profile, int $sellerId): array
    {
        $product['slug'] = $slug;
        $product['artisan'] = $profile['name'];
        $product['artisan_slug'] = 'seller-'.$sellerId;
        $product['rating'] = 'جديد';
        $product['story'] = $product['description'];
        $product['seller_user_id'] = $sellerId;

        return $product;
    }

    // يجمع طلبات المنتجات التي تخص البائع ويجهز الإجماليات التابعة له.
    private function sellerOrders(Request $request): array
    {
        $sellerOrders = [];

        foreach (Order::query()->latest()->get() as $persistedOrder) {
            $order = $persistedOrder->toViewData();
            $items = array_values(array_filter(
                $order['items'],
                fn (array $item): bool => $request->user()->is_admin
                    || (int) ($item['seller_user_id'] ?? 0) === (int) $request->user()->id,
            ));
            if ($items === []) {
                continue;
            }

            $order['items'] = $items;
            $order['subtotal'] = array_sum(array_map(fn (array $item): int => $item['price'] * $item['quantity'], $items));
            $order['total'] = $order['subtotal'];
            $sellerOrders[$order['reference']] = $order;
        }

        return $sellerOrders;
    }

    // يجلب بيانات المتجر من طلب الانضمام المعتمد أو الجلسة المؤقتة.
    private function sellerProfile(Request $request): array
    {
        $application = $request->user()->sellerApplication;
        if ($application?->status === 'approved') {
            return [
                'name' => $application->shop_name,
                'craft' => $application->craft,
                'governorate' => $application->governorate,
                'city' => $application->city,
                'about' => $application->about,
                'phone' => $application->phone,
                'logo_url' => $application->logo_url,
                'banner_url' => $application->banner_url,
                'website' => $application->website,
            ];
        }

        return $request->session()->get('seller_profile', [
            'name' => $request->user()->name,
            'craft' => 'فخار وخزف يدوي',
            'governorate' => 'أمانة العاصمة',
            'city' => 'صنعاء القديمة',
            'about' => 'ورشة عائلية تعمل على تشكيل قطع خزفية للاستخدام اليومي، مع اهتمام بالتفاصيل والألوان الهادئة.',
            'phone' => '+967 7XX XXX XXX',
            'logo_url' => null,
            'banner_url' => null,
            'website' => null,
        ]);
    }

    // يحسب الحالة التالية في سلسلة حالات تجهيز الطلب.
    private function nextStatus(string $current): ?string
    {
        $index = array_search($current, self::ORDER_STATUSES, true);

        return $index === false ? self::ORDER_STATUSES[0] : (self::ORDER_STATUSES[$index + 1] ?? null);
    }

    // يحسب مبيعات الأيام الماضية لتقرير لوحة البائع.
    private function dailySales(array $orders): array
    {
        return array_map(function (int $daysAgo) use ($orders): array {
            $date = now()->subDays($daysAgo);
            $dateKey = $date->format('Y-m-d');
            $sales = array_sum(array_map(
                fn (array $order): int => $order['status'] === 'تم التسليم' && substr($order['created_at'] ?? '', 0, 10) === $dateKey ? $order['total'] : 0,
                $orders,
            ));

            return ['label' => $date->format('d/m'), 'sales' => $sales];
        }, range(6, 0));
    }
}

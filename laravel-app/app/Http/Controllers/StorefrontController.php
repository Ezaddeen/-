<?php

namespace App\Http\Controllers;

// يضم هذا المتحكم صفحات المتجر العامة والسلة واكتشاف المنتجات والحرفيين.

use App\Models\Product;
use App\Models\SellerApplication;
use App\Support\DemoCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StorefrontController extends Controller
{
    // يجهز الصفحة الرئيسية ببيانات التصنيفات والمنتجات والحرفي المختار.
    public function __construct(private readonly DemoCatalog $catalog) {}

    public function home(): View
    {
        return view('storefront.home', [
            'categories' => $this->categories(),
            'products' => Product::query()->where('is_hidden', false)->orderBy('id')->limit(4)->get()
                ->map(fn (Product $product): array => $product->toViewData())->all(),
            'artisan' => $this->catalog->artisans()[0],
        ]);
    }

    // يعرض كتالوج المنتجات ويطبق البحث والتصنيف والترتيب المطلوبين.
    public function products(Request $request): View
    {
        $products = Product::query()->where('is_hidden', false);
        $query = trim((string) $request->query('q', ''));
        $category = trim((string) $request->query('category', ''));
        $sort = (string) $request->query('sort', 'featured');

        if ($query !== '') {
            $products->where(fn ($builder) => $builder
                ->where('name', 'like', "%{$query}%")
                ->orWhere('artisan', 'like', "%{$query}%")
                ->orWhere('city', 'like', "%{$query}%"));
        }

        if ($category !== '') {
            $products->where('category', $category);
        }

        if ($sort === 'price') {
            $products->orderBy('price');
        } else {
            $products->orderBy('id');
        }

        return view('storefront.products.index', [
            'products' => $products->get()->map(fn (Product $product): array => $product->toViewData())->all(),
            'categories' => $this->categories(),
            'query' => $query,
            'selectedCategory' => $category,
            'sort' => $sort,
        ]);
    }

    // يعرض تفاصيل منتج واحد ويمنع عرض المنتج المخفي إداريا.
    public function product(string $slug): View
    {
        $product = Product::query()->where('slug', $slug)->where('is_hidden', false)->firstOrFail()->toViewData();

        return view('storefront.products.show', compact('product'));
    }

    // يعرض صفحة الحرفي أو المتجر المعتمد وجميع منتجاته المرتبطة به.
    public function artisan(string $slug): View
    {
        $artisan = $this->catalog->findArtisan($slug);
        $storeApplication = SellerApplication::query()->where('store_slug', $slug)->where('status', 'approved')->first();
        $artisanSlug = $storeApplication ? 'seller-'.$storeApplication->user_id : $slug;
        $products = Product::query()->where('artisan_slug', $artisanSlug)->orderBy('id')->get()
            ->map(fn (Product $product): array => $product->toViewData())->all();
        abort_if($artisan === null && $products === [], 404);
        $artisan ??= [
            'slug' => $slug,
            'name' => $storeApplication?->shop_name ?? $products[0]['artisan'],
            'craft' => $storeApplication?->craft ?? $products[0]['category'],
            'city' => $storeApplication?->city ?? $products[0]['city'],
            'image' => $storeApplication?->logo_url ?? $products[0]['image'],
            'story' => $storeApplication?->about ?? $products[0]['story'],
        ];

        return view('storefront.artisans.show', compact('artisan', 'products'));
    }

    // يعرض محتويات سلة الجلسة مع الأسعار والإجمالي الحالي.
    public function cart(Request $request): View
    {
        $quantities = $request->session()->get('cart_items', []);
        $items = [];

        foreach ($quantities as $slug => $quantity) {
            $product = Product::query()->where('slug', (string) $slug)->first();
            if ($product !== null) {
                $items[] = [...$product->toViewData(), 'quantity' => (int) $quantity];
            }
        }

        return view('storefront.cart', [
            'items' => $items,
            'total' => array_sum(array_map(fn (array $item): int => $item['price'] * $item['quantity'], $items)),
        ]);
    }

    // يضيف منتجا إلى السلة ويجمع الكمية إن كان موجودا مسبقا.
    public function addToCart(Request $request, string $slug): RedirectResponse
    {
        abort_if(! Product::query()->where('slug', $slug)->exists(), 404);

        $validated = $request->validate(['quantity' => ['nullable', 'integer', 'min:1', 'max:20']]);
        $cart = $request->session()->get('cart_items', []);
        $cart[$slug] = min(20, ($cart[$slug] ?? 0) + ($validated['quantity'] ?? 1));
        $request->session()->put('cart_items', $cart);

        return redirect()->route('cart.show')->with('status', 'أُضيف المنتج إلى سلتك.');
    }

    // يحذف منتجا محددا من سلة الجلسة.
    public function removeFromCart(Request $request, string $slug): RedirectResponse
    {
        $cart = $request->session()->get('cart_items', []);
        unset($cart[$slug]);
        $request->session()->put('cart_items', $cart);

        return redirect()->route('cart.show')->with('status', 'حُذف المنتج من السلة.');
    }

    // يحدّث كمية منتج في السلة ويتحقق من المخزون المسجل.
    public function updateCartQuantity(Request $request, string $slug): RedirectResponse
    {
        $product = Product::query()->where('slug', $slug)->firstOrFail();
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:'.$product->stock],
        ]);
        $cart = $request->session()->get('cart_items', []);
        $cart[$slug] = $validated['quantity'];
        $request->session()->put('cart_items', $cart);

        return redirect()->route('cart.show')->with('status', 'تم تحديث الكمية.');
    }

    // يعرض صفحة تسجيل الدخول الموحدة لجميع أدوار النظام.
    public function login(): View
    {
        return view('auth.login');
    }

    // يعرض نموذج إنشاء حساب عميل جديد.
    public function register(): View
    {
        return view('auth.register');
    }

    // يحسب التصنيفات وأعداد منتجاتها ويجهزها لشريط الواجهة والبحث.
    private function categories(): array
    {
        $counts = Product::query()
            ->selectRaw('category, COUNT(*) as product_count')
            ->groupBy('category')
            ->pluck('product_count', 'category');

        return array_map(
            fn (array $category): array => [...$category, 'count' => (int) ($counts[$category['name']] ?? 0)],
            $this->catalog->categories(),
        );
    }
}

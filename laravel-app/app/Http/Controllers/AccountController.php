<?php

namespace App\Http\Controllers;

// يدير هذا المتحكم حساب العميل والعناوين والمفضلة والطلب والدفع والتقييم.

use App\Models\AuditLog;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AccountController extends Controller
{
    // يعرض نظرة عامة لحساب العميل مع العناوين والطلبات المرتبطة به.
    public function dashboard(Request $request): View
    {
        return view('customer.dashboard', [
            'profile' => $this->profileData($request),
            'addresses' => $request->user()->addresses()->orderByDesc('is_default')->latest()->get()
                ->map(fn ($address): array => $address->toViewData())->all(),
            'orders' => $request->user()->orders()->latest()->get()->map(fn ($order): array => $order->toViewData())->all(),
        ]);
    }

    // يعرض بيانات الملف الشخصي الحالية للمستخدم.
    public function profile(Request $request): View
    {
        return view('customer.profile', ['profile' => $this->profileData($request)]);
    }

    // يتحقق من بيانات الحساب ويحفظ الاسم والبريد ورقم الهاتف.
    public function updateProfile(Request $request): RedirectResponse
    {
        $profile = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'phone' => ['required', 'string', 'min:7', 'max:20', 'regex:/^[0-9+()\-\s]+$/'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($request->user()->id)],
        ]);

        $request->user()->update([
            'name' => $profile['name'],
            'email' => $profile['email'],
            'phone' => $profile['phone'],
        ]);

        return redirect()->route('account.profile')->with('status', 'تم تحديث بيانات الملف الشخصي.');
    }

    // يعرض جميع عناوين التوصيل الخاصة بالمستخدم.
    public function addresses(Request $request): View
    {
        return view('customer.addresses', [
            'addresses' => $request->user()->addresses()->orderByDesc('is_default')->latest()->get()
                ->map(fn ($address): array => $address->toViewData())->all(),
        ]);
    }

    // يضيف عنوان توصيل جديد ويجعل أول عنوان عنوانا افتراضيا.
    public function addAddress(Request $request): RedirectResponse
    {
        $address = $request->validate([
            'label' => ['required', 'string', 'max:40'],
            'governorate' => ['required', 'string', 'max:80'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['required', 'string', 'max:100'],
            'landmark' => ['required', 'string', 'max:180'],
            'phone' => ['required', 'string', 'min:7', 'max:20', 'regex:/^[0-9+()\-\s]+$/'],
            'note' => ['nullable', 'string', 'max:250'],
        ]);

        DB::transaction(function () use ($request, $address): void {
            $user = $request->user()->newQuery()->lockForUpdate()->findOrFail($request->user()->id);
            $user->addresses()->create([
                ...$address,
                'is_default' => ! $user->addresses()->exists(),
            ]);
        });

        return redirect()->route('account.addresses')->with('status', 'تمت إضافة العنوان.');
    }

    // يحدث عنوان توصيل بعد التحقق من ملكية المستخدم له.
    public function updateAddress(Request $request, string $id): RedirectResponse
    {
        $address = $request->validate([
            'label' => ['required', 'string', 'max:40'],
            'governorate' => ['required', 'string', 'max:80'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['required', 'string', 'max:100'],
            'landmark' => ['required', 'string', 'max:180'],
            'phone' => ['required', 'string', 'min:7', 'max:20', 'regex:/^[0-9+()\-\s]+$/'],
            'note' => ['nullable', 'string', 'max:250'],
        ]);
        $request->user()->addresses()->findOrFail($id)->update($address);

        return redirect()->route('account.addresses')->with('status', 'تم تحديث العنوان.');
    }

    // يعين عنوانا واحدا افتراضيا ويزيل الافتراضية عن بقية العناوين.
    public function setDefaultAddress(Request $request, string $id): RedirectResponse
    {
        DB::transaction(function () use ($request, $id): void {
            $user = $request->user()->newQuery()->lockForUpdate()->findOrFail($request->user()->id);
            $address = $user->addresses()->findOrFail($id);
            $user->addresses()->where('is_default', true)->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });

        return redirect()->route('account.addresses')->with('status', 'تم تعيين العنوان الافتراضي.');
    }

    // يحذف عنوانا ويعوض عنوانا افتراضيا إن كان المحذوف هو الافتراضي.
    public function deleteAddress(Request $request, string $id): RedirectResponse
    {
        DB::transaction(function () use ($request, $id): void {
            $user = $request->user()->newQuery()->lockForUpdate()->findOrFail($request->user()->id);
            $addresses = $user->addresses();
            $address = $addresses->findOrFail($id);
            $wasDefault = $address->is_default;
            $replacement = $wasDefault
                ? $user->addresses()->where('id', '<>', $address->id)->oldest('id')->first()
                : null;
            $address->delete();

            if ($replacement !== null) {
                $replacement->update(['is_default' => true]);
            }
        });

        return redirect()->route('account.addresses')->with('status', 'تم حذف العنوان.');
    }

    // يعرض منتجات المفضلة المحفوظة في قاعدة بيانات المستخدم.
    public function favorites(): View
    {
        $products = request()->user()->favoriteProducts()->orderBy('products.id')->get()
            ->map(fn (Product $product): array => $product->toViewData())->all();

        return view('customer.favorites', compact('products'));
    }

    // يضيف المنتج للمفضلة أو يحذفه حسب حالته الحالية للمستخدم.
    public function toggleFavorite(Request $request, string $slug): RedirectResponse
    {
        $product = Product::query()->where('slug', $slug)->firstOrFail();
        $attached = $request->user()->favoriteProducts()->whereKey($product->id)->exists();
        if ($attached) {
            $request->user()->favoriteProducts()->detach($product);
            $status = 'حُذف المنتج من المفضلة.';
        } else {
            $request->user()->favoriteProducts()->attach($product);
            $status = 'حُفظ المنتج في المفضلة.';
        }

        return back()->with('status', $status);
    }

    // يعرض صفحة إتمام الطلب بعد التحقق من وجود سلة وعنوان.
    public function checkout(Request $request): View|RedirectResponse
    {
        $items = $this->cartItems($request);
        if ($items === []) {
            return redirect()->route('cart.show')->with('status', 'أضف المنتجات إلى سلتك قبل إتمام الطلب.');
        }

        $addresses = $request->user()->addresses()->orderByDesc('is_default')->latest()->get()
            ->map(fn ($address): array => $address->toViewData())->all();
        if ($addresses === []) {
            return redirect()->route('account.addresses')->with('status', 'أضف عنوان توصيل قبل إتمام الطلب.');
        }

        return view('customer.checkout', [
            'items' => $items,
            'subtotal' => $this->subtotal($items),
            'addresses' => $addresses,
            'deliveryFee' => in_array(
                collect($addresses)->firstWhere('is_default', true)['governorate'] ?? $addresses[0]['governorate'],
                ['أمانة العاصمة', 'صنعاء'],
                true
            ) ? 2000 : 3500,
        ]);
    }

    // ينشئ الطلب بقفل المخزون ويحفظ لقطة المنتج والعنوان وإجمالي الشراء.
    public function placeOrder(Request $request): RedirectResponse
    {
        $items = $this->cartItems($request);
        if ($items === []) {
            return redirect()->route('cart.show')->with('status', 'أضف المنتجات إلى سلتك قبل إتمام الطلب.');
        }

        $validated = $request->validate([
            'address_id' => ['required', 'integer', Rule::exists('addresses', 'id')->where(
                fn (QueryBuilder $query) => $query->where('user_id', $request->user()->id),
            )],
            'payment_method' => ['required', 'in:cash_on_delivery,contact_seller'],
            'customer_note' => ['nullable', 'string', 'max:300'],
        ]);
        $address = $request->user()->addresses()->findOrFail($validated['address_id'])->toViewData();

        $reference = 'BA-'.now()->format('ymd').'-'.Str::upper(Str::random(5));
        $paymentMethod = $validated['payment_method'] === 'cash_on_delivery' ? 'الدفع عند الاستلام' : 'التنسيق مع المتجر';

        DB::transaction(function () use ($request, $reference, $paymentMethod, $items, $address, $validated): void {
            $orderItems = [];

            foreach ($items as $item) {
                $product = Product::query()->where('slug', $item['slug'])->lockForUpdate()->firstOrFail();
                $quantity = $item['quantity'];
                if ($quantity < 1 || $quantity > $product->stock) {
                    throw ValidationException::withMessages([
                        'cart' => 'الكمية المطلوبة من '.$product->name.' غير متوفرة.',
                    ]);
                }

                $orderItems[] = [...$product->toViewData(), 'quantity' => $quantity];
                $product->decrement('stock', $quantity);
            }

            $subtotal = $this->subtotal($orderItems);
            $deliveryFee = in_array($address['governorate'], ['أمانة العاصمة', 'صنعاء'], true) ? 2000 : 3500;
            $request->user()->orders()->create([
                'reference' => $reference,
                'status' => 'بانتظار قبول البائع',
                'payment_method' => $paymentMethod,
                'payment_status' => $validated['payment_method'] === 'cash_on_delivery' ? 'عند الاستلام' : 'بانتظار التواصل',
                'shipment_status' => 'لم يتم الشحن',
                'customer_note' => $validated['customer_note'] ?? null,
                'items' => $orderItems,
                'address' => $address,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total' => $subtotal + $deliveryFee,
                'history' => [
                    ['status' => 'تم إرسال الطلب', 'at' => now()->format('Y-m-d H:i')],
                    ['status' => 'تم حساب رسوم التوصيل', 'at' => now()->format('Y-m-d H:i')],
                ],
            ]);
        });

        $request->session()->forget('cart_items');

        return redirect()->route('orders.confirmation', $reference);
    }

    // يعرض قائمة طلبات المستخدم مرتبة بالأحدث.
    public function orders(Request $request): View
    {
        $orders = $request->user()->orders()->latest()->get()->map(fn ($order): array => $order->toViewData())->all();

        return view('customer.orders.index', compact('orders'));
    }

    // يعرض تفاصيل طلب واحد بعد التحقق من ملكية المستخدم له.
    public function orderDetails(Request $request, string $reference): View
    {
        $order = $request->user()->orders()->where('reference', $reference)->firstOrFail()->toViewData();

        return view('customer.orders.show', compact('order'));
    }

    // يعرض تأكيد إنشاء الطلب بعد إتمام الحفظ.
    public function orderConfirmation(Request $request, string $reference): View
    {
        $order = $request->user()->orders()->where('reference', $reference)->firstOrFail()->toViewData();

        return view('customer.orders.confirmation', compact('order'));
    }

    // يلغي الطلب قبل قبوله ويعيد الكميات المحجوزة للمخزون.
    public function cancelOrder(Request $request, string $reference): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:300'],
        ]);

        $order = $request->user()->orders()->where('reference', $reference)->firstOrFail();
        abort_unless(in_array($order->status, ['بانتظار قبول البائع', 'مقبول'], true), 422);

        DB::transaction(function () use ($order, $validated): void {
            $locked = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();
            foreach ($locked->items as $item) {
                Product::query()->where('slug', $item['slug'])->increment('stock', $item['quantity']);
            }

            $history = $locked->history;
            $history[] = ['status' => 'ملغى من العميل', 'at' => now()->format('Y-m-d H:i')];
            $locked->update([
                'status' => 'ملغى',
                'cancel_reason' => $validated['reason'],
                'history' => $history,
            ]);
        });

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'cancel_order',
            'entity_type' => Order::class,
            'entity_id' => $order->id,
            'description' => 'ألغى العميل الطلب '.$reference.': '.$validated['reason'],
        ]);

        return redirect()->route('account.orders.show', $reference)->with('status', 'تم إلغاء الطلب وإعادة الكميات إلى المخزون.');
    }

    // يحفظ تقييم عميل لمنتج داخل طلب تسلم بالفعل.
    public function addReview(Request $request, string $reference): RedirectResponse
    {
        $order = $request->user()->orders()->where('reference', $reference)->firstOrFail();
        abort_unless($order->status === 'تم التسليم', 422);

        $validated = $request->validate([
            'product_slug' => ['required', 'string'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:600'],
        ]);

        $item = collect($order->items)->firstWhere('slug', $validated['product_slug']);
        abort_if($item === null, 422);

        $product = Product::query()->where('slug', $validated['product_slug'])->firstOrFail();
        Review::updateOrCreate([
            'user_id' => $request->user()->id,
            'product_id' => $product->id,
            'order_id' => $order->id,
        ], [
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return redirect()->route('account.orders.show', $reference)->with('status', 'تم حفظ تقييمك.');
    }

    // يحول عناصر سلة الجلسة إلى بيانات منتجات مفصلة مع الكميات.
    private function cartItems(Request $request): array
    {
        $items = [];

        foreach ($request->session()->get('cart_items', []) as $slug => $quantity) {
            $product = Product::query()->where('slug', (string) $slug)->first();
            if ($product !== null) {
                $items[] = [...$product->toViewData(), 'quantity' => (int) $quantity];
            }
        }

        return $items;
    }

    // يحسب قيمة المنتجات فقط قبل إضافة رسوم التوصيل.
    private function subtotal(array $items): int
    {
        return array_sum(array_map(fn (array $item): int => $item['price'] * $item['quantity'], $items));
    }

    // يجمع بيانات الملف الشخصي بصيغة مقروءة لقوالب حساب العميل.
    private function profileData(Request $request): array
    {
        return [
            'name' => $request->user()->name,
            'phone' => $request->user()->phone ?? '',
            'email' => $request->user()->email,
        ];
    }
}

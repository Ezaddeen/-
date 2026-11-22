<?php

namespace App\Http\Controllers;

// يدير هذا المتحكم طلب إنشاء متجر وحالة المراجعة وقرار الاعتماد أو الرفض.

use App\Models\SellerApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SellerApplicationController extends Controller
{
    // يعرض حالة طلب المتجر للمستخدم أو يحوله لمساحة البائع إذا اعتمد.
    public function show(Request $request): View|RedirectResponse
    {
        if ($request->user()->is_seller) {
            return redirect()->route('seller.dashboard');
        }

        return view('seller-applications.show', [
            'application' => $request->user()->sellerApplication,
            'governorates' => $this->governorates(),
        ]);
    }

    // يحفظ أو يحدث طلب إنشاء متجر ويتحقق من عدم تكرار الطلب المعلق.
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'shop_name' => ['required', 'string', 'min:2', 'max:100'],
            'store_slug' => ['nullable', 'string', 'min:3', 'max:140', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('seller_applications', 'store_slug')->ignore($request->user()->sellerApplication?->id)],
            'craft' => ['required', 'string', 'max:100'],
            'governorate' => ['required', 'string', 'in:'.implode(',', $this->governorates())],
            'city' => ['required', 'string', 'max:100'],
            'about' => ['required', 'string', 'min:20', 'max:1000'],
            'phone' => ['required', 'string', 'min:7', 'max:20', 'regex:/^[0-9+()\-\s]+$/'],
            'logo_url' => ['nullable', 'url', 'max:500'],
            'banner_url' => ['nullable', 'url', 'max:500'],
            'website' => ['nullable', 'url', 'max:255'],
        ]);

        $validated['store_slug'] = $validated['store_slug'] ?? Str::slug($validated['shop_name']);

        DB::transaction(function () use ($request, $validated): void {
            $user = $request->user()->newQuery()->lockForUpdate()->findOrFail($request->user()->id);
            $application = $user->sellerApplication;

            if ($application?->status === 'pending') {
                throw ValidationException::withMessages([
                    'application' => 'طلبك قيد المراجعة بالفعل.',
                ]);
            }

            if ($application?->status === 'approved' || $user->is_seller) {
                throw ValidationException::withMessages([
                    'application' => 'تم اعتماد حسابك كبائع.',
                ]);
            }

            $storeSlug = $validated['store_slug'];
            if (SellerApplication::query()->where('store_slug', $storeSlug)->where('id', '<>', $application?->id ?? 0)->exists()) {
                $storeSlug .= '-'.$user->id;
            }

            $user->sellerApplication()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    ...$validated,
                    'store_slug' => $storeSlug,
                    'status' => 'pending',
                    'review_note' => null,
                    'reviewed_by' => null,
                    'reviewed_at' => null,
                ],
            );
        });

        return redirect()->route('seller-application.show')
            ->with('status', 'تم إرسال طلبك. سنراجعه ونعلمك بالنتيجة.');
    }

    // يعيد قائمة محافظات التغطية المتاحة عند تقديم طلب المتجر.
    private function governorates(): array
    {
        return ['أمانة العاصمة', 'صنعاء', 'عدن', 'تعز', 'إب', 'الحديدة', 'حضرموت', 'ذمار', 'لحج', 'أبين', 'شبوة', 'مأرب'];
    }
}

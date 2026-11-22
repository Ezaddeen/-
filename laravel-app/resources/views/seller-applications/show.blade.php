@extends('layouts.storefront')
{{-- يعرض طلب إنشاء متجر وحالات المراجعة والنموذج والحقول المطلوبة. --}}

@section('title', 'ابدأ البيع | بصمة الحرفيين')

@section('content')
<section class="seller-heading shell">
    <div><span class="eyebrow">أنشئ متجرك في بصمة الحرفيين</span><h1>قدّم <em>متجرك</em></h1><p>حدّد هوية المتجر والمكان الذي يصنع منه، وسيراجع فريق الإدارة الطلب قبل تفعيل مساحة البيع.</p></div>
</section>
<section class="seller-application-page shell">
    @unless ($application?->status === 'pending')
        <div class="seller-application-steps" aria-label="خطوات إنشاء المتجر">
            <div><span>1</span><strong>بيانات المتجر</strong><small>الاسم والحرفة والمنطقة</small></div>
            <div><span>2</span><strong>مراجعة الإدارة</strong><small>اعتماد الطلب أو طلب تعديل</small></div>
            <div><span>3</span><strong>بدء البيع</strong><small>إضافة المنتجات وإدارة الطلبات</small></div>
        </div>
    @endunless
    @if ($application?->status === 'pending')
        <div class="seller-application-status" role="status">
            <span class="seller-application-status-mark">…</span>
            <div><h2>طلبك قيد المراجعة</h2><p>استلمنا طلب «{{ $application->shop_name }}». سنحدّث حالة الطلب بعد مراجعته.</p></div>
        </div>
    @elseif ($application?->status === 'rejected')
        <div class="seller-application-status seller-application-status-rejected" role="alert">
            <span class="seller-application-status-mark">!</span>
            <div><h2>نحتاج معلومات إضافية</h2><p>{{ $application->review_note }}</p><small>يمكنك تعديل البيانات وإعادة إرسال الطلب.</small></div>
        </div>
    @endif

    @if (!$application || $application->status === 'rejected')
        <div class="seller-form-panel seller-application-form-panel">
            <form class="seller-form" action="{{ route('seller-application.store') }}" method="POST">
                @csrf
                <div class="seller-form-grid">
                    <div class="seller-field seller-field-wide"><label for="shop_name">اسم المشروع أو المتجر</label><input id="shop_name" name="shop_name" value="{{ old('shop_name', $application?->shop_name) }}" required minlength="2" maxlength="100">@error('shop_name')<small class="field-error">{{ $message }}</small>@enderror</div>
                    <div class="seller-field seller-field-wide"><label for="store_slug">عنوان المتجر بالإنجليزية <span>اختياري</span></label><input id="store_slug" name="store_slug" value="{{ old('store_slug', $application?->store_slug) }}" pattern="[a-z0-9]+(?:-[a-z0-9]+)*" placeholder="warshat-sanaani">@error('store_slug')<small class="field-error">{{ $message }}</small>@enderror</div>
                    <div class="seller-field"><label for="logo_url">رابط شعار المتجر <span>اختياري</span></label><input id="logo_url" name="logo_url" type="url" value="{{ old('logo_url', $application?->logo_url) }}" maxlength="500">@error('logo_url')<small class="field-error">{{ $message }}</small>@enderror</div>
                    <div class="seller-field"><label for="banner_url">رابط صورة غلاف المتجر <span>اختياري</span></label><input id="banner_url" name="banner_url" type="url" value="{{ old('banner_url', $application?->banner_url) }}" maxlength="500">@error('banner_url')<small class="field-error">{{ $message }}</small>@enderror</div>
                    <div class="seller-field seller-field-wide"><label for="website">رابط تواصل أو موقع إضافي <span>اختياري</span></label><input id="website" name="website" type="url" value="{{ old('website', $application?->website) }}" maxlength="255">@error('website')<small class="field-error">{{ $message }}</small>@enderror</div>
                    <div class="seller-field"><label for="craft">الحرفة أو النشاط</label><input id="craft" name="craft" value="{{ old('craft', $application?->craft) }}" required maxlength="100">@error('craft')<small class="field-error">{{ $message }}</small>@enderror</div>
                    <div class="seller-field"><label for="phone">رقم التواصل</label><input id="phone" name="phone" value="{{ old('phone', $application?->phone ?? auth()->user()->phone) }}" inputmode="tel" required minlength="7" maxlength="20">@error('phone')<small class="field-error">{{ $message }}</small>@enderror</div>
                    <div class="seller-field"><label for="governorate">المحافظة</label><select id="governorate" name="governorate" required><option value="">اختر المحافظة</option>@foreach ($governorates as $governorate)<option value="{{ $governorate }}" @selected(old('governorate', $application?->governorate) === $governorate)>{{ $governorate }}</option>@endforeach</select>@error('governorate')<small class="field-error">{{ $message }}</small>@enderror</div>
                    <div class="seller-field"><label for="city">المدينة أو المديرية</label><input id="city" name="city" value="{{ old('city', $application?->city) }}" required maxlength="100">@error('city')<small class="field-error">{{ $message }}</small>@enderror</div>
                    <div class="seller-field seller-field-wide"><label for="about">عرّفنا بمشروعك ومنتجاتك</label><textarea id="about" name="about" rows="5" minlength="20" maxlength="1000" required>{{ old('about', $application?->about) }}</textarea>@error('about')<small class="field-error">{{ $message }}</small>@enderror</div>
                </div>
                @error('application')<p class="field-error" role="alert">{{ $message }}</p>@enderror
                <div class="seller-form-actions"><button class="button button-dark" type="submit">إرسال طلب الانضمام</button></div>
            </form>
        </div>
    @endif
</section>
@endsection

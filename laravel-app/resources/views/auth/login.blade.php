@extends('layouts.storefront')
{{-- نموذج دخول موحد يحفظ البريد وكلمة المرور ويعيد توجيه المستخدم حسب دوره. --}}

@section('title', 'تسجيل الدخول | بصمة الحرفيين')

@section('content')
<section class="auth-shell shell">
	<div class="auth-art"><img src="https://images.unsplash.com/photo-1493106641515-6b5631de4bb9?auto=format&fit=crop&w=1200&q=85" alt="تفاصيل من حرفة يدوية"><div class="auth-art-copy"><span class="eyebrow">أهلًا بعودتك</span><h1>مكانك محفوظ<br>بين <em>أهل الحرفة.</em></h1><p>تابع القطع التي أحببتها وحكايات صُنّاعها.</p></div></div>
	<div class="auth-panel"><span class="eyebrow">حساب بصمة الحرفيين</span><h2>تسجيل الدخول</h2><p>أدخل بيانات حسابك للمتابعة.</p><form class="auth-form" method="POST" action="{{ route('login.store') }}">@csrf<label for="email">البريد الإلكتروني</label><input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" placeholder="name@example.com" required>@error('email')<span class="field-error">{{ $message }}</span>@enderror<label for="password">كلمة المرور</label><input id="password" name="password" type="password" autocomplete="current-password" placeholder="••••••••" required>@error('password')<span class="field-error">{{ $message }}</span>@enderror<button class="button button-dark" type="submit">دخول إلى حسابي <span>←</span></button></form><p class="auth-switch">ليس لديك حساب؟ <a href="{{ route('register') }}">أنشئ حسابًا جديدًا</a></p></div>
</section>
@endsection
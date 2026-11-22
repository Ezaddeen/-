# تطبيق بصمة الحرفيين — Laravel

هذا المجلد يحتوي تطبيق Laravel الخاص بمنصة بصمة الحرفيين.

للتشغيل الكامل على جهاز جديد، اتبع دليل [تشغيل المشروع على Windows](../README.md#تشغيل-المشروع-على-جهاز-جديد--windows).

## التشغيل السريع

المتطلبات: PHP 8.2 أو أحدث، Composer، Node.js 20 أو أحدث، وامتداد SQLite لبيئة الاستخدام المحلية.

```powershell
composer install
npm install
Copy-Item .env.example .env
php artisan key:generate
New-Item -ItemType File -Force database/database.sqlite
php artisan migrate --seed
```

تشغيل خادمي التطوير في نافذتين منفصلتين:

```powershell
php artisan serve
```

```powershell
npm run dev
```

ثم افتح `http://127.0.0.1:8000`.

حساب المدير الافتراضي:

```text
البريد: admin@basmat-anamil.com
كلمة المرور: Admin12345!
```

لوحة الإدارة: `http://127.0.0.1:8000/admin`

## فحص المشروع

```powershell
php artisan test
vendor/bin/pint --test
npm run build
```

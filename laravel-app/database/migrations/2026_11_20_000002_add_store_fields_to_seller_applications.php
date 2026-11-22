<?php

// تضيف هذه الترحيلة هوية المتجر وعنوانه وشعار الغلاف والموقع الإضافي لطلب الانضمام.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seller_applications', function (Blueprint $table): void {
            $table->string('store_slug', 140)->nullable()->unique()->after('shop_name');
            $table->string('logo_url', 500)->nullable()->after('store_slug');
            $table->string('banner_url', 500)->nullable()->after('logo_url');
            $table->string('website', 255)->nullable()->after('banner_url');
        });
    }

    public function down(): void
    {
        Schema::table('seller_applications', function (Blueprint $table): void {
            $table->dropColumn(['store_slug', 'logo_url', 'banner_url', 'website']);
        });
    }
};

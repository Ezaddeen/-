<?php

// تنشئ هذه الترحيلة بيانات مراجعة المحتوى والشكاوى والإعدادات والمناطق وتضيف أعلام الإخفاء للمنتجات.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->boolean('is_hidden')->default(false)->after('stock');
            $table->boolean('is_reported')->default(false)->after('is_hidden');
            $table->string('moderation_note', 500)->nullable()->after('is_reported');
        });

        Schema::create('complaints', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subject', 150);
            $table->text('message');
            $table->string('status', 30)->default('جديد')->index();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_response')->nullable();
            $table->timestamps();
        });

        Schema::create('platform_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('regions', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 80)->unique();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regions');
        Schema::dropIfExists('platform_settings');
        Schema::dropIfExists('complaints');
        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn(['is_hidden', 'is_reported', 'moderation_note']);
        });
    }
};

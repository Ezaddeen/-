<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // ينشئ جدول عناوين التوصيل اليمنية ويربطه بالمستخدم.
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label', 40);
            $table->string('governorate', 80);
            $table->string('city', 100);
            $table->string('district', 100);
            $table->string('landmark', 180);
            $table->string('phone', 20);
            $table->string('note', 250)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'is_default']);
        });
    }

    // يحذف جدول العناوين عند التراجع.
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};

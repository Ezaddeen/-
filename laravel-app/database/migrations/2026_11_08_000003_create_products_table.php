<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('seller_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('name', 120);
            $table->string('category', 80);
            $table->unsignedBigInteger('price');
            $table->string('city', 100);
            $table->string('artisan', 120);
            $table->string('artisan_slug', 140)->index();
            $table->string('rating', 20)->default('جديد');
            $table->string('image', 500);
            $table->text('description');
            $table->text('story');
            $table->unsignedInteger('stock')->default(0);
            $table->timestamps();

            $table->index(['category', 'stock']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

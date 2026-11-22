<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // ينشئ جدول الطلبات وحقول لقطة الشراء والدفع والشحن.
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->string('status', 80);
            $table->string('payment_method', 80);
            $table->string('customer_note', 300)->nullable();
            $table->json('items');
            $table->json('address');
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('delivery_fee')->nullable();
            $table->unsignedBigInteger('total');
            $table->json('history');
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    // يحذف جدول الطلبات عند التراجع.
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

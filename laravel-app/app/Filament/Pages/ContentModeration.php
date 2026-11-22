<?php

namespace App\Filament\Pages;

// يتيح لمدير المنصة مراجعة المنتجات وإخفاء المحتوى المخالف أو إظهار المنتج مرة أخرى.
use App\Models\AuditLog;
use App\Models\Product;
use Filament\Pages\Page;

class ContentModeration extends Page
{
    protected static ?string $navigationLabel = 'المنتجات والمحتوى المخالف';

    protected static ?string $title = 'المنتجات والمحتوى المخالف';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.admin-moderation';

    public array $products = [];

    // يحضر قائمة المنتجات وحالة الإخفاء والبلاغ للمراجعة.
    public function mount(): void
    {
        $this->products = Product::query()->latest()->get()->map(fn (Product $product): array => [
            'id' => $product->id,
            'name' => $product->name,
            'category' => $product->category,
            'artisan' => $product->artisan,
            'hidden' => $product->is_hidden,
            'reported' => $product->is_reported,
            'note' => $product->moderation_note,
        ])->all();
    }

    // يخفي منتجا مخالفا أو يعيد إظهاره ويحفظ قرار الإدارة.
    public function toggleHidden(int $id): void
    {
        $product = Product::query()->findOrFail($id);
        $product->update(['is_hidden' => ! $product->is_hidden]);
        AuditLog::create(['user_id' => auth()->id(), 'action' => 'moderate_product', 'entity_type' => Product::class, 'entity_id' => $id, 'description' => ($product->is_hidden ? 'إخفاء' : 'إظهار').' منتج '.$product->name]);
        $this->mount();
    }
}

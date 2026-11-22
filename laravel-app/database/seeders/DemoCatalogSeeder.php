<?php

namespace Database\Seeders;

// يملأ كتالوج العرض ببيانات حقيقية للتنظيم دون استبدال السجلات الموجودة.
use App\Models\Product;
use App\Support\DemoCatalog;
use Illuminate\Database\Seeder;

class DemoCatalogSeeder extends Seeder
{
    // يضيف منتجات كتالوج العرض دون استبدال مخزون السجلات الموجودة.
    public function run(): void
    {
        foreach (app(DemoCatalog::class)->products() as $product) {
            Product::query()->firstOrCreate(
                ['slug' => $product['slug']],
                [...$product, 'seller_user_id' => null],
            );
        }
    }
}

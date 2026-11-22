<?php

namespace Database\Seeders;

// ينفذ جميع مزروعات المشروع الأساسية وبشكل مترتب.
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    // ينفذ مزروعات المدير والكتالوج وتصنيفات العرض وحساب المستخدم التجريبي.
    public function run(): void
    {
        $this->call(AdminUserSeeder::class);
        $this->call(DemoCatalogSeeder::class);

        foreach (['فخار وخزف', 'نسيج وتطريز', 'فضيات وحلي', 'بن وعسل', 'بخور وعطور'] as $index => $name) {
            Category::query()->updateOrCreate(
                ['name' => $name],
                ['slug' => Str::slug($name, '-').'-'.$index, 'is_active' => true, 'sort_order' => $index],
            );
        }

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}

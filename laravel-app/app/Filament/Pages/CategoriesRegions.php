<?php

namespace App\Filament\Pages;

// يدير التصنيفات والمناطق المستخدمة في تصفح المنصة وإعدادات المتاجر.
use App\Models\Category;
use App\Models\Region;
use Filament\Pages\Page;
use Illuminate\Support\Str;

class CategoriesRegions extends Page
{
    protected static ?string $navigationLabel = 'التصنيفات والمناطق';

    protected static ?string $title = 'التصنيفات والمناطق';

    protected static ?int $navigationSort = 6;

    protected string $view = 'filament.pages.admin-taxonomy';

    public array $categories = [];

    public array $regions = [];

    public string $categoryName = '';

    public string $regionName = '';

    // يحضر التصنيفات والمناطق وحالات تفعيلها.
    public function mount(): void
    {
        $this->categories = Category::query()->orderBy('sort_order')->get()->map(fn (Category $category): array => ['id' => $category->id, 'name' => $category->name, 'active' => $category->is_active])->all();
        $this->regions = Region::query()->orderBy('sort_order')->get()->map(fn (Region $region): array => ['id' => $region->id, 'name' => $region->name, 'active' => $region->is_active])->all();
    }

    // ينشئ تصنيفا جديدا إذا كان الاسم صالحا وغير فارغ.
    public function addCategory(): void
    {
        $name = trim($this->categoryName);
        abort_if($name === '', 422);
        Category::query()->updateOrCreate(['name' => $name], ['slug' => Str::slug($name).'-'.time(), 'is_active' => true]);
        $this->categoryName = '';
        $this->mount();
    }

    // يفعّل تصنيفا أو يعطله دون حذف سجل التصنيف.
    public function toggleCategory(int $id): void
    {
        $category = Category::query()->findOrFail($id);
        $category->update(['is_active' => ! $category->is_active]);
        $this->mount();
    }

    // ينشئ محافظة أو منطقة خدمة جديدة ويحفظها نشطة.
    public function addRegion(): void
    {
        $name = trim($this->regionName);
        abort_if($name === '', 422);
        Region::query()->updateOrCreate(['name' => $name], ['is_active' => true]);
        $this->regionName = '';
        $this->mount();
    }
}

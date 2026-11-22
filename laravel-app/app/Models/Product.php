<?php

namespace App\Models;

// يمثل هذا الصف منتجًا مع صورته وسعره ومخزونه وربطه بالحرفي الذي أنشأه.
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'seller_user_id',
        'slug',
        'name',
        'category',
        'price',
        'city',
        'artisan',
        'artisan_slug',
        'rating',
        'image',
        'description',
        'story',
        'stock',
        'is_hidden',
        'is_reported',
        'moderation_note',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'stock' => 'integer',
            'is_hidden' => 'boolean',
            'is_reported' => 'boolean',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_user_id');
    }

    public function toViewData(): array
    {
        return [
            'seller_user_id' => $this->seller_user_id,
            'slug' => $this->slug,
            'name' => $this->name,
            'category' => $this->category,
            'price' => $this->price,
            'city' => $this->city,
            'artisan' => $this->artisan,
            'artisan_slug' => $this->artisan_slug,
            'rating' => $this->rating,
            'image' => $this->image,
            'description' => $this->description,
            'story' => $this->story,
            'stock' => $this->stock,
        ];
    }
}

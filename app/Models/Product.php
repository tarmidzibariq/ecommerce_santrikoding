<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'image',
        'title',
        'slug',
        'category_id',
        'user_id',
        'description',
        'weight',
        'price',
        'stock',
        'discount'
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => asset('/storage/products/' . $value),
        );
    }

    public function reviewAvgRating(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? substr($value, 0, 3) : 0,
        );
    } 
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Category;
use App\Models\Image;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'feature',
        'name',
        'description',
        'price',
        'quantity',
        'category_id',
    ];

    public function category(): BelongsTo {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function images(): HasMany {
        return $this->hasMany(Image::class, 'product_id');
    }
}

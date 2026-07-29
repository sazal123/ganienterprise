<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'slug',
        'banner',
        'discount_tag',
        'start_date',
        'end_date',
        'status',
        'meta_title',
        'meta_description',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'offer_product', 'offer_id', 'product_id')
                    ->withPivot('custom_price', 'sort_order')
                    ->withTimestamps();
    }
}

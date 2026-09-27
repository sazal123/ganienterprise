<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];
    public function getRouteKeyName() {
        return 'slug';
    }
    public function image()
    {
        return $this->hasOne(Productimage::class, 'product_id')->select('id','image','product_id');
    }
    public function images()
    {
        return $this->hasMany(Productimage::class, 'product_id')->select('id','image','product_id','color_id');
    }
    public function mainImages()
    {
        return $this->hasMany(Productimage::class, 'product_id')->whereNull('color_id')->select('id','image','product_id','color_id');
    }
    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id')->select('id');
    }
    public function category()
    {
        return $this->hasOne(Category::class,'id','category_id')->select('id','name','slug');
    }
    public function subcategory()
    {
        return $this->hasOne(Subcategory::class,'id','subcategory_id')->select('id','subcategoryName','slug');
    }
    public function childcategory()
    {
        return $this->hasOne(Childcategory::class,'id','childcategory_id')->select('id','childcategoryName','slug');
    }
    public function brand()
    {
        return $this->hasOne(Brand::class,'id','brand_id')->select('id','name','slug');
    }
    public function sizes()
    {
        return $this->belongsToMany('App\Models\Size','productsizes')->withPivot('price','stock')->withTimestamps();
    }
    public function colors()
    {
        return $this->belongsToMany('App\Models\Color','productcolors')->withPivot('price','stock')->withTimestamps();
    }
    public function offers()
    {
        return $this->belongsToMany(Offer::class, 'offer_product', 'product_id', 'offer_id')
                    ->withPivot('custom_price', 'sort_order')
                    ->withTimestamps();
    }

    public function prosizes()
    {
        return $this->hasMany('App\Models\Productsize');
    }
    public function procolors()
    {
        return $this->hasMany('App\Models\Productcolor');
    }

     public function prosize()
    {
        return $this->hasOne(Productsize::class, 'product_id');
    }
     public function procolor()
    {
        return $this->hasOne(Productcolor::class, 'product_id');
    }

    public function scopeInStock($query)
    {
        return $query->where('products.stock', '>', 0)
            ->where(function($q) {
                $q->whereDoesntHave('procolors')
                  ->orWhereHas('procolors', function($cq) {
                      $cq->where('stock', '>', 0)->orWhereNull('stock');
                  });
            })
            ->where(function($q) {
                $q->whereDoesntHave('prosizes')
                  ->orWhereHas('prosizes', function($sq) {
                      $sq->where('stock', '>', 0)->orWhereNull('stock');
                  });
            });
    }

    public function isInStock()
    {
        if ($this->stock <= 0) {
            return false;
        }

        if ($this->relationLoaded('procolors')) {
            if ($this->procolors->isNotEmpty()) {
                $hasAvailableColor = $this->procolors->contains(function($c) {
                    return $c->stock === null || $c->stock > 0;
                });
                if (!$hasAvailableColor) {
                    return false;
                }
            }
        } else {
            if ($this->procolors()->exists()) {
                $hasAvailableColor = $this->procolors()->where(function($q) {
                    $q->where('stock', '>', 0)->orWhereNull('stock');
                })->exists();
                if (!$hasAvailableColor) {
                    return false;
                }
            }
        }

        if ($this->relationLoaded('prosizes')) {
            if ($this->prosizes->isNotEmpty()) {
                $hasAvailableSize = $this->prosizes->contains(function($s) {
                    return $s->stock === null || $s->stock > 0;
                });
                if (!$hasAvailableSize) {
                    return false;
                }
            }
        } else {
            if ($this->prosizes()->exists()) {
                $hasAvailableSize = $this->prosizes()->where(function($q) {
                    $q->where('stock', '>', 0)->orWhereNull('stock');
                })->exists();
                if (!$hasAvailableSize) {
                    return false;
                }
            }
        }

        return true;
    }

    public function isOutOfStock()
    {
        return !$this->isInStock();
    }
}

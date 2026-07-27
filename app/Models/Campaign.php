<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function product(){
        return $this->hasOne(Product::class, 'id','product_id')->select('id','name','slug','old_price','new_price');
    }
    public function products()
    {
        return $this->belongsToMany(Product::class, 'campaign_product', 'campaign_id', 'product_id')->select('id','name','slug','old_price','new_price');
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'campaign_category', 'campaign_id', 'category_id')->select('categories.id', 'categories.name', 'categories.slug');
    }
    public function images(){
        return $this->hasMany(CampaignReview::class, 'campaign_id')->select('id','image','campaign_id');
    }
}

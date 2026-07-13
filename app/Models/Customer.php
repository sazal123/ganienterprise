<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    use HasFactory;

    protected $guard = 'customer';
    protected $fillable = [
        'name', 'email', 'password', 'phone', 'whatsapp', 'address',
        'district', 'area', 'image', 'status', 'verify', 'balance',
        'slug', 'customer_code', 'feedback',
    ];
    protected $hidden = [
      'password', 'remember_token',
    ];

    public function cust_area()
    {
        return $this->belongsTo(District::class,'area');
    }
    public function orders()
    {
        return $this->hasMany(Order::class,'customer_id');
    }

    /**
     * Get order product categories as comma-separated string.
     */
    public function getOrderCategoriesAttribute()
    {
        $categoryNames = \App\Models\OrderDetails::whereIn('order_id', $this->orders()->pluck('id'))
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->distinct()
            ->pluck('categories.name')
            ->toArray();

        return implode(', ', array_unique($categoryNames));
    }

    /**
     * Total order value.
     */
    public function getTotalOrderValueAttribute()
    {
        return $this->orders()->sum('amount');
    }

    /**
     * Number of deals (orders).
     */
    public function getNoOfDealAttribute()
    {
        return $this->orders()->count();
    }

    /**
     * Last order date.
     */
    public function getLastOrderDateAttribute()
    {
        $lastOrder = $this->orders()->latest()->first();
        return $lastOrder ? $lastOrder->created_at->format('d-m-Y') : null;
    }
}

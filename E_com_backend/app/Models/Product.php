<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Product belongs to one Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Product has many CartItems
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    // Product has many OrderItems
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
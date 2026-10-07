<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total',
        'status',
        'shipping_address',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Order belongs to one User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Order has many OrderItems
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
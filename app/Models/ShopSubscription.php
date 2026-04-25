<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopSubscription extends Model
{
    protected $fillable = [
        'shop_id',
        'amount',
        'payment_date',
        'valid_until',
        'status',
        'notes'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'valid_until' => 'date',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}

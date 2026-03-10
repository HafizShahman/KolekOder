<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = ['customer_id', 'shop_id', 'order_number', 'type', 'total_amount', 'total_cups', 'status', 'notes'];

    protected $casts = ['total_amount' => 'decimal:2'];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->order_number)) {
                $shopInitial = $order->shop?->initial ?: 'ORD';
                $dateCode = now()->format('dm'); // e.g. 080326
                $count = static::whereDate('created_at', now()->toDateString())->count() + 1;
                $order->order_number = strtoupper($shopInitial) . '-' . $dateCode . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}

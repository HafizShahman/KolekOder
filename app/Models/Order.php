<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = ['customer_id', 'shop_id', 'order_number', 'type', 'total_amount', 'total_cups', 'status', 'notes', 'is_archived'];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'is_archived' => 'boolean'
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->order_number)) {
                $shop = $order->shop;
                $shopInitial = $shop?->initial ?: 'ORD';
                $dayStartTime = $shop?->day_start_time ?: '00:00';
                
                $now = now();
                $currentTime = $now->format('H:i');
                
                if ($currentTime < $dayStartTime) {
                    $businessDate = $now->copy()->subDay();
                } else {
                    $businessDate = $now->copy();
                }
                
                $dateCode = $businessDate->format('dm');
                
                $start = \Illuminate\Support\Carbon::parse($businessDate->toDateString() . ' ' . $dayStartTime);
                $end = $start->copy()->addDay();
                
                $shiftCount = static::where('shop_id', $order->shop_id)
                    ->where('created_at', '>=', $start)
                    ->where('created_at', '<', $end)
                    ->count() + 1;

                $order->order_number = strtoupper($shopInitial) . '-' . $dateCode . '-' . str_pad($shiftCount, 4, '0', STR_PAD_LEFT);
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

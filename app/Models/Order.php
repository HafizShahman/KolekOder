<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = ['customer_id', 'shop_id', 'order_number', 'type', 'total_amount', 'total_cups', 'status', 'notes', 'is_archived', 'business_date'];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'is_archived' => 'boolean',
        'business_date' => 'date:Y-m-d'
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $shop = $order->shop;
            $now = now();
            
            // Calculate business date if not set
            if (empty($order->business_date)) {
                $dayStartTime = $shop?->day_start_time ?: '00:00';
                $currentTime = $now->format('H:i');
                
                if ($currentTime < $dayStartTime) {
                    $order->business_date = $now->copy()->subDay()->toDateString();
                } else {
                    $order->business_date = $now->toDateString();
                }
            }

            if (empty($order->order_number)) {
                $shopInitial = $shop?->initial ?: 'ORD';
                $businessDate = \Illuminate\Support\Carbon::parse($order->business_date);
                $dateCode = $businessDate->format('dm');
                
                $dayStartTime = $shop?->day_start_time ?: '00:00';
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

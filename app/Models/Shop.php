<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shop extends Model
{
    protected $fillable = ['user_id', 'shop_name', 'initial', 'shop_logo', 'shop_address', 'color_setting', 'day_start_time', 'is_active'];

    protected $casts = [
        'color_setting' => 'array',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the business date range for a given date (defaults to now).
     */
    public function getBusinessDateRange(?\Carbon\Carbon $date = null): array
    {
        $now = $date ?: now();
        $dayStartTime = $this->day_start_time ?: '00:00';
        
        $currentDate = $now->copy()->startOfDay();
        $startOfShiftToday = \Carbon\Carbon::parse($currentDate->toDateString() . ' ' . $dayStartTime);
        
        if ($now->lt($startOfShiftToday)) {
            // We are currently in the shift that started yesterday
            $start = $startOfShiftToday->copy()->subDay();
            $end = $startOfShiftToday->copy();
        } else {
            // We are in the shift that started today
            $start = $startOfShiftToday->copy();
            $end = $startOfShiftToday->copy()->addDay();
        }
        
        return [$start, $end];
    }

    /**
     * Get the current business date string (Y-m-d).
     */
    public function getBusinessDate(?\Carbon\Carbon $date = null): string
    {
        $now = $date ?: now();
        $dayStartTime = $this->day_start_time ?: '00:00';
        
        if ($now->format('H:i') < $dayStartTime) {
            return $now->copy()->subDay()->toDateString();
        }
        return $now->toDateString();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shop extends Model
{
    protected $fillable = ['user_id', 'shop_name', 'initial', 'shop_logo', 'shop_address', 'color_setting', 'day_start_time', 'is_active', 'redemption_threshold', 'redemption_reward', 'operation_hours'];

    protected $casts = [
        'color_setting' => 'array',
        'is_active' => 'boolean',
        'redemption_threshold' => 'integer',
        'operation_hours' => 'array',
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

    public function subscriptions(): HasMany
    {
        return $this->hasMany(ShopSubscription::class);
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(Redemption::class);
    }

    /**
     * Get operation hours for a specific day of the week.
     */
    public function getOperationHoursForDay(int $dayOfWeek): ?array
    {
        $hours = $this->operation_hours;
        if (!is_array($hours)) {
            return null;
        }

        foreach ($hours as $key => $val) {
            if (is_array($val) && isset($val['day']) && (int)$val['day'] === $dayOfWeek) {
                return $val;
            }
            if ((int)$key === $dayOfWeek && is_array($val)) {
                return $val;
            }
        }

        return null;
    }

    /**
     * Check if the shop is closed at the given datetime (defaults to now).
     */
    public function isClosed(?\Carbon\Carbon $date = null): bool
    {
        $now = $date ?: now();
        $hours = $this->operation_hours;

        if (empty($hours)) {
            return false;
        }

        // Shop is open if $now falls within yesterday's or today's active shift
        // Yesterday's shift check
        $yesterday = $now->copy()->subDay();
        $yesterdayConf = $this->getOperationHoursForDay($yesterday->dayOfWeek);
        if ($yesterdayConf && !($yesterdayConf['is_closed'] ?? false)) {
            $yStart = \Carbon\Carbon::parse($yesterday->toDateString() . ' ' . ($yesterdayConf['start_time'] ?? '00:00'));
            $yEnd = \Carbon\Carbon::parse($yesterday->toDateString() . ' ' . ($yesterdayConf['end_time'] ?? '23:59'));
            if ($yEnd->lte($yStart)) {
                $yEnd->addDay();
            }
            if ($now->gte($yStart) && $now->lt($yEnd)) {
                return false;
            }
        }

        // Today's shift check
        $todayConf = $this->getOperationHoursForDay($now->dayOfWeek);
        if ($todayConf && !($todayConf['is_closed'] ?? false)) {
            $tStart = \Carbon\Carbon::parse($now->toDateString() . ' ' . ($todayConf['start_time'] ?? '00:00'));
            $tEnd = \Carbon\Carbon::parse($now->toDateString() . ' ' . ($todayConf['end_time'] ?? '23:59'));
            if ($tEnd->lte($tStart)) {
                $tEnd->addDay();
            }
            if ($now->gte($tStart) && $now->lt($tEnd)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the business date range for a given date (defaults to now).
     */
    public function getBusinessDateRange(?\Carbon\Carbon $date = null): array
    {
        $now = $date ?: now();
        $hours = $this->operation_hours;

        if (empty($hours)) {
            $dayStartTime = $this->day_start_time ?: '00:00';
            $currentDate = $now->copy()->startOfDay();
            $startOfShiftToday = \Carbon\Carbon::parse($currentDate->toDateString() . ' ' . $dayStartTime);
            
            if ($now->lt($startOfShiftToday)) {
                $start = $startOfShiftToday->copy()->subDay();
                $end = $startOfShiftToday->copy();
            } else {
                $start = $startOfShiftToday->copy();
                $end = $startOfShiftToday->copy()->addDay();
            }
            return [$start, $end];
        }

        // Check if we are in yesterday's shift
        $yesterday = $now->copy()->subDay();
        $yesterdayConf = $this->getOperationHoursForDay($yesterday->dayOfWeek);
        if ($yesterdayConf && !($yesterdayConf['is_closed'] ?? false)) {
            $yStart = \Carbon\Carbon::parse($yesterday->toDateString() . ' ' . ($yesterdayConf['start_time'] ?? '00:00'));
            $yEnd = \Carbon\Carbon::parse($yesterday->toDateString() . ' ' . ($yesterdayConf['end_time'] ?? '23:59'));
            if ($yEnd->lte($yStart)) {
                $yEnd->addDay();
            }
            if ($now->gte($yStart) && $now->lt($yEnd)) {
                return [$yStart, $yEnd];
            }
        }

        // Check if we are in today's shift
        $todayConf = $this->getOperationHoursForDay($now->dayOfWeek);
        if ($todayConf && !($todayConf['is_closed'] ?? false)) {
            $tStart = \Carbon\Carbon::parse($now->toDateString() . ' ' . ($todayConf['start_time'] ?? '00:00'));
            $tEnd = \Carbon\Carbon::parse($now->toDateString() . ' ' . ($todayConf['end_time'] ?? '23:59'));
            if ($tEnd->lte($tStart)) {
                $tEnd->addDay();
            }
            if ($now->gte($tStart) && $now->lt($tEnd)) {
                return [$tStart, $tEnd];
            }
        }

        // Fallback: Default shift boundary calculation
        $defaultStartTime = '00:00';
        if ($todayConf && !($todayConf['is_closed'] ?? false)) {
            $defaultStartTime = $todayConf['start_time'] ?? '00:00';
        } else {
            $defaultStartTime = $this->day_start_time ?: '00:00';
        }
        
        $currentDate = $now->copy()->startOfDay();
        $startOfShiftToday = \Carbon\Carbon::parse($currentDate->toDateString() . ' ' . $defaultStartTime);
        
        if ($now->lt($startOfShiftToday)) {
            $start = $startOfShiftToday->copy()->subDay();
            $end = $startOfShiftToday->copy();
        } else {
            $start = $startOfShiftToday->copy();
            $end = $startOfShiftToday->copy()->addDay();
        }
        
        return [$start, $end];
    }

    /**
     * Get the start and end datetimes for a specific business date.
     */
    public function getShiftRangeForBusinessDate(\Carbon\Carbon $businessDate): array
    {
        $dayOfWeek = $businessDate->dayOfWeek;
        $hours = $this->operation_hours;

        if (empty($hours)) {
            $dayStartTime = $this->day_start_time ?: '00:00';
            $start = \Carbon\Carbon::parse($businessDate->toDateString() . ' ' . $dayStartTime);
            $end = $start->copy()->addDay();
            return [$start, $end];
        }

        $conf = $this->getOperationHoursForDay($dayOfWeek);
        if ($conf && !($conf['is_closed'] ?? false)) {
            $startTime = $conf['start_time'] ?? '00:00';
            $endTime = $conf['end_time'] ?? '23:59';
            
            $start = \Carbon\Carbon::parse($businessDate->toDateString() . ' ' . $startTime);
            $end = \Carbon\Carbon::parse($businessDate->toDateString() . ' ' . $endTime);
            if ($end->lte($start)) {
                $end->addDay();
            }
            return [$start, $end];
        }

        // Fallback for closed day or empty config
        $dayStartTime = $this->day_start_time ?: '00:00';
        $start = \Carbon\Carbon::parse($businessDate->toDateString() . ' ' . $dayStartTime);
        $end = $start->copy()->addDay();
        return [$start, $end];
    }

    /**
     * Get the current business date string (Y-m-d).
     */
    public function getBusinessDate(?\Carbon\Carbon $date = null): string
    {
        $range = $this->getBusinessDateRange($date);
        return $range[0]->toDateString();
    }
}

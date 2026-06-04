<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuestClaimController extends Controller
{
    /**
     * Link a previously placed guest order to the authenticated user's account.
     * Awards points if the order was already completed.
     */
    public function apiClaim(Request $request)
    {
        $request->validate([
            'order_id'       => 'required|integer',
            'tracking_token' => 'required|string',
        ]);

        $user = auth()->user();

        // Find the unclaimed guest order within the last 48 hours
        $order = Order::where('id', $request->order_id)
            ->where('tracking_token', $request->tracking_token)
            ->whereNull('customer_id')
            ->where('created_at', '>=', now()->subHours(48))
            ->first();

        if (!$order) {
            return response()->json([
                'message' => 'Order not found, already claimed, or claim window has expired (48 hours).',
            ], 404);
        }

        return DB::transaction(function () use ($user, $order) {
            // Create or find the customer record for this user at this shop
            $customer = Customer::firstOrCreate(
                ['user_id' => $user->id, 'shop_id' => $order->shop_id],
                ['name' => $user->name, 'phone' => null, 'collect_points' => 0]
            );

            // Link the order to this customer
            $order->update(['customer_id' => $customer->id]);

            // Award points immediately if order is already completed
            $pointsAwarded = 0;
            if ($order->status === 'completed') {
                $customer->increment('collect_points', $order->total_cups);
                $pointsAwarded = $order->total_cups;
            }

            return response()->json([
                'message'       => $pointsAwarded > 0
                    ? "Order claimed! {$pointsAwarded} point(s) awarded."
                    : 'Order claimed! Points will be awarded when your order is completed.',
                'customer'      => $customer->fresh(),
                'order'         => $order->fresh(),
                'points_awarded'=> $pointsAwarded,
            ]);
        });
    }
}

<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Redemption;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RedemptionController extends Controller
{
    /**
     * List all redemptions for the authenticated customer across all shops.
     */
    public function apiIndex()
    {
        $user = auth()->user();
        $customerIds = Customer::where('user_id', $user->id)->pluck('id');

        $redemptions = Redemption::with(['shop', 'order'])
            ->whereIn('customer_id', $customerIds)
            ->latest()
            ->get();

        return response()->json(['redemptions' => $redemptions]);
    }

    /**
     * Generate a redemption code for a specific shop.
     * Requires the customer to have enough points at that shop.
     */
    public function apiCreate(Request $request)
    {
        $request->validate([
            'shop_id' => 'required|integer|exists:shops,id',
        ]);

        $user = auth()->user();
        $shop = Shop::findOrFail($request->shop_id);

        if (!$shop->redemption_threshold) {
            return response()->json(['message' => 'This shop has not enabled point redemption.'], 422);
        }

        $customer = Customer::where('user_id', $user->id)
            ->where('shop_id', $shop->id)
            ->first();

        if (!$customer) {
            return response()->json(['message' => 'You have no loyalty record at this shop.'], 422);
        }

        if ($customer->collect_points < $shop->redemption_threshold) {
            return response()->json([
                'message' => "You need {$shop->redemption_threshold} points to redeem. You have {$customer->collect_points}.",
            ], 422);
        }

        return DB::transaction(function () use ($customer, $shop) {
            // Expire any pending codes for this customer at this shop
            Redemption::where('customer_id', $customer->id)
                ->where('shop_id', $shop->id)
                ->where('status', 'pending')
                ->update(['status' => 'expired']);

            // Generate unique 6-char uppercase code
            do {
                $code = strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
            } while (Redemption::where('redemption_code', $code)->exists());

            $redemption = Redemption::create([
                'customer_id'    => $customer->id,
                'shop_id'        => $shop->id,
                'points_used'    => $shop->redemption_threshold,
                'redemption_code'=> $code,
                'status'         => 'pending',
                'expires_at'     => now()->addMinutes(30),
            ]);

            return response()->json([
                'message'    => 'Redemption code generated! Show this to the barista.',
                'redemption' => $redemption->load('shop'),
                'code'       => $code,
                'expires_at' => $redemption->expires_at->toISOString(),
                'reward'     => $shop->redemption_reward,
            ]);
        });
    }
}

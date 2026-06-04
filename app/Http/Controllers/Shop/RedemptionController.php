<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Redemption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RedemptionController extends Controller
{
    /**
     * Verify a redemption code entered by the shop.
     * Returns customer info and reward details without consuming the code.
     */
    public function apiVerify(Request $request)
    {
        $shop = auth()->user()->shop;

        $request->validate([
            'code' => 'required|string|max:8',
        ]);

        $redemption = Redemption::with('customer')
            ->where('shop_id', $shop->id)
            ->where('redemption_code', strtoupper(trim($request->code)))
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if (!$redemption) {
            return response()->json(['message' => 'Invalid or expired redemption code.'], 404);
        }

        $secondsLeft = max(0, now()->diffInSeconds($redemption->expires_at, false));

        return response()->json([
            'valid'       => true,
            'redemption'  => $redemption,
            'customer'    => $redemption->customer,
            'reward'      => $shop->redemption_reward,
            'points_used' => $redemption->points_used,
            'expires_in'  => $secondsLeft,
        ]);
    }

    /**
     * Apply (consume) a redemption code and deduct points from the customer.
     */
    public function apiApply(Request $request)
    {
        $shop = auth()->user()->shop;

        $request->validate([
            'code'     => 'required|string|max:8',
            'order_id' => 'nullable|integer|exists:orders,id',
        ]);

        $redemption = Redemption::with('customer')
            ->where('shop_id', $shop->id)
            ->where('redemption_code', strtoupper(trim($request->code)))
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if (!$redemption) {
            return response()->json(['message' => 'Invalid or expired redemption code.'], 404);
        }

        DB::transaction(function () use ($redemption, $request) {
            $redemption->update([
                'status'   => 'used',
                'order_id' => $request->order_id ?? null,
            ]);

            // Deduct points from the customer
            $newPoints = max(0, $redemption->customer->collect_points - $redemption->points_used);
            $redemption->customer->update(['collect_points' => $newPoints]);
        });

        return response()->json([
            'message'          => 'Redemption applied successfully!',
            'redemption'       => $redemption->fresh(['customer']),
            'remaining_points' => $redemption->customer->fresh()->collect_points,
        ]);
    }

    /**
     * List recent redemptions for this shop (for shop owner's reference).
     */
    public function apiIndex()
    {
        $shop = auth()->user()->shop;

        $redemptions = Redemption::with(['customer', 'order'])
            ->where('shop_id', $shop->id)
            ->latest()
            ->paginate(20);

        return response()->json(['redemptions' => $redemptions]);
    }
}

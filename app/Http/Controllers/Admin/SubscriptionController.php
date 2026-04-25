<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\ShopSubscription;

class SubscriptionController extends Controller
{
    public function apiStore(Request $request, Shop $shop)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:payment_date',
            'notes' => 'nullable|string'
        ]);

        $subscription = $shop->subscriptions()->create([
            'amount' => $validated['amount'],
            'payment_date' => $validated['payment_date'],
            'valid_until' => $validated['valid_until'],
            'status' => 'active',
            'notes' => $validated['notes']
        ]);

        return response()->json([
            'message' => 'Subscription recorded successfully',
            'subscription' => $subscription
        ]);
    }

    public function apiIndex(Shop $shop)
    {
        return response()->json([
            'subscriptions' => $shop->subscriptions()->orderBy('payment_date', 'desc')->get()
        ]);
    }
}

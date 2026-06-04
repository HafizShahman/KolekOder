<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\FavoriteOrder;
use Illuminate\Http\Request;

class FavoriteOrderController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $customerIds = Customer::where('user_id', $user->id)->pluck('id');
        $favorites = FavoriteOrder::whereIn('customer_id', $customerIds)->latest()->get();
        return view('customer.favorites.index', compact('favorites'));
    }

    public function destroy(FavoriteOrder $favoriteOrder)
    {
        $user = auth()->user();
        $customerIds = Customer::where('user_id', $user->id)->pluck('id');
        abort_if(!$customerIds->contains($favoriteOrder->customer_id), 403);
        $favoriteOrder->delete();
        return back()->with('success', 'Favorite removed.');
    }

    public function apiIndex()
    {
        $user = auth()->user();
        $customerIds = Customer::where('user_id', $user->id)->pluck('id');
        $favorites = FavoriteOrder::whereIn('customer_id', $customerIds)->latest()->get();
        return response()->json(['favorites' => $favorites]);
    }

    public function apiStore(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'name'     => 'nullable|string|max:255',
        ]);

        $user = auth()->user();
        $customerIds = Customer::where('user_id', $user->id)->pluck('id');

        // Verify the order belongs to one of this user's customer records
        $customerId = $customerIds->first();
        if (!$customerId) {
            return response()->json(['message' => 'No customer profile found.'], 422);
        }

        $favorite = FavoriteOrder::create([
            'customer_id' => $customerId,
            'order_id'    => $request->order_id,
            'name'        => $request->name,
        ]);

        return response()->json(['message' => 'Favorite saved.', 'favorite' => $favorite], 201);
    }

    public function apiDestroy(FavoriteOrder $favorite)
    {
        $user = auth()->user();
        $customerIds = Customer::where('user_id', $user->id)->pluck('id');

        if (!$customerIds->contains($favorite->customer_id)) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $favorite->delete();
        return response()->json(['message' => 'Favorite removed.']);
    }
}

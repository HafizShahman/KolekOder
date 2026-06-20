<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\FavoriteOrder;
use App\Models\Order;
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

        if ($customerIds->isEmpty()) {
            return response()->json(['message' => 'No customer profile found.'], 422);
        }

        // Verify the order actually belongs to one of this user's customer records
        $order = Order::with('items.product')
            ->where('id', $request->order_id)
            ->whereIn('customer_id', $customerIds)
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        // Snapshot the order's items so the favorite stays reorderable
        // even if the original order is later archived or changed.
        $orderData = $order->items->map(fn ($item) => [
            'product_id'   => $item->product_id,
            'product_name' => $item->product?->name,
            'quantity'     => $item->quantity,
            'unit_price'   => $item->unit_price,
            'variant'      => $item->variant,
            'addons'       => $item->addons,
        ])->all();

        $favorite = FavoriteOrder::create([
            'customer_id' => $order->customer_id,
            'name'        => $request->name ?: ('Order #' . $order->order_number),
            'order_data'  => $orderData,
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

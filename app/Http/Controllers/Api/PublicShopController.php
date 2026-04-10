<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicShopController extends Controller
{
    /**
     * Get basic shop branding info by its initial.
     */
    public function getShop($initial)
    {
        $shop = Shop::where('initial', $initial)->where('is_active', true)->firstOrFail();
        return response()->json(['shop' => $shop]);
    }

    /**
     * Get all available products for a shop by its initial.
     */
    public function getProducts($initial)
    {
        $shop = Shop::where('initial', $initial)->firstOrFail();
        $products = Product::where('shop_id', $shop->id)
            ->where('is_available', true)
            ->with(['variants', 'addons'])
            ->orderBy('sort_order')
            ->get();

        return response()->json(['products' => $products]);
    }

    /**
     * Get the status of a specific order.
     */
    public function getOrderStatus($orderId)
    {
        $order = Order::with(['shop', 'items.product'])->findOrFail($orderId);
        return response()->json(['order' => $order]);
    }

    /**
     * Store a new public (guest or authenticated) order.
     */
    public function storeOrder(Request $request)
    {
        $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'user_id' => 'nullable|exists:users,id',
            'items' => 'required|array',
            'customer_name' => 'required|string',
            'total_amount' => 'required|numeric',
        ]);

        return DB::transaction(function () use ($request) {
            $totalCups = collect($request->items)->sum('quantity');

            // Resolve Customer ID
            $customerId = null;
            if ($request->user_id) {
                $customer = Customer::firstOrCreate(
                    [
                        'user_id' => $request->user_id,
                        'shop_id' => $request->shop_id
                    ],
                    [
                        'name' => $request->customer_name,
                        'phone' => $request->customer_phone ?? null,
                    ]
                );
                $customerId = $customer->id;
            }

            $order = Order::create([
                'shop_id' => $request->shop_id,
                'customer_id' => $customerId, // Linked to shop-specific customer record
                'total_amount' => $request->total_amount,
                'total_cups' => $totalCups,
                'status' => 'pending',
                'notes' => "Public Order: {$request->customer_name}" . ($request->customer_phone ? " ({$request->customer_phone})" : ""),
            ]);

            foreach ($request->items as $item) {
                // Map item details from the public checkout
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'] ?? 0,
                    'subtotal' => ($item['unit_price'] ?? 0) * $item['quantity'],
                    'variant' => $item['variant'] ?? null,
                    'addons' => $item['addons'] ?? [],
                ]);
            }

            // Award loyalty points to the customer record
            if ($customerId) {
                $customer->increment('collect_points', $totalCups);
            }

            return response()->json([
                'message' => 'Order placed successfully',
                'order' => $order->fresh(['shop', 'items']),
                'tracking_token' => bin2hex(random_bytes(16))
            ]);
        });
    }
}

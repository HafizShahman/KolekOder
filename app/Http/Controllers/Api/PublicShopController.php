<?php

namespace App\Http\Controllers\Api;

use App\Events\NewOrderReceived;
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

        // Only expose branding/loyalty fields — never internal data like user_id.
        return response()->json(['shop' => $shop->only([
            'id', 'shop_name', 'initial', 'shop_logo', 'shop_address',
            'color_setting', 'redemption_threshold', 'redemption_reward', 'is_active',
            'operation_hours', 'day_start_time'
        ])]);
    }

    /**
     * Get all available products for a shop by its initial.
     * Only returns products for active shops.
     */
    public function getProducts($initial)
    {
        $shop = Shop::where('initial', $initial)->where('is_active', true)->firstOrFail();
        $products = Product::where('shop_id', $shop->id)
            ->where('is_available', true)
            ->with(['variants', 'addons'])
            ->orderBy('sort_order')
            ->get();

        return response()->json(['products' => $products]);
    }

    /**
     * Get the status of a specific order using a tracking token.
     */
    public function getOrderStatus(Request $request, $orderId)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $order = Order::with(['shop', 'items.product'])
            ->where('id', $orderId)
            ->where('tracking_token', $request->token)
            ->firstOrFail();

        return response()->json(['order' => $order]);
    }

    /**
     * Store a new public (guest or authenticated) order.
     * Total is always recalculated server-side from actual product prices.
     */
    public function storeOrder(Request $request)
    {
        $request->validate([
            'shop_id'       => 'required|exists:shops,id',
            'items'         => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.variant'    => 'nullable|string',
            'items.*.addons'     => 'nullable|array',
            'items.*.note'       => 'nullable|string|max:200',
            'customer_name' => 'required|string|max:255',
            'customer_phone'=> 'nullable|string|max:30',
        ]);

        // Verify the shop is active
        $shop = Shop::where('id', $request->shop_id)->where('is_active', true)->firstOrFail();

        if ($shop->isClosed()) {
            return response()->json(['message' => 'The shop is currently closed.'], 422);
        }

        return DB::transaction(function () use ($request, $shop) {
            $totalAmount = 0;
            $totalCups   = 0;
            $orderItemsData = [];

            foreach ($request->items as $item) {
                // Verify product belongs to this shop
                $product = Product::with(['variants', 'addons'])
                    ->where('id', $item['product_id'])
                    ->where('shop_id', $shop->id)
                    ->where('is_available', true)
                    ->firstOrFail();

                $qty       = (int) $item['quantity'];
                $unitPrice = (float) $product->price;

                // Add variant price modifier
                if (!empty($item['variant'])) {
                    $variant = $product->variants->where('name', $item['variant'])->first();
                    if ($variant) {
                        $unitPrice += (float) $variant->price_modifier;
                    }
                }

                // Add addon prices
                $selectedAddons = [];
                if (!empty($item['addons'])) {
                    foreach ($item['addons'] as $addonId) {
                        $addon = $product->addons->find($addonId);
                        if ($addon) {
                            $unitPrice += (float) $addon->price;
                            $selectedAddons[] = ['id' => $addon->id, 'name' => $addon->name, 'price' => $addon->price];
                        }
                    }
                }

                $sub          = $unitPrice * $qty;
                $totalAmount += $sub;
                $totalCups   += $qty;

                // Strip any markup from the customer-supplied note before storing it
                $note = trim(strip_tags((string) ($item['note'] ?? '')));

                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'quantity'   => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal'   => $sub,
                    'variant'    => $item['variant'] ?? null,
                    'addons'     => !empty($selectedAddons) ? $selectedAddons : [],
                    'note'       => $note !== '' ? $note : null,
                ];
            }

            // Resolve Customer ID from auth token only — never trust user_id from request body
            $customerId = null;
            if (auth('sanctum')->check()) {
                $authUser = auth('sanctum')->user();
                $customer = Customer::firstOrCreate(
                    ['user_id' => $authUser->id, 'shop_id' => $shop->id],
                    ['name' => $request->customer_name, 'phone' => $request->customer_phone ?? null]
                );
                $customerId = $customer->id;
            }

            $trackingToken = bin2hex(random_bytes(16));

            // Strip any markup from customer-supplied values before storing them in notes
            $customerName  = strip_tags((string) $request->customer_name);
            $customerPhone = strip_tags((string) $request->customer_phone);

            $order = Order::create([
                'shop_id'        => $shop->id,
                'customer_id'    => $customerId,
                'total_amount'   => $totalAmount,
                'total_cups'     => $totalCups,
                'status'         => 'pending',
                'tracking_token' => $trackingToken,
                'notes'          => "Public Order: {$customerName}" . ($customerPhone ? " ({$customerPhone})" : ""),
            ]);

            foreach ($orderItemsData as $d) {
                $order->items()->create($d);
            }

            broadcast(new NewOrderReceived($order->fresh(['shop', 'items.product', 'customer'])));

            return response()->json([
                'message'        => 'Order placed successfully',
                'order'          => $order->fresh(['shop', 'items']),
                'tracking_token' => $trackingToken,
            ]);
        });
    }
}

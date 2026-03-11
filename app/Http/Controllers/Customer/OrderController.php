<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $customerIds = Customer::where('user_id', $user->id)->pluck('id');

        $query = Order::with(['shop', 'items.product'])
            ->whereIn('customer_id', $customerIds)
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('customer.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $user = auth()->user();
        $customerIds = Customer::where('user_id', $user->id)->pluck('id');
        abort_if(!$customerIds->contains($order->customer_id), 403);

        $order->load(['shop', 'items.product']);
        return view('customer.orders.show', compact('order'));
    }

    public function apiIndex(Request $request)
    {
        $user = auth()->user();
        $customerIds = Customer::where('user_id', $user->id)->pluck('id');

        $query = Order::with(['shop', 'items.product'])
            ->whereIn('customer_id', $customerIds)
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(15);

        return response()->json([
            'orders' => $orders
        ]);
    }

    public function apiShow(Order $order)
    {
        $user = auth()->user();
        $customerIds = Customer::where('user_id', $user->id)->pluck('id');

        if (!$customerIds->contains($order->customer_id)) {
            return response()->json(['message' => 'Unauthorized Access'], 403);
        }

        return response()->json([
            'order' => $order->load(['shop', 'items.product'])
        ]);
    }
}

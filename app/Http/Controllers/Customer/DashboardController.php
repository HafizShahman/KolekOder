<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get all customer records linked to this user
        $customerIds = Customer::where('user_id', $user->id)->pluck('id');

        $totalOrders = Order::whereIn('customer_id', $customerIds)->count();
        $totalSpent = Order::whereIn('customer_id', $customerIds)->where('status', 'completed')->sum('total_amount');
        $totalPoints = Customer::where('user_id', $user->id)->sum('collect_points');

        $recentOrders = Order::with(['shop', 'items.product'])
            ->whereIn('customer_id', $customerIds)
            ->latest()
            ->take(5)
            ->get();

        return view('customer.dashboard', compact('totalOrders', 'totalSpent', 'totalPoints', 'recentOrders'));
    }
}

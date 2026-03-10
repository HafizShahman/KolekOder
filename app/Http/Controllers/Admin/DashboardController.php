<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shop;

class DashboardController extends Controller
{
    public function index()
    {
        $totalIncome = Order::where('status', 'completed')->sum('total_amount');
        $totalTenants = Shop::count();
        $activeTenants = Shop::where('is_active', true)->count();
        $inactiveTenants = Shop::where('is_active', false)->count();
        $totalOrders = Order::count();
        $totalCups = Order::where('status', 'completed')->sum('total_cups');

        $recentShops = Shop::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalIncome',
            'totalTenants',
            'activeTenants',
            'inactiveTenants',
            'totalOrders',
            'totalCups',
            'recentShops'
        ));
    }
}

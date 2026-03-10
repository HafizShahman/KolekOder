<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Order;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $query = Shop::with('user')
            ->withCount('orders')
            ->withCount('products')
            ->withCount('customers');

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('shop_name', 'like', "%{$s}%")
                ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%")));
        }

        $tenants = $query->latest()->paginate(15)->withQueryString();

        return view('admin.tenants.index', compact('tenants'));
    }

    public function show(Shop $shop)
    {
        $shop->load('user');
        $totalOrders = Order::where('shop_id', $shop->id)->count();
        $totalSales = Order::where('shop_id', $shop->id)->where('status', 'completed')->sum('total_amount');
        $totalCups = Order::where('shop_id', $shop->id)->where('status', 'completed')->sum('total_cups');
        $products = $shop->products()->count();
        $customers = $shop->customers()->count();

        return view('admin.tenants.show', compact('shop', 'totalOrders', 'totalSales', 'totalCups', 'products', 'customers'));
    }

    public function toggleStatus(Shop $shop)
    {
        $shop->update(['is_active' => !$shop->is_active]);
        return back()->with('success', "Tenant '{$shop->shop_name}' " . ($shop->is_active ? 'activated' : 'deactivated') . '.');
    }
}

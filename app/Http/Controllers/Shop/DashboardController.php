<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $shop = auth()->user()->shop;
        $period = $request->get('period', 'daily');

        // Date ranges based on period
        $now = Carbon::now();
        switch ($period) {
            case 'weekly':
                $startDate = $now->copy()->startOfWeek();
                $endDate = $now->copy()->endOfWeek();
                break;
            case 'monthly':
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                break;
            default: // daily
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
        }

        // Analytics
        $totalCups = Order::where('shop_id', $shop->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_cups');

        $totalOrders = Order::where('shop_id', $shop->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $totalSales = Order::where('shop_id', $shop->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        // Top products ranking
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.shop_id', $shop->id)
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'products.name',
                'products.price',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.price')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

        $maxSold = $topProducts->max('total_sold') ?: 1;

        // Customer ranking
        $topCustomers = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->where('orders.shop_id', $shop->id)
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'customers.name',
                'customers.phone',
                DB::raw('COUNT(orders.id) as order_count'),
                DB::raw('SUM(orders.total_amount) as total_spent'),
                DB::raw('SUM(orders.total_cups) as total_cups_bought')
            )
            ->groupBy('customers.id', 'customers.name', 'customers.phone')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        // Recent orders
        $recentOrders = Order::with(['customer', 'items.product'])
            ->where('shop_id', $shop->id)
            ->latest()
            ->take(5)
            ->get();

        // Pending count
        $pendingOrders = Order::where('shop_id', $shop->id)->where('status', 'pending')->count();

        return view('shop.dashboard', compact(
            'shop',
            'period',
            'totalCups',
            'totalOrders',
            'totalSales',
            'topProducts',
            'maxSold',
            'topCustomers',
            'recentOrders',
            'pendingOrders'
        ));
    }

    public function apiIndex(Request $request)
    {
        $shop = auth()->user()->shop;
        $period = $request->get('period', 'daily');

        // Date ranges based on period
        $now = Carbon::now();
        switch ($period) {
            case 'weekly':
                $startDate = $now->copy()->startOfWeek();
                $endDate = $now->copy()->endOfWeek();
                break;
            case 'monthly':
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                break;
            default: // daily
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
        }

        // Analytics
        $totalCups = Order::where('shop_id', $shop->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_cups');

        $totalOrders = Order::where('shop_id', $shop->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $totalSales = Order::where('shop_id', $shop->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        // Top products ranking
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.shop_id', $shop->id)
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'products.name',
                'products.price',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.price')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

        $maxSold = $topProducts->max('total_sold') ?: 1;

        // Customer ranking
        $topCustomers = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->where('orders.shop_id', $shop->id)
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'customers.name',
                'customers.phone',
                DB::raw('COUNT(orders.id) as order_count'),
                DB::raw('SUM(orders.total_amount) as total_spent'),
                DB::raw('SUM(orders.total_cups) as total_cups_bought')
            )
            ->groupBy('customers.id', 'customers.name', 'customers.phone')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        // Recent orders
        $recentOrders = Order::with(['customer', 'items.product'])
            ->where('shop_id', $shop->id)
            ->latest()
            ->take(5)
            ->get();

        // Pending count
        $pendingOrders = Order::where('shop_id', $shop->id)->where('status', 'pending')->count();

        return response()->json([
            'shop' => $shop,
            'period' => $period,
            'totalCups' => $totalCups,
            'totalOrders' => $totalOrders,
            'totalSales' => $totalSales,
            'topProducts' => $topProducts,
            'maxSold' => $maxSold,
            'topCustomers' => $topCustomers,
            'recentOrders' => $recentOrders,
            'pendingOrders' => $pendingOrders
        ]);
    }
}

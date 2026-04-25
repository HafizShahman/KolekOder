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
        $now = now();
        $startDate = null;
        $endDate = null;
        $businessDate = null;

        switch ($period) {
            case 'weekly':
                $startDate = $now->copy()->startOfWeek(\Carbon\Carbon::MONDAY)->toDateString();
                $endDate = $now->copy()->endOfWeek(\Carbon\Carbon::SUNDAY)->toDateString();
                break;
            case 'monthly':
                $startDate = $now->copy()->startOfMonth()->toDateString();
                $endDate = $now->copy()->endOfMonth()->toDateString();
                break;
            default: // daily
                $businessDate = $shop->getBusinessDate($now);
                break;
        }

    /**
     * Helper to apply date filters to a query
     */
    $applyFilters = function ($query) use ($startDate, $endDate, $businessDate) {
        if ($businessDate) {
            return $query->where('business_date', $businessDate);
        }
        if ($startDate && $endDate) {
            return $query->whereBetween('business_date', [$startDate, $endDate]);
        }
        return $query->where('is_archived', 0); // Default fallback for unarchived
    };

        // Analytics
        $totalCups = $applyFilters(Order::where('shop_id', $shop->id)->where('status', 'completed'))->sum('total_cups');
        $totalOrders = $applyFilters(Order::where('shop_id', $shop->id)->where('status', 'completed'))->count();
        $totalSales = $applyFilters(Order::where('shop_id', $shop->id)->where('status', 'completed'))->sum('total_amount');

        // Top products ranking
        $topProductsQuery = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.shop_id', $shop->id)
            ->where('orders.status', 'completed');
        
        if ($businessDate) {
            $topProductsQuery->where('orders.business_date', $businessDate);
        } elseif ($startDate && $endDate) {
            $topProductsQuery->whereBetween('orders.business_date', [$startDate, $endDate]);
        } else {
            $topProductsQuery->where('orders.is_archived', 0);
        }

        $topProducts = $topProductsQuery->select(
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
        $topCustomersQuery = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->where('orders.shop_id', $shop->id)
            ->where('orders.status', 'completed');

        if ($businessDate) {
            $topCustomersQuery->where('orders.business_date', $businessDate);
        } elseif ($startDate && $endDate) {
            $topCustomersQuery->whereBetween('orders.business_date', [$startDate, $endDate]);
        } else {
            $topCustomersQuery->where('orders.is_archived', 0);
        }

        $topCustomers = $topCustomersQuery->select(
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
        $now = now();
        $startDate = null;
        $endDate = null;
        $businessDate = null;

        switch ($period) {
            case 'weekly':
                $startDate = $now->copy()->startOfWeek(\Carbon\Carbon::MONDAY)->toDateString();
                $endDate = $now->copy()->endOfWeek(\Carbon\Carbon::SUNDAY)->toDateString();
                break;
            case 'monthly':
                $startDate = $now->copy()->startOfMonth()->toDateString();
                $endDate = $now->copy()->endOfMonth()->toDateString();
                break;
            default: // daily
                $businessDate = $shop->getBusinessDate($now);
                break;
        }

        $applyFilters = function ($query) use ($startDate, $endDate, $businessDate) {
            if ($businessDate) {
                return $query->where('business_date', $businessDate);
            }
            if ($startDate && $endDate) {
                return $query->whereBetween('business_date', [$startDate, $endDate]);
            }
            return $query->where('is_archived', 0);
        };

        // Analytics
        $totalCups = $applyFilters(Order::where('shop_id', $shop->id)->where('status', 'completed'))->sum('total_cups');
        $totalOrders = $applyFilters(Order::where('shop_id', $shop->id)->where('status', 'completed'))->count();
        $totalSales = $applyFilters(Order::where('shop_id', $shop->id)->where('status', 'completed'))->sum('total_amount');

        // Top products ranking
        $topProductsQuery = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.shop_id', $shop->id)
            ->where('orders.status', 'completed');

        if ($businessDate) {
            $topProductsQuery->where('orders.business_date', $businessDate);
        } elseif ($startDate && $endDate) {
            $topProductsQuery->whereBetween('orders.business_date', [$startDate, $endDate]);
        } else {
            $topProductsQuery->where('orders.is_archived', 0);
        }

        $topProducts = $topProductsQuery->select(
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
        $topCustomersQuery = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->where('orders.shop_id', $shop->id)
            ->where('orders.status', 'completed');

        if ($businessDate) {
            $topCustomersQuery->where('orders.business_date', $businessDate);
        } elseif ($startDate && $endDate) {
            $topCustomersQuery->whereBetween('orders.business_date', [$startDate, $endDate]);
        } else {
            $topCustomersQuery->where('orders.is_archived', 0);
        }

        $topCustomers = $topCustomersQuery->select(
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
            'current_business_date' => $businessDate,
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

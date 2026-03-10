<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $shop = auth()->user()->shop;

        $query = Customer::where('shop_id', $shop->id)
            ->withCount('orders')
            ->withSum(['orders as total_spent' => fn($q) => $q->where('status', 'completed')], 'total_amount')
            ->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%"));
        }

        $customers = $query->paginate(15)->withQueryString();

        // Favorite orders per customer
        $favorites = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereIn('orders.customer_id', $customers->pluck('id'))
            ->where('orders.status', 'completed')
            ->where('orders.shop_id', $shop->id)
            ->select('orders.customer_id', 'products.name as item_name', DB::raw('SUM(order_items.quantity) as total_qty'))
            ->groupBy('orders.customer_id', 'products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->get()
            ->groupBy('customer_id')
            ->map(fn($items) => $items->first()->item_name);

        return view('shop.customers.index', compact('customers', 'favorites'));
    }

    public function store(Request $request)
    {
        $shop = auth()->user()->shop;

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:500',
        ]);

        Customer::create(array_merge($request->only('name', 'phone', 'notes'), ['shop_id' => $shop->id]));
        return back()->with('success', 'Customer added!');
    }
}

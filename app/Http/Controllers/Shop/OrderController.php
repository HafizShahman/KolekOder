<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $shop = auth()->user()->shop;
        $query = Order::with(['customer', 'items.product'])->where('shop_id', $shop->id)->latest();

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('type')) $query->where('type', $request->type);
        if ($request->filled('from')) $query->whereDate('created_at', '>=', $request->from);
        if ($request->filled('to')) $query->whereDate('created_at', '<=', $request->to);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('order_number', 'like', "%{$s}%")
                ->orWhereHas('customer', fn($cq) => $cq->where('name', 'like', "%{$s}%")));
        }

        $orders = $query->paginate(15)->withQueryString();
        $totalOrders = Order::where('shop_id', $shop->id)->count();
        $pendingCount = Order::where('shop_id', $shop->id)->where('status', 'pending')->count();
        $preparingCount = Order::where('shop_id', $shop->id)->where('status', 'preparing')->count();
        $completedCount = Order::where('shop_id', $shop->id)->where('status', 'completed')->count();

        return view('shop.orders.index', compact('orders', 'totalOrders', 'pendingCount', 'preparingCount', 'completedCount'));
    }

    public function create()
    {
        $shop = auth()->user()->shop;
        $products = Product::with(['variants', 'addons'])
            ->where('shop_id', $shop->id)
            ->where('is_available', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        $customers = Customer::where('shop_id', $shop->id)->orderBy('name')->get();
        return view('shop.orders.create', compact('products', 'customers'));
    }

    public function apiCreate()
    {
        $shop = auth()->user()->shop;
        $products = Product::with(['variants', 'addons'])
            ->where('shop_id', $shop->id)
            ->where('is_available', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        $customers = Customer::where('shop_id', $shop->id)->orderBy('name')->get();
        return response()->json([
            'products' => $products,
            'customers' => $customers
        ]);
    }

    public function store(Request $request)
    {
        $shop = auth()->user()->shop;

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.variant' => 'nullable|string',
            'items.*.addons' => 'nullable|array',
            'customer_id' => 'nullable|exists:customers,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $totalAmount = 0;
        $totalCups = 0;
        $orderItemsData = [];

        foreach ($request->items as $item) {
            $product = Product::with(['variants', 'addons'])->findOrFail($item['product_id']);
            $qty = (int) $item['quantity'];
            $unitPrice = $product->price;

            // Add variant price modifier
            if (!empty($item['variant'])) {
                $variant = $product->variants->where('name', $item['variant'])->first();
                if ($variant) $unitPrice += $variant->price_modifier;
            }

            // Add addon prices
            $addonTotal = 0;
            $selectedAddons = [];
            if (!empty($item['addons'])) {
                foreach ($item['addons'] as $addonId) {
                    $addon = $product->addons->find($addonId);
                    if ($addon) {
                        $addonTotal += $addon->price;
                        $selectedAddons[] = ['id' => $addon->id, 'name' => $addon->name, 'price' => $addon->price];
                    }
                }
            }

            $unitPrice += $addonTotal;
            $sub = $unitPrice * $qty;
            $totalAmount += $sub;
            $totalCups += $qty;

            $orderItemsData[] = [
                'product_id' => $product->id,
                'quantity' => $qty,
                'unit_price' => $unitPrice,
                'subtotal' => $sub,
                'variant' => $item['variant'] ?? null,
                'addons' => !empty($selectedAddons) ? $selectedAddons : null,
            ];
        }

        $order = Order::create([
            'shop_id' => $shop->id,
            'customer_id' => $request->customer_id,
            'type' => count($request->items) > 1 ? 'bulk' : 'single',
            'total_amount' => $totalAmount,
            'total_cups' => $totalCups,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        foreach ($orderItemsData as $d) {
            $order->items()->create($d);
        }

        // Award collect points to customer
        if ($request->customer_id) {
            $customer = Customer::find($request->customer_id);
            if ($customer) {
                $customer->increment('collect_points', $totalCups);
            }
        }

        return redirect()->route('shop.orders.index')->with('success', "Order {$order->order_number} created!");
    }

    public function show(Order $order)
    {
        $shop = auth()->user()->shop;
        abort_if($order->shop_id !== $shop->id, 403);
        $order->load(['customer', 'items.product']);
        return view('shop.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $shop = auth()->user()->shop;
        abort_if($order->shop_id !== $shop->id, 403);
        $request->validate(['status' => 'required|in:pending,preparing,completed,cancelled']);
        $order->update(['status' => $request->status]);
        return back()->with('success', "Order {$order->order_number} updated to {$request->status}.");
    }

    public function apiIndex(Request $request)
    {
        $shop = auth()->user()->shop;
        $query = Order::with(['customer', 'items.product'])
            ->where('shop_id', $shop->id)
            ->where('is_archived', 0) // Default to unarchived
            ->latest();

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('type')) $query->where('type', $request->type);
        if ($request->filled('from')) $query->whereDate('created_at', '>=', $request->from);
        if ($request->filled('to')) $query->whereDate('created_at', '<=', $request->to);
        
        // No longer defaulting to current business day to allow manual archiving

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('order_number', 'like', "%{$s}%")
                ->orWhereHas('customer', fn($cq) => $cq->where('name', 'like', "%{$s}%")));
        }

        $orders = $query->paginate(15);

        // Stats calculation should match the unarchived list behavior
        $statsQuery = Order::where('shop_id', $shop->id)->where('is_archived', 0);
        if ($request->filled('from')) $statsQuery->whereDate('created_at', '>=', $request->from);
        if ($request->filled('to')) $statsQuery->whereDate('created_at', '<=', $request->to);

        $totalOrders = (clone $statsQuery)->count();
        $pendingCount = (clone $statsQuery)->where('status', 'pending')->count();
        $preparingCount = (clone $statsQuery)->where('status', 'preparing')->count();
        $completedCount = (clone $statsQuery)->where('status', 'completed')->count();

        return response()->json([
            'orders' => $orders,
            'stats' => [
                'total' => $totalOrders,
                'pending' => $pendingCount,
                'preparing' => $preparingCount,
                'completed' => $completedCount
            ]
        ]);
    }

    public function apiStore(Request $request)
    {
        $shop = auth()->user()->shop;

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.variant' => 'nullable|string',
            'items.*.addons' => 'nullable|array',
            'customer_id' => 'nullable|exists:customers,id',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $totalAmount = 0;
        $totalCups = 0;
        $orderItemsData = [];

        foreach ($request->items as $item) {
            $product = Product::with(['variants', 'addons'])->findOrFail($item['product_id']);
            $qty = (int) $item['quantity'];
            $unitPrice = $product->price;

            // Add variant price modifier
            if (!empty($item['variant'])) {
                $variant = $product->variants->where('name', $item['variant'])->first();
                if ($variant) $unitPrice += $variant->price_modifier;
            }

            // Add addon prices
            $addonTotal = 0;
            $selectedAddons = [];
            if (!empty($item['addons'])) {
                foreach ($item['addons'] as $addonId) {
                    $addon = $product->addons->find($addonId);
                    if ($addon) {
                        $addonTotal += $addon->price;
                        $selectedAddons[] = ['id' => $addon->id, 'name' => $addon->name, 'price' => $addon->price];
                    }
                }
            }

            $unitPrice += $addonTotal;
            $sub = $unitPrice * $qty;
            $totalAmount += $sub;
            $totalCups += $qty;

            $orderItemsData[] = [
                'product_id' => $product->id,
                'quantity' => $qty,
                'unit_price' => $unitPrice,
                'subtotal' => $sub,
                'variant' => $item['variant'] ?? null,
                'addons' => !empty($selectedAddons) ? $selectedAddons : null,
            ];
        }

        $order = Order::create([
            'shop_id' => $shop->id,
            'customer_id' => $request->customer_id,
            'type' => count($request->items) > 1 ? 'bulk' : 'single',
            'total_amount' => $totalAmount,
            'total_cups' => $totalCups,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        foreach ($orderItemsData as $d) {
            $order->items()->create($d);
        }

        // Award collect points to customer
        if ($request->customer_id) {
            $customer = Customer::find($request->customer_id);
            if ($customer) {
                $customer->increment('collect_points', $totalCups);
            }
        }

        return response()->json([
            'message' => 'Order created successfully!',
            'order' => $order->load(['customer', 'items.product'])
        ], 201);
    }

    public function apiShow(Order $order)
    {
        $shop = auth()->user()->shop;
        if ($order->shop_id !== $shop->id) {
            return response()->json(['message' => 'Unauthorized Access'], 403);
        }

        return response()->json([
            'order' => $order->load(['customer', 'items.product'])
        ]);
    }

    public function apiUpdateStatus(Request $request, Order $order)
    {
        $shop = auth()->user()->shop;
        if ($order->shop_id !== $shop->id) {
            return response()->json(['message' => 'Unauthorized Access'], 403);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'status' => 'required|in:pending,preparing,completed,cancelled'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $order->update(['status' => $request->status]);

        return response()->json([
            'message' => "Order {$order->order_number} status updated successfully",
            'order' => $order
        ]);
    }

    public function apiArchive()
    {
        $shop = auth()->user()->shop;
        Order::where('shop_id', $shop->id)->where('is_archived', 0)->update(['is_archived' => 1]);
        return response()->json(['message' => 'All orders archived successfully!']);
    }

    public function apiArchivedIndex(Request $request)
    {
        $shop = auth()->user()->shop;
        
        $query = Order::where('shop_id', $shop->id)
            ->where('is_archived', 1)
            ->select('business_date', 
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_amount) as total_amount'),
                DB::raw('SUM(total_cups) as total_cups')
            )
            ->groupBy('business_date')
            ->orderByDesc('business_date');

        if ($request->filled('from')) $query->whereDate('business_date', '>=', $request->from);
        if ($request->filled('to')) $query->whereDate('business_date', '<=', $request->to);

        $summaries = $query->paginate(15);
        
        return response()->json([
            'history' => $summaries
        ]);
    }

    public function apiArchivedDayDetail(Request $request, $date)
    {
        $shop = auth()->user()->shop;
        
        $orders = Order::with(['customer', 'items.product'])
            ->where('shop_id', $shop->id)
            ->where('is_archived', 1)
            ->whereDate('business_date', $date)
            ->latest()
            ->get();

        $stats = [
            'total_amount' => $orders->sum('total_amount'),
            'total_cups' => $orders->sum('total_cups'),
            'total_orders' => $orders->count(),
        ];

        return response()->json([
            'date' => $date,
            'stats' => $stats,
            'orders' => $orders
        ]);
    }
}

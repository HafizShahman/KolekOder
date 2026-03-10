@extends('layouts.customer')

@section('content')
    <div class="space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-foreground">{{ __('Welcome back!') }}</h1>
            <p class="mt-1 text-sm text-muted-foreground">{{ Auth::user()->name }}</p>
        </div>

        <div class="grid grid-cols-3 gap-3">
            <div
                class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 to-emerald-600/80 p-4 text-white shadow-xl">
                <p class="text-[10px] font-bold uppercase tracking-widest opacity-80 mb-1">Orders</p>
                <span class="text-xl font-black">{{ number_format($totalOrders) }}</span>
            </div>
            <div class="rounded-2xl bg-card border border-border p-4 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground mb-1">Spent</p>
                <span class="text-lg font-black text-foreground">RM {{ number_format($totalSpent, 0) }}</span>
            </div>
            <div class="rounded-2xl bg-card border border-border p-4 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground mb-1">Points</p>
                <span class="text-xl font-black text-emerald-600">{{ number_format($totalPoints) }}</span>
            </div>
        </div>

        <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-border flex items-center justify-between bg-muted/30">
                <h3 class="text-sm font-bold text-foreground">{{ __('Recent Orders') }}</h3>
                <a href="{{ route('customer.orders.index') }}"
                    class="text-xs font-bold text-emerald-600 hover:underline uppercase tracking-tighter">View All</a>
            </div>
            <div class="divide-y divide-border/50">
                @forelse($recentOrders as $order)
                    <div class="px-4 py-3.5">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-sm font-bold text-foreground">{{ $order->order_number }}</span>
                                @php $sc = ['pending'=>'text-yellow-600','preparing'=>'text-blue-600','completed'=>'text-emerald-600','cancelled'=>'text-red-600']; @endphp
                                <span class="text-[10px] font-bold uppercase {{ $sc[$order->status] ?? '' }}"> ·
                                    {{ $order->status }}</span>
                                <p class="text-xs text-muted-foreground mt-0.5">{{ $order->shop?->shop_name ?? '' }} ·
                                    {{ $order->created_at->diffForHumans() }}</p>
                            </div>
                            <span class="text-sm font-black text-foreground">RM
                                {{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-10 text-center text-sm text-muted-foreground">{{ __('No orders yet.') }}</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

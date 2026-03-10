@extends('layouts.shop')

@section('content')
    <div class="space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-foreground">{{ __('Dashboard') }}</h1>
                <p class="mt-1 text-sm text-muted-foreground">{{ $shop->shop_name }} · {{ __('at a glance') }}</p>
            </div>
            <a href="{{ route('shop.orders.create') }}"
                class="inline-flex h-10 items-center justify-center rounded-xl bg-primary px-4 text-xs font-bold text-primary-foreground shadow-lg shadow-primary/20 active:scale-95 transition-all shrink-0">
                <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('New Order') }}
            </a>
        </div>

        {{-- Period Selector --}}
        <div class="flex gap-2">
            @foreach (['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly'] as $key => $label)
                <a href="{{ route('shop.dashboard', ['period' => $key]) }}"
                    class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all {{ $period === $key ? 'bg-primary text-primary-foreground shadow-lg shadow-primary/20' : 'bg-card border border-border text-muted-foreground hover:text-foreground' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-3 gap-3">
            <div
                class="relative group overflow-hidden rounded-2xl bg-gradient-to-br from-primary to-primary/80 p-4 text-primary-foreground shadow-xl">
                <p class="text-[10px] font-bold uppercase tracking-widest opacity-80 mb-1">{{ __('Total Cups') }}</p>
                <span class="text-xl font-black">{{ number_format($totalCups) }}</span>
                <div class="absolute -right-3 -bottom-3 h-16 w-16 rounded-full bg-white/10 blur-2xl"></div>
            </div>
            <div class="relative group overflow-hidden rounded-2xl bg-card border border-border p-4 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground mb-1">
                    {{ __('Top Product') }}</p>
                <span class="text-sm font-black text-foreground">{{ $topProducts->first()->name ?? '-' }}</span>
            </div>
            <div class="relative group overflow-hidden rounded-2xl bg-card border border-border p-4 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground mb-1">
                    {{ __('Total Orders') }}</p>
                <span class="text-xl font-black text-foreground">{{ number_format($totalOrders) }}</span>
            </div>
        </div>

        {{-- Sales Total --}}
        <div
            class="rounded-2xl bg-gradient-to-br from-primary to-primary/80 p-5 text-primary-foreground shadow-xl shadow-primary/20 overflow-hidden relative">
            <div class="relative z-10">
                <h4 class="text-[10px] font-bold uppercase tracking-widest opacity-80 mb-1">{{ __('Total Sales') }}</h4>
                <p class="text-3xl font-black">RM {{ number_format($totalSales, 2) }}</p>
                <p class="text-xs opacity-80 mt-1">{{ ucfirst($period) }} · {{ $pendingOrders }} {{ __('pending') }}</p>
            </div>
            <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/10 blur-3xl"></div>
        </div>

        {{-- Product Ranking --}}
        <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-border bg-muted/30">
                <h3 class="text-sm font-bold text-foreground flex items-center gap-2">
                    <svg class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                    {{ __('Product Ranking') }}
                </h3>
            </div>
            <div class="divide-y divide-border/50">
                @forelse($topProducts as $index => $item)
                    <div class="px-4 py-3">
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center gap-2.5">
                                <span
                                    class="flex h-6 w-6 items-center justify-center rounded-md text-[10px] font-black {{ $index === 0 ? 'bg-primary text-primary-foreground' : ($index === 1 ? 'bg-gray-300 text-gray-700' : ($index === 2 ? 'bg-amber-700/60 text-white' : 'bg-secondary text-muted-foreground')) }}">{{ $index + 1 }}</span>
                                <p class="text-sm font-bold text-foreground">{{ $item->name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-foreground">{{ $item->total_sold }}</p>
                                <p class="text-[10px] text-muted-foreground">cups</p>
                            </div>
                        </div>
                        <div class="w-full bg-secondary rounded-full h-1 overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-to-r from-primary/80 to-primary transition-all duration-500"
                                style="width: {{ ($item->total_sold / $maxSold) * 100 }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-muted-foreground">{{ __('No sales data yet.') }}</div>
                @endforelse
            </div>
        </div>

        {{-- Customer Ranking --}}
        <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-border bg-muted/30">
                <h3 class="text-sm font-bold text-foreground">{{ __('Customer Ranking') }}</h3>
            </div>
            <div class="divide-y divide-border/50">
                @forelse($topCustomers as $index => $customer)
                    <div class="px-4 py-3 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span
                                class="flex h-6 w-6 items-center justify-center rounded-md text-[10px] font-black {{ $index === 0 ? 'bg-primary text-primary-foreground' : 'bg-secondary text-muted-foreground' }}">{{ $index + 1 }}</span>
                            <div>
                                <p class="text-sm font-bold text-foreground">{{ $customer->name }}</p>
                                <p class="text-[10px] text-muted-foreground">{{ $customer->order_count }} orders ·
                                    {{ $customer->total_cups_bought }} cups</p>
                            </div>
                        </div>
                        <span class="text-sm font-black text-foreground">RM
                            {{ number_format($customer->total_spent, 2) }}</span>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-muted-foreground">{{ __('No customer data yet.') }}</div>
                @endforelse
            </div>
        </div>

        {{-- Recent Orders --}}
        <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-border flex items-center justify-between bg-muted/30">
                <h3 class="text-sm font-bold text-foreground">{{ __('Recent Orders') }}</h3>
                <a href="{{ route('shop.orders.index') }}"
                    class="text-xs font-bold text-primary hover:underline uppercase tracking-tighter">{{ __('View All') }}</a>
            </div>
            <div class="divide-y divide-border/50">
                @forelse($recentOrders as $order)
                    <a href="{{ route('shop.orders.show', $order) }}"
                        class="flex items-center justify-between px-4 py-3.5 active:bg-secondary/30 transition-colors">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-primary">{{ $order->order_number }}</span>
                                @php $sc = ['pending'=>'bg-yellow-500/10 text-yellow-600 border-yellow-500/20','preparing'=>'bg-blue-500/10 text-blue-600 border-blue-500/20','completed'=>'bg-emerald-500/10 text-emerald-600 border-emerald-500/20','cancelled'=>'bg-red-500/10 text-red-600 border-red-500/20']; @endphp
                                <span
                                    class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold uppercase border {{ $sc[$order->status] ?? '' }}">{{ $order->status }}</span>
                            </div>
                            <p class="text-xs text-muted-foreground mt-0.5">{{ $order->customer?->name ?? 'Walk-in' }} ·
                                {{ $order->total_cups }} cups · {{ $order->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="text-sm font-black text-foreground ml-3 shrink-0">RM
                            {{ number_format($order->total_amount, 2) }}</span>
                    </a>
                @empty
                    <div class="px-4 py-10 text-center text-sm text-muted-foreground">{{ __('No orders yet.') }}</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

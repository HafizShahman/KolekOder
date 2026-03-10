@extends('layouts.app')

@section('content')
    <div class="space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-foreground">{{ __('Dashboard') }}</h1>
                <p class="mt-1 text-sm text-muted-foreground">{{ __('Your stall at a glance') }}</p>
            </div>
            <a href="{{ route('coffee.orders.create') }}"
                class="inline-flex h-10 items-center justify-center rounded-xl bg-primary px-4 text-xs font-bold text-primary-foreground shadow-lg shadow-primary/20 active:scale-95 transition-all shrink-0">
                <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('New Order') }}
            </a>
        </div>

        {{-- Stats Grid: always 2 columns --}}
        <div class="grid grid-cols-2 gap-3">
            <div
                class="relative group overflow-hidden rounded-2xl bg-gradient-to-br from-primary to-primary/80 p-4 text-primary-foreground shadow-xl">
                <p class="text-[10px] font-bold uppercase tracking-widest opacity-80 mb-1">{{ __('Total Sales') }}</p>
                <span class="text-xl font-black">RM {{ number_format($totalSales, 2) }}</span>
                <div class="absolute -right-3 -bottom-3 h-16 w-16 rounded-full bg-white/10 blur-2xl"></div>
            </div>
            <div class="relative group overflow-hidden rounded-2xl bg-card border border-border p-4 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground mb-1">
                    {{ __('Cups Sold') }}</p>
                <span class="text-xl font-black text-foreground">{{ number_format($totalCups) }}</span>
            </div>
            <div class="relative group overflow-hidden rounded-2xl bg-card border border-border p-4 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground mb-1">
                    {{ __("Today's Sales") }}</p>
                <span class="text-xl font-black text-emerald-500">RM {{ number_format($todaySales, 2) }}</span>
            </div>
            <div class="relative group overflow-hidden rounded-2xl bg-card border border-border p-4 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground mb-1">
                    {{ __("Today's Cups") }}</p>
                <span class="text-xl font-black text-blue-500">{{ number_format($todayCups) }}</span>
            </div>
        </div>

        {{-- 7-Day Sales Trend --}}
        <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-border bg-muted/30">
                <h3 class="text-sm font-bold text-foreground">{{ __('7-Day Sales') }}</h3>
            </div>
            <div class="p-4">
                <div class="flex items-end gap-2 h-32">
                    @foreach ($salesTrend as $day)
                        <div class="flex-1 flex flex-col items-center gap-1.5 group">
                            <div
                                class="text-[9px] font-bold text-muted-foreground opacity-0 group-hover:opacity-100 transition-opacity">
                                RM {{ number_format($day['sales'], 0) }}</div>
                            <div class="w-full rounded-t-lg transition-all relative overflow-hidden"
                                style="height: {{ $maxDaySales > 0 ? max(($day['sales'] / $maxDaySales) * 100, 4) : 4 }}%">
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-primary to-primary/70 rounded-t-lg"
                                    style="height: 100%"></div>
                            </div>
                            <div class="text-[10px] font-bold text-foreground">{{ $day['date'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Quick Stats + Top Menu --}}
        <div
            class="rounded-2xl bg-gradient-to-br from-primary to-primary/80 p-5 text-primary-foreground shadow-xl shadow-primary/20 overflow-hidden relative">
            <div class="relative z-10">
                <h4 class="text-[10px] font-bold uppercase tracking-widest opacity-80 mb-3">{{ __('Quick Stats') }}
                </h4>
                <div class="space-y-2">
                    <div class="flex justify-between"><span class="text-sm opacity-80">{{ __('Total Orders') }}</span><span
                            class="text-base font-black">{{ number_format($totalOrders) }}</span></div>
                    <div class="h-px bg-white/20"></div>
                    <div class="flex justify-between"><span class="text-sm opacity-80">{{ __('Pending') }}</span><span
                            class="text-base font-black">{{ number_format($pendingOrders) }}</span></div>
                    <div class="h-px bg-white/20"></div>
                    <div class="flex justify-between"><span class="text-sm opacity-80">{{ __('Avg Per Cup') }}</span><span
                            class="text-base font-black">RM
                            {{ $totalCups > 0 ? number_format($totalSales / $totalCups, 2) : '0.00' }}</span></div>
                </div>
            </div>
            <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/10 blur-3xl">
            </div>
        </div>

        {{-- Recent Orders — Card Layout --}}
        <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-border flex items-center justify-between bg-muted/30">
                <h3 class="text-sm font-bold text-foreground">{{ __('Recent Orders') }}</h3>
                <a href="{{ route('coffee.orders.index') }}"
                    class="text-xs font-bold text-primary hover:underline uppercase tracking-tighter">{{ __('View All') }}</a>
            </div>
            <div class="divide-y divide-border/50">
                @forelse($recentOrders as $order)
                    <a href="{{ route('coffee.orders.show', $order) }}"
                        class="flex items-center justify-between px-4 py-3.5 active:bg-secondary/30 transition-colors">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-primary">{{ $order->order_number }}</span>
                                @php $sc = ['pending'=>'bg-yellow-500/10 text-yellow-600 border-yellow-500/20','preparing'=>'bg-blue-500/10 text-blue-600 border-blue-500/20','completed'=>'bg-emerald-500/10 text-emerald-600 border-emerald-500/20','cancelled'=>'bg-red-500/10 text-red-600 border-red-500/20']; @endphp
                                <span
                                    class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold uppercase border {{ $sc[$order->status] ?? '' }}">{{ $order->status }}</span>
                            </div>
                            <p class="text-xs text-muted-foreground mt-0.5">
                                {{ $order->customer?->name ?? 'Walk-in' }} · {{ $order->total_cups }} cups ·
                                {{ $order->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <span class="text-sm font-black text-foreground ml-3 shrink-0">RM
                            {{ number_format($order->total_amount, 2) }}</span>
                    </a>
                @empty
                    <div class="px-4 py-10 text-center text-sm text-muted-foreground">{{ __('No orders yet.') }}</div>
                @endforelse
            </div>
        </div>

        {{-- Top Menu Ranking --}}
        <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-border bg-muted/30">
                <h3 class="text-sm font-bold text-foreground flex items-center gap-2">
                    <svg class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                    {{ __('Top Menu Ranking') }}
                </h3>
            </div>
            <div class="divide-y divide-border/50">
                @forelse($topMenuItems as $index => $item)
                    <div class="px-4 py-3">
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center gap-2.5">
                                <span
                                    class="flex h-6 w-6 items-center justify-center rounded-md text-[10px] font-black {{ $index === 0 ? 'bg-primary text-primary-foreground' : ($index === 1 ? 'bg-gray-300 text-gray-700' : ($index === 2 ? 'bg-amber-700/60 text-white' : 'bg-secondary text-muted-foreground')) }}">{{ $index + 1 }}</span>
                                <div>
                                    <p class="text-sm font-bold text-foreground">{{ $item->name }}</p>
                                    <p class="text-[10px] text-muted-foreground uppercase tracking-wider">
                                        {{ $item->category }}</p>
                                </div>
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
    </div>
@endsection

@extends('layouts.shop')

@section('content')
    <div class="space-y-5 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-foreground">{{ __('Orders') }}</h1>
            <p class="mt-1 text-sm text-muted-foreground">{{ __('Manage your order list') }}</p>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-4 gap-2">
            <div class="rounded-xl bg-card border border-border p-3 text-center shadow-sm">
                <p class="text-[9px] font-bold uppercase tracking-widest text-muted-foreground">All</p>
                <p class="text-lg font-black text-foreground">{{ $totalOrders }}</p>
            </div>
            <div class="rounded-xl bg-yellow-500/10 border border-yellow-500/20 p-3 text-center">
                <p class="text-[9px] font-bold uppercase tracking-widest text-yellow-600">Pending</p>
                <p class="text-lg font-black text-yellow-600">{{ $pendingCount }}</p>
            </div>
            <div class="rounded-xl bg-blue-500/10 border border-blue-500/20 p-3 text-center">
                <p class="text-[9px] font-bold uppercase tracking-widest text-blue-600">Prep</p>
                <p class="text-lg font-black text-blue-600">{{ $preparingCount }}</p>
            </div>
            <div class="rounded-xl bg-emerald-500/10 border border-emerald-500/20 p-3 text-center">
                <p class="text-[9px] font-bold uppercase tracking-widest text-emerald-600">Done</p>
                <p class="text-lg font-black text-emerald-600">{{ $completedCount }}</p>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" class="flex flex-wrap gap-2">
            <select name="status"
                class="h-9 rounded-lg bg-card border border-border px-3 text-xs font-bold text-foreground"
                onchange="this.form.submit()">
                <option value="">All Status</option>
                @foreach (['pending', 'preparing', 'completed', 'cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}
                    </option>
                @endforeach
            </select>
            <select name="type"
                class="h-9 rounded-lg bg-card border border-border px-3 text-xs font-bold text-foreground"
                onchange="this.form.submit()">
                <option value="">All Types</option>
                <option value="single" {{ request('type') === 'single' ? 'selected' : '' }}>Single</option>
                <option value="bulk" {{ request('type') === 'bulk' ? 'selected' : '' }}>Bulk</option>
            </select>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                class="h-9 flex-1 min-w-[120px] rounded-lg bg-card border border-border px-3 text-xs text-foreground placeholder:text-muted-foreground/50">
        </form>

        {{-- Orders List --}}
        <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden">
            <div class="divide-y divide-border/50">
                @forelse($orders as $order)
                    <div class="p-4 border-b border-border/50 bg-background hover:bg-muted/10 transition-colors"
                        x-data="{ expanded: false }">
                        <div class="flex items-start justify-between mb-3">
                            <button type="button" @click="expanded = !expanded"
                                class="flex-1 min-w-0 group text-left flex items-start gap-3">
                                <div class="mt-1 shrink-0 text-muted-foreground transition-transform"
                                    :class="expanded ? 'rotate-180' : ''">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0 shrink">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span
                                            class="text-sm font-bold text-primary group-hover:underline decoration-primary/50 underline-offset-4">{{ $order->order_number }}</span>
                                        @php $sc = ['pending'=>'bg-yellow-500/10 text-yellow-600 border-yellow-500/20','preparing'=>'bg-blue-500/10 text-blue-600 border-blue-500/20','completed'=>'bg-emerald-500/10 text-emerald-600 border-emerald-500/20','cancelled'=>'bg-red-500/10 text-red-600 border-red-500/20']; @endphp
                                        <span
                                            class="inline-flex items-center w-max px-1.5 py-0.5 rounded text-[9px] font-bold uppercase border {{ $sc[$order->status] ?? '' }}">{{ $order->status }}</span>
                                        @if ($order->type === 'bulk')
                                            <span
                                                class="inline-flex w-max items-center px-1.5 py-0.5 rounded text-[9px] font-bold uppercase bg-purple-500/10 text-purple-600 border border-purple-500/20">Bulk</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-muted-foreground truncate">
                                        {{ $order->customer?->name ?? 'Walk-in' }} · {{ $order->total_cups }} cups ·
                                        {{ $order->created_at->diffForHumans() }}</p>
                                </div>
                            </button>

                            <div class="text-right shrink-0 ml-4 flex flex-col items-end gap-2">
                                <span class="text-sm font-black text-foreground">RM
                                    {{ number_format($order->total_amount, 2) }}</span>

                                @if (in_array($order->status, ['pending', 'preparing']))
                                    <form method="POST" action="{{ route('shop.orders.update-status', $order) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit"
                                            class="h-8 px-3 rounded-lg text-xs font-bold bg-primary text-primary-foreground hover:bg-primary/90 hover:scale-105 active:scale-95 transition-all shadow-sm shadow-primary/20">
                                            Done Order
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        {{-- Order Items Compact List (Hidden by default, expands on click) --}}
                        <div x-show="expanded" x-collapse x-cloak>
                            <div class="bg-card/50 rounded-xl p-3 text-xs space-y-1.5 mt-2 border border-border/50">
                                @foreach ($order->items as $item)
                                    <div class="flex justify-between items-start">
                                        <div class="flex flex-wrap items-center gap-1.5 min-w-0 pr-2">
                                            <span class="font-bold text-foreground shrink-0">{{ $item->quantity }}x</span>
                                            <span class="text-foreground/90 shrink-0">{{ $item->product->name }}</span>

                                            @if ($item->variant || $item->addons)
                                                <span class="text-muted-foreground text-[10px] truncate">
                                                    (@if ($item->variant)
                                                        {{ $item->variant }}
                                                        @endif{{ $item->variant && $item->addons ? ', ' : '' }}@if ($item->addons)
                                                            {{ collect($item->addons)->pluck('name')->join(', ') }}
                                                        @endif)
                                                </span>
                                            @endif
                                        </div>
                                        <span class="font-medium text-foreground/80 shrink-0">RM
                                            {{ number_format($item->subtotal, 2) }}</span>
                                    </div>
                                @endforeach
                            </div>

                            @if ($order->notes)
                                <p class="text-[10px] text-muted-foreground mt-2 italic flex items-center gap-1">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $order->notes }}
                                </p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-10 text-center text-sm text-muted-foreground">{{ __('No orders found.') }}</div>
                @endforelse
            </div>
        </div>

        {{ $orders->links() }}
    </div>
@endsection

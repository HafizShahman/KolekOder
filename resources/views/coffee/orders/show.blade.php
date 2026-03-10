@extends('layouts.app')
@section('content')
    <div class="space-y-5">
        <div class="flex items-center gap-3">
            <a href="{{ route('coffee.orders.index') }}"
                class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-secondary text-foreground active:bg-secondary/80 transition-all shrink-0">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-foreground">{{ $order->order_number }}</h1>
                <p class="text-xs text-muted-foreground">{{ $order->created_at->format('d M Y, h:iA') }}</p>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/20 p-3 rounded-xl">
                <p class="text-sm font-bold text-emerald-600">{{ session('success') }}</p>
            </div>
        @endif

        {{-- Status & Actions --}}
        <div class="bg-card border border-border rounded-2xl shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground mb-1.5">Status</p>
                    @php $sc=['pending'=>'bg-yellow-500/10 text-yellow-600 border-yellow-500/20','preparing'=>'bg-blue-500/10 text-blue-600 border-blue-500/20','completed'=>'bg-emerald-500/10 text-emerald-600 border-emerald-500/20','cancelled'=>'bg-red-500/10 text-red-600 border-red-500/20']; @endphp
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold uppercase border {{ $sc[$order->status] ?? '' }}">{{ $order->status }}</span>
                </div>
                <span
                    class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase {{ $order->type === 'bulk' ? 'bg-purple-500/10 text-purple-600 border border-purple-500/20' : 'bg-sky-500/10 text-sky-600 border border-sky-500/20' }}">{{ $order->type }}</span>
            </div>
            @if (in_array($order->status, ['pending', 'preparing']))
                <div class="mt-4 flex gap-2">
                    @if ($order->status === 'pending')
                        <form action="{{ route('coffee.orders.update-status', $order) }}" method="POST" class="flex-1">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="preparing">
                            <button
                                class="w-full h-11 rounded-xl bg-blue-600 text-white text-xs font-bold active:scale-95 transition-all">
                                Start Preparing
                            </button>
                        </form>
                    @endif
                    @if ($order->status === 'preparing')
                        <form action="{{ route('coffee.orders.update-status', $order) }}" method="POST" class="flex-1">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="completed">
                            <button
                                class="w-full h-11 rounded-xl bg-emerald-600 text-white text-xs font-bold active:scale-95 transition-all">
                                Mark Completed
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('coffee.orders.update-status', $order) }}" method="POST">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="cancelled">
                        <button
                            class="h-11 px-4 rounded-xl bg-secondary text-destructive text-xs font-bold border border-border active:scale-95 transition-all">
                            Cancel
                        </button>
                    </form>
                </div>
            @endif
        </div>

        {{-- Customer Info --}}
        <div class="bg-card border border-border rounded-2xl shadow-sm p-4">
            <p class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground mb-1.5">Customer</p>
            @if ($order->customer)
                <p class="text-sm font-bold text-foreground">{{ $order->customer->name }}</p>
                @if ($order->customer->phone)
                    <p class="text-xs text-muted-foreground mt-0.5">{{ $order->customer->phone }}</p>
                @endif
            @else
                <p class="text-sm text-muted-foreground">Walk-in</p>
            @endif
        </div>

        {{-- Order Items — Card List --}}
        <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-border bg-muted/30">
                <h3 class="text-sm font-bold text-foreground">Order Items</h3>
            </div>
            <div class="divide-y divide-border/50">
                @foreach ($order->items as $item)
                    <div class="px-4 py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-foreground">{{ $item->menuItem->name }}</p>
                            <p class="text-[10px] text-muted-foreground uppercase mt-0.5">
                                {{ $item->menuItem->category }} · RM {{ number_format($item->unit_price, 2) }} ×
                                {{ $item->quantity }}
                            </p>
                        </div>
                        <p class="text-sm font-bold text-foreground">RM {{ number_format($item->subtotal, 2) }}</p>
                    </div>
                @endforeach
            </div>
            <div class="px-4 py-3 border-t-2 border-border bg-muted/30 flex items-center justify-between">
                <div>
                    <p class="text-sm font-bold text-foreground">Total</p>
                    <p class="text-[10px] text-muted-foreground">{{ $order->total_cups }} cups</p>
                </div>
                <p class="text-lg font-black text-primary">RM {{ number_format($order->total_amount, 2) }}</p>
            </div>
        </div>

        @if ($order->notes)
            <div class="bg-card border border-border rounded-2xl shadow-sm p-4">
                <p class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground mb-1.5">Notes</p>
                <p class="text-sm text-foreground">{{ $order->notes }}</p>
            </div>
        @endif
    </div>
@endsection

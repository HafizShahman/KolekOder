@extends('layouts.shop')

@section('content')
    <div class="space-y-5 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="flex items-center gap-3">
            <a href="{{ route('shop.orders.index') }}"
                class="h-10 w-10 rounded-xl bg-secondary flex items-center justify-center shrink-0">
                <svg class="h-5 w-5 text-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-extrabold tracking-tight text-foreground">{{ $order->order_number }}</h1>
                <p class="text-xs text-muted-foreground">{{ $order->created_at->format('d M Y, h:i A') }}</p>
            </div>
        </div>

        {{-- Status --}}
        <div class="bg-card border border-border rounded-2xl p-4 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div>
                    @php $sc = ['pending'=>'bg-yellow-500/10 text-yellow-600 border-yellow-500/20','preparing'=>'bg-blue-500/10 text-blue-600 border-blue-500/20','completed'=>'bg-emerald-500/10 text-emerald-600 border-emerald-500/20','cancelled'=>'bg-red-500/10 text-red-600 border-red-500/20']; @endphp
                    <span
                        class="inline-flex items-center px-2 py-1 rounded-lg text-[10px] font-bold uppercase border {{ $sc[$order->status] ?? '' }}">{{ $order->status }}</span>
                    <span
                        class="ml-2 text-xs text-muted-foreground">{{ $order->type === 'bulk' ? 'Bulk Order' : 'Single Order' }}</span>
                </div>
            </div>

            <form method="POST" action="{{ route('shop.orders.update-status', $order) }}" class="flex gap-2">
                @csrf @method('PATCH')
                @foreach (['pending', 'preparing', 'completed', 'cancelled'] as $s)
                    @if ($s !== $order->status)
                        <button type="submit" name="status" value="{{ $s }}"
                            class="px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase bg-secondary text-foreground hover:bg-primary hover:text-primary-foreground transition-all">
                            {{ ucfirst($s) }}
                        </button>
                    @endif
                @endforeach
            </form>
        </div>

        {{-- Customer --}}
        <div class="bg-card border border-border rounded-2xl p-4 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground mb-1">Customer</p>
            <p class="text-sm font-bold text-foreground">{{ $order->customer?->name ?? 'Walk-in' }}</p>
            @if ($order->customer?->phone)
                <p class="text-xs text-muted-foreground">{{ $order->customer->phone }}</p>
            @endif
        </div>

        {{-- Items --}}
        <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-border bg-muted/30">
                <h3 class="text-sm font-bold text-foreground">{{ __('Order Items') }}</h3>
            </div>
            <div class="divide-y divide-border/50">
                @foreach ($order->items as $item)
                    <div class="px-4 py-3">
                        <div class="flex justify-between">
                            <div>
                                <p class="text-sm font-bold text-foreground">{{ $item->product->name }}</p>
                                <div class="flex gap-2 mt-1">
                                    @if ($item->variant)
                                        <span
                                            class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase bg-blue-500/10 text-blue-600">{{ $item->variant }}</span>
                                    @endif
                                    @if ($item->addons)
                                        @foreach ($item->addons as $addon)
                                            <span
                                                class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase bg-purple-500/10 text-purple-600">{{ $addon['name'] }}</span>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-foreground">RM {{ number_format($item->subtotal, 2) }}</p>
                                <p class="text-[10px] text-muted-foreground">{{ $item->quantity }} × RM
                                    {{ number_format($item->unit_price, 2) }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="px-4 py-3 border-t border-border bg-muted/30 flex justify-between">
                <span class="text-sm font-bold text-foreground">Total ({{ $order->total_cups }} cups)</span>
                <span class="text-lg font-black text-primary">RM {{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>

        @if ($order->notes)
            <div class="bg-card border border-border rounded-2xl p-4 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground mb-1">Notes</p>
                <p class="text-sm text-foreground">{{ $order->notes }}</p>
            </div>
        @endif
    </div>
@endsection

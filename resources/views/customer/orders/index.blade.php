@extends('layouts.customer')

@section('content')
    <div class="space-y-5 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <h1 class="text-2xl font-extrabold tracking-tight text-foreground">{{ __('My Orders') }}</h1>

        <form method="GET" class="flex gap-2">
            <select name="status" class="h-9 rounded-lg bg-card border border-border px-3 text-xs font-bold text-foreground"
                onchange="this.form.submit()">
                <option value="">All Status</option>
                @foreach (['pending', 'preparing', 'completed', 'cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}
                    </option>
                @endforeach
            </select>
        </form>

        <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden">
            <div class="divide-y divide-border/50">
                @forelse($orders as $order)
                    <div class="px-4 py-3.5">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-sm font-bold text-foreground">{{ $order->order_number }}</span>
                                @php $sc = ['pending'=>'bg-yellow-500/10 text-yellow-600 border-yellow-500/20','preparing'=>'bg-blue-500/10 text-blue-600 border-blue-500/20','completed'=>'bg-emerald-500/10 text-emerald-600 border-emerald-500/20','cancelled'=>'bg-red-500/10 text-red-600 border-red-500/20']; @endphp
                                <span
                                    class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold uppercase border {{ $sc[$order->status] ?? '' }} ml-1">{{ $order->status }}</span>
                                <p class="text-xs text-muted-foreground mt-0.5">{{ $order->shop?->shop_name }} ·
                                    {{ $order->total_cups }} cups · {{ $order->created_at->diffForHumans() }}</p>
                            </div>
                            <span class="text-sm font-black text-foreground">RM
                                {{ number_format($order->total_amount, 2) }}</span>
                        </div>
                        <div class="flex flex-wrap gap-1 mt-2">
                            @foreach ($order->items as $item)
                                <span
                                    class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-secondary text-foreground">{{ $item->product->name }}
                                    ×{{ $item->quantity }}</span>
                            @endforeach
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

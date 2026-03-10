@extends('layouts.app')

@section('content')
    <div class="space-y-5 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-foreground">{{ __('Orders') }}</h1>
                <p class="mt-1 text-sm text-muted-foreground">{{ __('Single and bulk orders') }}</p>
            </div>
            <a href="{{ route('coffee.orders.create') }}"
                class="inline-flex h-10 items-center justify-center rounded-xl bg-primary px-4 text-xs font-bold text-primary-foreground shadow-lg shadow-primary/20 active:scale-95 transition-all shrink-0">
                <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('New Order') }}
            </a>
        </div>

        @if (session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/20 p-3 rounded-xl">
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-emerald-500 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm font-bold text-emerald-600">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- Status Filter Chips --}}
        <div class="flex items-center gap-2 overflow-x-auto scrollbar-none -mx-4 px-4 pb-1">
            <a href="{{ route('coffee.orders.index') }}"
                class="inline-flex items-center px-3.5 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all border whitespace-nowrap {{ !request('status') ? 'bg-primary text-primary-foreground border-primary shadow-lg shadow-primary/20' : 'bg-card text-muted-foreground border-border active:bg-secondary' }}">{{ __('All') }}
                <span class="ml-1.5 text-[10px] opacity-80">{{ $totalOrders }}</span></a>
            @foreach (['pending' => $pendingCount, 'preparing' => $preparingCount, 'completed' => $completedCount] as $st => $cnt)
                <a href="{{ route('coffee.orders.index', ['status' => $st]) }}"
                    class="inline-flex items-center px-3.5 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all border whitespace-nowrap {{ request('status') === $st ? 'bg-primary text-primary-foreground border-primary shadow-lg shadow-primary/20' : 'bg-card text-muted-foreground border-border active:bg-secondary' }}">{{ ucfirst($st) }}
                    <span class="ml-1.5 text-[10px] opacity-80">{{ $cnt }}</span></a>
            @endforeach
        </div>

        {{-- Search & Filter --}}
        <div class="bg-card border border-border rounded-2xl p-3">
            <form action="{{ route('coffee.orders.index') }}" method="GET" class="space-y-3">
                @if (request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="{{ __('Search order or customer...') }}"
                        class="w-full h-10 px-4 rounded-xl bg-background border border-border text-sm focus:ring-2 focus:ring-primary/50 transition-all">
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <select name="type"
                        class="h-10 px-3 rounded-xl bg-background border border-border text-xs focus:ring-2 focus:ring-primary/50">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="single" {{ request('type') === 'single' ? 'selected' : '' }}>Single</option>
                        <option value="bulk" {{ request('type') === 'bulk' ? 'selected' : '' }}>Bulk</option>
                    </select>
                    <input type="date" name="from" value="{{ request('from') }}"
                        class="h-10 px-3 rounded-xl bg-background border border-border text-xs focus:ring-2 focus:ring-primary/50"
                        placeholder="{{ __('From') }}">
                    <input type="date" name="to" value="{{ request('to') }}"
                        class="h-10 px-3 rounded-xl bg-background border border-border text-xs focus:ring-2 focus:ring-primary/50"
                        placeholder="{{ __('To') }}">
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 h-10 rounded-xl bg-primary text-primary-foreground text-xs font-bold active:scale-95 transition-all">{{ __('Filter') }}</button>
                    @if (request()->hasAny(['search', 'type', 'from', 'to', 'status']))
                        <a href="{{ route('coffee.orders.index') }}"
                            class="h-10 px-4 inline-flex items-center justify-center rounded-xl text-xs font-bold text-muted-foreground border border-border active:bg-secondary transition-all">{{ __('Clear') }}</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Orders — Card List --}}
        <div class="space-y-3">
            @forelse($orders as $order)
                <a href="{{ route('coffee.orders.show', $order) }}"
                    class="block bg-card border border-border rounded-2xl p-4 shadow-sm active:bg-secondary/30 transition-all">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm font-bold text-primary">{{ $order->order_number }}</span>
                                <span
                                    class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold uppercase {{ $order->type === 'bulk' ? 'bg-purple-500/10 text-purple-600 border border-purple-500/20' : 'bg-sky-500/10 text-sky-600 border border-sky-500/20' }}">{{ $order->type }}</span>
                                @php $sc=['pending'=>'bg-yellow-500/10 text-yellow-600 border-yellow-500/20','preparing'=>'bg-blue-500/10 text-blue-600 border-blue-500/20','completed'=>'bg-emerald-500/10 text-emerald-600 border-emerald-500/20','cancelled'=>'bg-red-500/10 text-red-600 border-red-500/20']; @endphp
                                <span
                                    class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold uppercase border {{ $sc[$order->status] ?? '' }}">{{ $order->status }}</span>
                            </div>
                            <p class="text-xs text-muted-foreground mt-1">
                                {{ $order->customer?->name ?? 'Walk-in' }} ·
                                {{ $order->total_cups }} cups ·
                                {{ $order->created_at->format('d M, h:iA') }}
                            </p>
                            <p class="text-[10px] text-muted-foreground mt-0.5 truncate">
                                {{ $order->items->map(fn($i) => $i->menuItem->name)->join(', ') }}
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-base font-black text-foreground">RM
                                {{ number_format($order->total_amount, 2) }}</p>
                            @if (in_array($order->status, ['pending', 'preparing']))
                                <div class="mt-2 flex gap-1.5 justify-end"
                                    onclick="event.preventDefault(); event.stopPropagation();">
                                    @if ($order->status === 'pending')
                                        <form action="{{ route('coffee.orders.update-status', $order) }}" method="POST">
                                            @csrf @method('PATCH')<input type="hidden" name="status"
                                                value="preparing"><button
                                                class="h-8 px-3 rounded-lg bg-blue-600 text-white text-[10px] font-bold active:scale-95 transition-all">Prepare</button>
                                        </form>
                                    @elseif($order->status === 'preparing')
                                        <form action="{{ route('coffee.orders.update-status', $order) }}" method="POST">
                                            @csrf @method('PATCH')<input type="hidden" name="status"
                                                value="completed"><button
                                                class="h-8 px-3 rounded-lg bg-emerald-600 text-white text-[10px] font-bold active:scale-95 transition-all">Complete</button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="bg-card border border-border rounded-2xl p-12 text-center">
                    <svg class="h-10 w-10 text-muted-foreground/30 mx-auto mb-3" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 8h1a4 4 0 110 8h-1M3 8h14v9a4 4 0 01-4 4H7a4 4 0 01-4-4V8zm0 0V6a2 2 0 012-2h10a2 2 0 012 2v2" />
                    </svg>
                    <p class="text-sm text-muted-foreground">{{ __('No orders found') }}</p>
                </div>
            @endforelse
        </div>

        @if ($orders->hasPages())
            <div class="flex justify-center">{{ $orders->links() }}</div>
        @endif
    </div>
@endsection

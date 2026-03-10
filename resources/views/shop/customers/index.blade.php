@extends('layouts.shop')

@section('content')
    <div class="space-y-5 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-foreground">{{ __('Customers') }}</h1>
                <p class="mt-1 text-sm text-muted-foreground">{{ __('Your customer list') }}</p>
            </div>
        </div>

        {{-- Search --}}
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or phone..."
                class="h-10 flex-1 rounded-xl bg-card border border-border px-4 text-sm text-foreground placeholder:text-muted-foreground/50 focus:outline-none focus:ring-2 focus:ring-primary/50">
            <button class="h-10 px-4 rounded-xl bg-primary text-primary-foreground text-xs font-bold">Search</button>
        </form>

        {{-- Add Customer --}}
        <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden" x-data="{ open: false }">
            <button @click="open = !open"
                class="w-full px-4 py-3 flex items-center justify-between text-sm font-bold text-foreground bg-muted/30">
                <span>{{ __('Add New Customer') }}</span>
                <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open" x-cloak class="p-4 border-t border-border">
                <form method="POST" action="{{ route('shop.customers.store') }}" class="space-y-3">
                    @csrf
                    <input type="text" name="name" placeholder="Customer Name" required
                        class="w-full h-10 rounded-xl bg-background border border-border px-4 text-sm text-foreground placeholder:text-muted-foreground/50">
                    <input type="text" name="phone" placeholder="Phone (optional)"
                        class="w-full h-10 rounded-xl bg-background border border-border px-4 text-sm text-foreground placeholder:text-muted-foreground/50">
                    <textarea name="notes" rows="2" placeholder="Notes (optional)"
                        class="w-full rounded-xl bg-background border border-border px-4 py-2 text-sm text-foreground placeholder:text-muted-foreground/50 resize-none"></textarea>
                    <button type="submit"
                        class="w-full h-10 bg-primary text-primary-foreground text-xs font-bold uppercase rounded-xl shadow-lg shadow-primary/20">Add
                        Customer</button>
                </form>
            </div>
        </div>

        {{-- Customers List --}}
        <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden">
            <div class="divide-y divide-border/50">
                @forelse($customers as $customer)
                    <div class="px-4 py-3.5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-bold text-foreground">{{ $customer->name }}</p>
                                <p class="text-[10px] text-muted-foreground">{{ $customer->phone ?? 'No phone' }} ·
                                    {{ $customer->orders_count }} orders</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-foreground">{{ $customer->collect_points }} <span
                                        class="text-[10px] text-muted-foreground">pts</span></p>
                                <p class="text-[10px] text-muted-foreground">RM
                                    {{ number_format($customer->total_spent ?? 0, 2) }}</p>
                            </div>
                        </div>
                        @if (isset($favorites[$customer->id]))
                            <div class="mt-2">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-lg text-[9px] font-bold bg-primary/10 text-primary">
                                    ❤ {{ $favorites[$customer->id] }}
                                </span>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="px-4 py-10 text-center text-sm text-muted-foreground">{{ __('No customers yet.') }}</div>
                @endforelse
            </div>
        </div>

        {{ $customers->links() }}
    </div>
@endsection

@extends('layouts.app')
@section('content')
    <div class="space-y-5" x-data="{ showModal: false }">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-foreground">{{ __('Customers') }}</h1>
                <p class="mt-1 text-sm text-muted-foreground">{{ __('Regular patrons') }}</p>
            </div>
            <button @click="showModal=true"
                class="inline-flex h-10 items-center justify-center rounded-xl bg-primary px-4 text-xs font-bold text-primary-foreground shadow-lg shadow-primary/20 active:scale-95 transition-all shrink-0">
                <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>{{ __('Add') }}
            </button>
        </div>

        @if (session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/20 p-3 rounded-xl">
                <p class="text-sm font-bold text-emerald-600">{{ session('success') }}</p>
            </div>
        @endif

        {{-- Search --}}
        <div class="bg-card border border-border rounded-2xl p-3">
            <form action="{{ route('coffee.customers.index') }}" method="GET" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or phone..."
                    class="flex-1 h-10 px-4 rounded-xl bg-background border border-border text-sm focus:ring-2 focus:ring-primary/50 transition-all">
                <button type="submit"
                    class="h-10 px-4 rounded-xl bg-primary text-primary-foreground text-xs font-bold shrink-0 active:scale-95 transition-all">Search</button>
                @if (request('search'))
                    <a href="{{ route('coffee.customers.index') }}"
                        class="h-10 px-3 inline-flex items-center rounded-xl text-xs font-bold text-muted-foreground border border-border active:bg-secondary shrink-0">Clear</a>
                @endif
            </form>
        </div>

        {{-- Customer Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @forelse($customers as $customer)
                <div
                    class="bg-card border border-border rounded-2xl shadow-sm p-4 active:shadow-md transition-all duration-300">
                    <div class="flex items-start gap-3">
                        <div
                            class="h-10 w-10 rounded-xl bg-primary/10 flex items-center justify-center text-base font-black text-primary shrink-0">
                            {{ substr($customer->name, 0, 1) }}</div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-foreground truncate">{{ $customer->name }}</h3>
                            <p class="text-xs text-muted-foreground mt-0.5">{{ $customer->phone ?? 'No phone' }}</p>
                        </div>
                    </div>
                    <div class="mt-3 grid grid-cols-2 gap-2">
                        <div class="bg-secondary/50 rounded-xl p-2.5 text-center">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Orders</p>
                            <p class="text-lg font-black text-foreground mt-0.5">{{ $customer->orders_count }}</p>
                        </div>
                        <div class="bg-secondary/50 rounded-xl p-2.5 text-center">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Spent</p>
                            <p class="text-lg font-black text-primary mt-0.5">RM
                                {{ number_format($customer->total_spent ?? 0, 0) }}</p>
                        </div>
                    </div>
                    @if (isset($favorites[$customer->id]))
                        <div
                            class="mt-3 flex items-center gap-2 px-2.5 py-1.5 rounded-lg bg-primary/5 border border-primary/10">
                            <svg class="h-3.5 w-3.5 text-primary shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span class="text-xs font-bold text-primary truncate">{{ $favorites[$customer->id] }}</span>
                        </div>
                    @endif
                </div>
            @empty
                <div class="col-span-full py-12 text-center">
                    <p class="text-sm text-muted-foreground">No customers found</p>
                </div>
            @endforelse
        </div>

        @if ($customers->hasPages())
            <div class="flex justify-center">{{ $customers->links() }}</div>
        @endif

        {{-- Add Customer Modal --}}
        <div x-show="showModal" x-cloak
            class="fixed inset-0 z-100 flex items-end sm:items-center justify-center bg-background/80 backdrop-blur-sm"
            x-transition>
            <div @click.away="showModal=false"
                class="relative w-full max-w-md rounded-t-3xl sm:rounded-3xl bg-card border border-border p-6 shadow-2xl pb-safe"
                x-transition>
                <div class="w-12 h-1 bg-border rounded-full mx-auto mb-4 sm:hidden"></div>
                <button @click="showModal=false"
                    class="absolute right-4 top-4 rounded-full p-2 text-muted-foreground active:bg-secondary">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <h3 class="text-xl font-bold text-foreground mb-5">Add Customer</h3>
                <form action="{{ route('coffee.customers.store') }}" method="POST" class="space-y-4">@csrf
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground mb-1 block">Name
                            *</label>
                        <input type="text" name="name" required
                            class="w-full h-11 px-4 rounded-xl bg-background border border-border text-sm focus:ring-2 focus:ring-primary/50"
                            placeholder="Customer name">
                    </div>
                    <div>
                        <label
                            class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground mb-1 block">Phone</label>
                        <input type="text" name="phone"
                            class="w-full h-11 px-4 rounded-xl bg-background border border-border text-sm focus:ring-2 focus:ring-primary/50"
                            placeholder="012-3456789">
                    </div>
                    <div>
                        <label
                            class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground mb-1 block">Notes</label>
                        <textarea name="notes" rows="2"
                            class="w-full px-4 py-2 rounded-xl bg-background border border-border text-sm focus:ring-2 focus:ring-primary/50 resize-none"
                            placeholder="Notes..."></textarea>
                    </div>
                    <button type="submit"
                        class="w-full h-12 rounded-xl bg-primary text-primary-foreground text-sm font-bold shadow-lg shadow-primary/20 active:scale-[0.98] transition-all">Add
                        Customer</button>
                </form>
            </div>
        </div>
    </div>
@endsection

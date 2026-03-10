@extends('layouts.admin')

@section('content')
    <div class="space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-foreground">{{ __('System Dashboard') }}</h1>
            <p class="mt-1 text-sm text-muted-foreground">{{ __('Platform overview') }}</p>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-2 gap-3">
            <div
                class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-violet-600 to-violet-600/80 p-4 text-white shadow-xl col-span-2">
                <p class="text-[10px] font-bold uppercase tracking-widest opacity-80 mb-1">{{ __('Total Income') }}</p>
                <span class="text-3xl font-black">RM {{ number_format($totalIncome, 2) }}</span>
                <div class="absolute -right-3 -bottom-3 h-16 w-16 rounded-full bg-white/10 blur-2xl"></div>
            </div>
            <div class="rounded-2xl bg-card border border-border p-4 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground mb-1">Total Tenants</p>
                <span class="text-xl font-black text-foreground">{{ $totalTenants }}</span>
            </div>
            <div class="rounded-2xl bg-card border border-border p-4 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground mb-1">Total Orders</p>
                <span class="text-xl font-black text-foreground">{{ number_format($totalOrders) }}</span>
            </div>
            <div class="rounded-2xl bg-emerald-500/10 border border-emerald-500/20 p-4">
                <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 mb-1">Active</p>
                <span class="text-xl font-black text-emerald-600">{{ $activeTenants }}</span>
            </div>
            <div class="rounded-2xl bg-red-500/10 border border-red-500/20 p-4">
                <p class="text-[10px] font-bold uppercase tracking-widest text-red-600 mb-1">Inactive</p>
                <span class="text-xl font-black text-red-600">{{ $inactiveTenants }}</span>
            </div>
        </div>

        {{-- Total cups --}}
        <div class="rounded-2xl bg-card border border-border p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground mb-1">
                {{ __('Total Cups Sold (Platform)') }}</p>
            <p class="text-2xl font-black text-foreground">{{ number_format($totalCups) }}</p>
        </div>

        {{-- Recent Shops --}}
        <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-border flex items-center justify-between bg-muted/30">
                <h3 class="text-sm font-bold text-foreground">{{ __('Recent Tenants') }}</h3>
                <a href="{{ route('admin.tenants.index') }}"
                    class="text-xs font-bold text-violet-600 hover:underline uppercase tracking-tighter">View All</a>
            </div>
            <div class="divide-y divide-border/50">
                @forelse($recentShops as $shop)
                    <a href="{{ route('admin.tenants.show', $shop) }}"
                        class="flex items-center justify-between px-4 py-3.5 active:bg-secondary/30 transition-colors">
                        <div>
                            <p class="text-sm font-bold text-foreground">{{ $shop->shop_name }}</p>
                            <p class="text-xs text-muted-foreground">{{ $shop->user->name }} ·
                                {{ $shop->created_at->diffForHumans() }}</p>
                        </div>
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded-lg text-[9px] font-bold uppercase {{ $shop->is_active ? 'bg-emerald-500/10 text-emerald-600' : 'bg-red-500/10 text-red-600' }}">
                            {{ $shop->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </a>
                @empty
                    <div class="px-4 py-10 text-center text-sm text-muted-foreground">{{ __('No tenants yet.') }}</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@extends('layouts.admin')

@section('content')
    <div class="space-y-5 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.tenants.index') }}"
                class="h-10 w-10 rounded-xl bg-secondary flex items-center justify-center shrink-0">
                <svg class="h-5 w-5 text-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-extrabold tracking-tight text-foreground">{{ $shop->shop_name }}</h1>
                <p class="text-xs text-muted-foreground">{{ $shop->user->name }} · {{ $shop->user->email }}</p>
            </div>
        </div>

        {{-- Status --}}
        <div class="flex items-center gap-3">
            <span
                class="inline-flex items-center px-3 py-1 rounded-xl text-xs font-bold uppercase {{ $shop->is_active ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20' : 'bg-red-500/10 text-red-600 border border-red-500/20' }}">
                {{ $shop->is_active ? 'Active' : 'Inactive' }}
            </span>
            <form method="POST" action="{{ route('admin.tenants.toggle', $shop) }}">
                @csrf @method('PATCH')
                <button type="submit"
                    class="px-4 py-1.5 rounded-xl text-xs font-bold {{ $shop->is_active ? 'bg-red-500/10 text-red-600' : 'bg-emerald-500/10 text-emerald-600' }}">
                    {{ $shop->is_active ? 'Deactivate' : 'Activate' }}
                </button>
            </form>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-2 gap-3">
            <div class="rounded-2xl bg-card border border-border p-4 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground mb-1">Total Orders</p>
                <span class="text-xl font-black text-foreground">{{ number_format($totalOrders) }}</span>
            </div>
            <div class="rounded-2xl bg-card border border-border p-4 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground mb-1">Total Sales</p>
                <span class="text-lg font-black text-foreground">RM {{ number_format($totalSales, 2) }}</span>
            </div>
            <div class="rounded-2xl bg-card border border-border p-4 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground mb-1">Total Cups</p>
                <span class="text-xl font-black text-foreground">{{ number_format($totalCups) }}</span>
            </div>
            <div class="rounded-2xl bg-card border border-border p-4 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground mb-1">Products</p>
                <span class="text-xl font-black text-foreground">{{ $products }}</span>
            </div>
        </div>

        {{-- Details --}}
        <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-border bg-muted/30">
                <h3 class="text-sm font-bold text-foreground">{{ __('Tenant Details') }}</h3>
            </div>
            <div class="p-4 space-y-3">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Shop Address</p>
                    <p class="text-sm text-foreground">{{ $shop->shop_address ?? 'Not set' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Customers</p>
                    <p class="text-sm text-foreground">{{ $customers }} registered customers</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Registered</p>
                    <p class="text-sm text-foreground">{{ $shop->created_at->format('d M Y, h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection

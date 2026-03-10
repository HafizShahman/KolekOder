@extends('layouts.admin')

@section('content')
    <div class="space-y-5 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <h1 class="text-2xl font-extrabold tracking-tight text-foreground">{{ __('Tenant List') }}</h1>

        {{-- Filters --}}
        <form method="GET" class="flex flex-wrap gap-2">
            <select name="status" class="h-9 rounded-lg bg-card border border-border px-3 text-xs font-bold text-foreground"
                onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                class="h-9 flex-1 min-w-[120px] rounded-lg bg-card border border-border px-3 text-xs text-foreground placeholder:text-muted-foreground/50">
        </form>

        <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden">
            <div class="divide-y divide-border/50">
                @forelse($tenants as $tenant)
                    <div class="px-4 py-3.5">
                        <div class="flex items-center justify-between">
                            <a href="{{ route('admin.tenants.show', $tenant) }}" class="flex-1">
                                <p class="text-sm font-bold text-foreground">{{ $tenant->shop_name }}</p>
                                <p class="text-xs text-muted-foreground">{{ $tenant->user->name }} ·
                                    {{ $tenant->user->email }}</p>
                                <div class="flex gap-3 mt-1 text-[10px] text-muted-foreground">
                                    <span>{{ $tenant->products_count }} products</span>
                                    <span>{{ $tenant->orders_count }} orders</span>
                                    <span>{{ $tenant->customers_count }} customers</span>
                                </div>
                            </a>
                            <div class="flex items-center gap-2 ml-3">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-lg text-[9px] font-bold uppercase {{ $tenant->is_active ? 'bg-emerald-500/10 text-emerald-600' : 'bg-red-500/10 text-red-600' }}">
                                    {{ $tenant->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                <form method="POST" action="{{ route('admin.tenants.toggle', $tenant) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        class="h-8 px-3 rounded-lg text-[10px] font-bold {{ $tenant->is_active ? 'bg-red-500/10 text-red-600 hover:bg-red-500/20' : 'bg-emerald-500/10 text-emerald-600 hover:bg-emerald-500/20' }} transition-colors">
                                        {{ $tenant->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-10 text-center text-sm text-muted-foreground">{{ __('No tenants found.') }}</div>
                @endforelse
            </div>
        </div>
        {{ $tenants->links() }}
    </div>
@endsection

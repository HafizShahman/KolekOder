@extends('layouts.shop')

@section('content')
    <div class="space-y-5 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-foreground">{{ __('Settings') }}</h1>
            <p class="mt-1 text-sm text-muted-foreground">{{ __('Manage your shop') }}</p>
        </div>

        {{-- Shop Details --}}
        <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-border bg-muted/30">
                <h3 class="text-sm font-bold text-foreground">{{ __('Shop Details') }}</h3>
            </div>
            <form method="POST" action="{{ route('shop.settings.update-shop') }}" enctype="multipart/form-data"
                class="p-4 space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-[10px] font-black uppercase tracking-widest text-muted-foreground mb-2">Shop
                            Name</label>
                        <input type="text" name="shop_name" value="{{ old('shop_name', $shop->shop_name) }}" required
                            class="w-full h-11 rounded-xl bg-background border border-border px-4 text-sm font-medium text-foreground focus:outline-none focus:ring-2 focus:ring-primary/50">
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-black uppercase tracking-widest text-muted-foreground mb-2">Shop
                            Initial (e.g. KODE) for Orders</label>
                        <input type="text" name="initial" value="{{ old('initial', $shop->initial) }}" maxlength="10"
                            placeholder="ORD"
                            class="w-full h-11 rounded-xl bg-background border border-border px-4 text-sm uppercase font-medium text-foreground focus:outline-none focus:ring-2 focus:ring-primary/50">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-muted-foreground mb-2">Shop
                        Address</label>
                    <textarea name="shop_address" rows="2"
                        class="w-full rounded-xl bg-background border border-border px-4 py-3 text-sm text-foreground resize-none focus:outline-none focus:ring-2 focus:ring-primary/50">{{ old('shop_address', $shop->shop_address) }}</textarea>
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-muted-foreground mb-2">Shop
                        Logo</label>
                    @if ($shop->shop_logo)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $shop->shop_logo) }}" alt="Logo"
                                class="h-16 w-16 rounded-xl object-cover">
                        </div>
                    @endif
                    <input type="file" name="shop_logo" accept="image/*"
                        class="text-sm text-muted-foreground file:mr-3 file:rounded-lg file:border-0 file:bg-primary file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-primary-foreground">
                </div>
                <button type="submit"
                    class="w-full h-10 bg-primary text-primary-foreground text-xs font-bold uppercase rounded-xl shadow-lg shadow-primary/20">Save
                    Shop Details</button>
            </form>
        </div>

        {{-- Color Settings --}}
        <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-border bg-muted/30">
                <h3 class="text-sm font-bold text-foreground">{{ __('Color Setting') }}</h3>
            </div>
            <form method="POST" action="{{ route('shop.settings.update-color') }}" class="p-4 space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-[10px] font-black uppercase tracking-widest text-muted-foreground mb-2">Primary
                            Color</label>
                        <input type="color" name="primary_color"
                            value="{{ $shop->color_setting['primary'] ?? '#6366f1' }}"
                            class="h-11 w-full rounded-xl border border-border cursor-pointer">
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-black uppercase tracking-widest text-muted-foreground mb-2">Secondary
                            Color</label>
                        <input type="color" name="secondary_color"
                            value="{{ $shop->color_setting['secondary'] ?? '#8b5cf6' }}"
                            class="h-11 w-full rounded-xl border border-border cursor-pointer">
                    </div>
                </div>
                <button type="submit"
                    class="w-full h-10 bg-primary text-primary-foreground text-xs font-bold uppercase rounded-xl shadow-lg shadow-primary/20">Save
                    Colors</button>
            </form>
        </div>

        {{-- Quick Links --}}
        <div class="space-y-2">
            <a href="{{ route('shop.settings.products') }}"
                class="flex items-center justify-between bg-card border border-border rounded-2xl p-4 shadow-sm active:bg-secondary/30 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-primary/10 flex items-center justify-center">
                        <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 8h1a4 4 0 110 8h-1M3 8h14v9a4 4 0 01-4 4H7a4 4 0 01-4-4V8zm0 0V6a2 2 0 012-2h10a2 2 0 012 2v2" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-foreground">{{ __('Manage Products') }}</p>
                        <p class="text-xs text-muted-foreground">Add/edit products, variants & add-ons</p>
                    </div>
                </div>
                <svg class="h-5 w-5 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
            <a href="{{ route('shop.settings.user') }}"
                class="flex items-center justify-between bg-card border border-border rounded-2xl p-4 shadow-sm active:bg-secondary/30 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-primary/10 flex items-center justify-center">
                        <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-foreground">{{ __('User Settings') }}</p>
                        <p class="text-xs text-muted-foreground">Update your profile</p>
                    </div>
                </div>
                <svg class="h-5 w-5 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
@endsection

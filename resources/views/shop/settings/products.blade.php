@extends('layouts.shop')

@section('content')
    <div class="space-y-5 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="flex items-center gap-3">
            <a href="{{ route('shop.settings.index') }}"
                class="h-10 w-10 rounded-xl bg-secondary flex items-center justify-center shrink-0">
                <svg class="h-5 w-5 text-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h1 class="text-xl font-extrabold tracking-tight text-foreground">{{ __('Manage Products') }}</h1>
        </div>

        {{-- Add Product Form --}}
        <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden" x-data="{ open: false, variants: [], addons: [] }">
            <button @click="open = !open"
                class="w-full px-4 py-3 flex items-center justify-between text-sm font-bold text-foreground bg-muted/30">
                <span>{{ __('Add New Product') }}</span>
                <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open" x-cloak class="p-4 border-t border-border">
                <form method="POST" action="{{ route('shop.settings.store-product') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label
                                class="block text-[10px] font-black uppercase tracking-widest text-muted-foreground mb-1">Product
                                Name</label>
                            <input type="text" name="name" required placeholder="e.g. Latte"
                                class="w-full h-10 rounded-xl bg-background border border-border px-3 text-sm text-foreground">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-black uppercase tracking-widest text-muted-foreground mb-1">Price
                                (RM)</label>
                            <input type="number" name="price" required step="0.01" min="0" placeholder="5.00"
                                class="w-full h-10 rounded-xl bg-background border border-border px-3 text-sm text-foreground">
                        </div>
                    </div>

                    {{-- Variants --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground">Variants
                                (Cold/Hot)</label>
                            <button type="button" @click="variants.push({name:'', price_modifier: 0})"
                                class="text-xs font-bold text-primary">+ Add</button>
                        </div>
                        <template x-for="(v, i) in variants" :key="i">
                            <div class="flex gap-2 mb-2">
                                <input type="text" :name="'variants[' + i + '][name]'" x-model="v.name"
                                    placeholder="e.g. Cold"
                                    class="flex-1 h-9 rounded-lg bg-background border border-border px-3 text-xs text-foreground">
                                <input type="number" :name="'variants[' + i + '][price_modifier]'" x-model="v.price_modifier"
                                    placeholder="+RM" step="0.01"
                                    class="w-24 h-9 rounded-lg bg-background border border-border px-3 text-xs text-foreground">
                                <button type="button" @click="variants.splice(i, 1)"
                                    class="h-9 w-9 rounded-lg bg-red-500/10 text-red-500 flex items-center justify-center text-xs">✕</button>
                            </div>
                        </template>
                    </div>

                    {{-- Addons --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-muted-foreground">Add-ons</label>
                            <button type="button" @click="addons.push({name:'', price: 0})"
                                class="text-xs font-bold text-primary">+ Add</button>
                        </div>
                        <template x-for="(a, i) in addons" :key="i">
                            <div class="flex gap-2 mb-2">
                                <input type="text" :name="'addons[' + i + '][name]'" x-model="a.name"
                                    placeholder="e.g. Extra Shot"
                                    class="flex-1 h-9 rounded-lg bg-background border border-border px-3 text-xs text-foreground">
                                <input type="number" :name="'addons[' + i + '][price]'" x-model="a.price" placeholder="RM"
                                    step="0.01"
                                    class="w-24 h-9 rounded-lg bg-background border border-border px-3 text-xs text-foreground">
                                <button type="button" @click="addons.splice(i, 1)"
                                    class="h-9 w-9 rounded-lg bg-red-500/10 text-red-500 flex items-center justify-center text-xs">✕</button>
                            </div>
                        </template>
                    </div>

                    <button type="submit"
                        class="w-full h-10 bg-primary text-primary-foreground text-xs font-bold uppercase rounded-xl shadow-lg shadow-primary/20">Add
                        Product</button>
                </form>
            </div>
        </div>

        {{-- Products List --}}
        <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden">
            <div class="divide-y divide-border/50">
                @forelse($products as $product)
                    <div class="px-4 py-3.5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-bold text-foreground">{{ $product->name }}</p>
                                <p class="text-xs text-muted-foreground">RM {{ number_format($product->price, 2) }}</p>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @foreach ($product->variants as $v)
                                        <span
                                            class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-500/10 text-blue-600">{{ $v->name }}
                                            {{ $v->price_modifier != 0 ? '(+' . number_format($v->price_modifier, 2) . ')' : '' }}</span>
                                    @endforeach
                                    @foreach ($product->addons as $a)
                                        <span
                                            class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-500/10 text-purple-600">{{ $a->name }}
                                            (+{{ number_format($a->price, 2) }})</span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('shop.settings.toggle-product', $product) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        class="h-8 w-8 rounded-lg flex items-center justify-center {{ $product->is_available ? 'bg-emerald-500/10 text-emerald-600' : 'bg-red-500/10 text-red-600' }}">
                                        @if ($product->is_available)
                                            ✓
                                        @else
                                            ✕
                                        @endif
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('shop.settings.destroy-product', $product) }}"
                                    onsubmit="return confirm('Delete this product?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="h-8 w-8 rounded-lg bg-red-500/10 text-red-500 flex items-center justify-center text-xs">🗑</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-10 text-center text-sm text-muted-foreground">
                        {{ __('No products yet. Add your first product above!') }}</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

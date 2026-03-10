@extends('layouts.shop')

@section('content')
    <div class="space-y-5 animate-in fade-in slide-in-from-bottom-4 duration-700" x-data="orderForm()">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-foreground">{{ __('New Order') }}</h1>
            <p class="mt-1 text-sm text-muted-foreground">{{ __('Select products and create order') }}</p>
        </div>

        <form method="POST" action="{{ route('shop.orders.store') }}" @submit="prepareSubmit"
            class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
            @csrf

            <div class="lg:col-span-8 space-y-5">
                {{-- Customer Selection --}}
                <div>
                    <label
                        class="block text-[10px] font-black uppercase tracking-widest text-muted-foreground mb-2">Customer</label>
                    <select name="customer_id"
                        class="w-full h-11 rounded-xl bg-card border border-border px-4 text-sm font-medium text-foreground">
                        <option value="">Walk-in Customer</option>
                        @foreach ($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} {{ $c->phone ? "($c->phone)" : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Product Selection --}}
                <div class="space-y-3">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-muted-foreground">Select
                        Products</label>

                    @foreach ($products as $product)
                        <div class="bg-card border border-border rounded-2xl p-4 shadow-sm" x-data="{ qty: 1, variant: '', variantPrice: 0, selectedAddons: [], addOnDetails: [], toggleAddonLocal(id, name, price) { const idx = this.selectedAddons.indexOf(id); if (idx > -1) { this.selectedAddons.splice(idx, 1);
                                    this.addOnDetails.splice(idx, 1); } else { this.selectedAddons.push(id);
                                    this.addOnDetails.push({ id, name, price }); } } }">
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <p class="text-sm font-bold text-foreground">{{ $product->name }}</p>
                                    <p class="text-xs text-muted-foreground">Base: RM
                                        {{ number_format($product->price, 2) }}</p>
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    <span class="text-sm font-black text-foreground">
                                        RM <span
                                            x-text="( ({{ $product->price }} + variantPrice + addOnDetails.reduce((t, a) => t + a.price, 0)) * qty ).toFixed(2)"></span>
                                    </span>
                                </div>
                            </div>

                            <div class="space-y-3 mt-3 pt-3 border-t border-border">
                                @if ($product->variants->count())
                                    <div>
                                        <p
                                            class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground mb-1">
                                            Variant</p>
                                        <div class="flex gap-2">
                                            @foreach ($product->variants as $v)
                                                <button type="button"
                                                    @click="variant = '{{ $v->name }}'; variantPrice = {{ $v->price_modifier }};"
                                                    :class="variant === '{{ $v->name }}' ?
                                                        'bg-primary text-primary-foreground shadow-sm' :
                                                        'bg-secondary text-foreground'"
                                                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all border border-transparent"
                                                    :class="variant === '{{ $v->name }}' ? 'border-primary/20' : ''">
                                                    {{ ucfirst($v->name) }}
                                                    @if ($v->price_modifier != 0)
                                                        <span
                                                            class="opacity-70">(+RM{{ number_format($v->price_modifier, 2) }})</span>
                                                    @endif
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if ($product->addons->count())
                                    <div>
                                        <p
                                            class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground mb-1">
                                            Add-ons</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($product->addons as $addon)
                                                <button type="button"
                                                    @click="toggleAddonLocal({{ $addon->id }}, '{{ addslashes($addon->name) }}', {{ $addon->price }})"
                                                    :class="selectedAddons.includes({{ $addon->id }}) ?
                                                        'bg-primary text-primary-foreground shadow-sm' :
                                                        'bg-secondary text-foreground'"
                                                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all border border-transparent"
                                                    :class="selectedAddons.includes({{ $addon->id }}) ?
                                                        'border-primary/20' : ''">
                                                    {{ $addon->name }}
                                                    @if ($addon->price > 0)
                                                        <span
                                                            class="opacity-70">(+RM{{ number_format($addon->price, 2) }})</span>
                                                    @endif
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Add to Order Action --}}
                                <div class="mt-4 pt-3 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="qty = Math.max(1, qty - 1)"
                                            class="h-8 w-8 rounded-lg bg-secondary flex items-center justify-center text-foreground active:scale-90 transition-transform"><svg
                                                class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                stroke-width="2">
                                                <path stroke-linecap="round" d="M20 12H4" />
                                            </svg></button>
                                        <span class="w-8 text-center text-sm font-black text-foreground"
                                            x-text="qty"></span>
                                        <button type="button" @click="qty++"
                                            class="h-8 w-8 rounded-lg bg-secondary flex items-center justify-center text-foreground active:scale-90 transition-transform"><svg
                                                class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                stroke-width="2">
                                                <path stroke-linecap="round" d="M12 4v16m8-8H4" />
                                            </svg></button>
                                    </div>
                                    <button type="button"
                                        @click="$dispatch('add-to-cart', { id: Date.now(), product_id: {{ $product->id }}, name: '{{ addslashes($product->name) }}', base_price: {{ $product->price }}, qty: qty, variant: variant, variant_price: variantPrice, addons: [...selectedAddons], addon_details: JSON.parse(JSON.stringify(addOnDetails)) }); qty = 1; variant = ''; variantPrice = 0; selectedAddons = []; addOnDetails = [];"
                                        class="h-9 px-4 bg-primary text-primary-foreground shadow-lg shadow-primary/20 font-bold text-xs uppercase tracking-widest rounded-xl hover:scale-105 active:scale-95 transition-all">
                                        Add to Order
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Cart Sidebar --}}
            <div class="lg:col-span-4" @add-to-cart.window="addToCart($event.detail)">
                <div
                    class="bg-card border border-border rounded-2xl shadow-sm sticky top-20 flex flex-col max-h-[calc(100vh-6rem)]">
                    <div class="p-4 border-b border-border bg-muted/30 shrink-0">
                        <h3 class="text-sm font-bold text-foreground">{{ __('Current Order') }}</h3>
                        <p class="text-xs text-muted-foreground mt-0.5"><span x-text="cart.length"></span> distinct items
                        </p>
                    </div>

                    <div class="p-4 overflow-y-auto flex-1 space-y-3 min-h-[250px]">
                        <template x-if="cart.length === 0">
                            <div class="h-full flex flex-col items-center justify-center text-muted-foreground/50 py-10">
                                <svg class="h-12 w-12 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                <p class="text-xs font-bold uppercase tracking-widest">{{ __('Cart is empty') }}</p>
                            </div>
                        </template>

                        <template x-for="(item, index) in cart" :key="item.id">
                            <div
                                class="flex gap-3 justify-between p-3 rounded-xl border border-border bg-background relative group">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-foreground truncate" x-text="item.name"></p>

                                    <div class="text-[10px] text-muted-foreground mt-1 space-y-0.5"
                                        x-show="item.variant || item.addon_details.length > 0">
                                        <template x-if="item.variant">
                                            <p><span class="font-bold">Var:</span> <span x-text="item.variant"></span>
                                                (+RM<span x-text="item.variant_price.toFixed(2)"></span>)</p>
                                        </template>
                                        <template x-if="item.addon_details.length > 0">
                                            <p class="truncate"><span class="font-bold">Add:</span> <span
                                                    x-text="item.addon_details.map(a => a.name).join(', ')"></span></p>
                                        </template>
                                    </div>

                                    <div class="flex items-center gap-2 mt-2">
                                        <button type="button"
                                            @click="item.qty = Math.max(1, item.qty - 1); calculateTotal()"
                                            class="h-6 w-6 rounded bg-secondary flex items-center justify-center text-foreground hover:bg-primary/20 transition-all">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" d="M20 12H4" />
                                            </svg>
                                        </button>
                                        <span class="text-xs font-black w-4 text-center" x-text="item.qty"></span>
                                        <button type="button" @click="item.qty++; calculateTotal()"
                                            class="h-6 w-6 rounded bg-secondary flex items-center justify-center text-foreground hover:bg-primary/20 transition-all">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="text-right shrink-0 flex flex-col justify-between">
                                    <span class="text-xs font-black text-foreground block">RM <span
                                            x-text="((item.base_price + item.variant_price + item.addon_details.reduce((a, b) => a + b.price, 0)) * item.qty).toFixed(2)"></span></span>
                                    <button type="button" @click="removeFromOrder(index)"
                                        class="h-6 w-6 rounded lg:opacity-0 group-hover:opacity-100 bg-destructive/10 text-destructive flex items-center justify-center self-end hover:bg-destructive hover:text-white transition-all">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="p-4 border-t border-border bg-muted/10 shrink-0">
                        <div class="mb-4">
                            <label
                                class="block text-[10px] font-black uppercase tracking-widest text-muted-foreground mb-2">Notes</label>
                            <textarea name="notes" rows="1" placeholder="Special instructions..."
                                class="w-full rounded-xl bg-background border border-border px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground/50 resize-none focus:outline-none focus:ring-2 focus:ring-primary/50"></textarea>
                        </div>

                        <div class="flex justify-between items-center mb-4">
                            <span class="text-xs font-bold uppercase tracking-widest text-muted-foreground">Total</span>
                            <span class="text-xl font-black text-primary">RM <span
                                    x-text="cartTotal.toFixed(2)"></span></span>
                        </div>

                        {{-- Hidden inputs to submit array --}}
                        <template x-for="(item, index) in cart" :key="item.id">
                            <div>
                                <input type="hidden" :name="`items[${index}][product_id]`" :value="item.product_id">
                                <input type="hidden" :name="`items[${index}][quantity]`" :value="item.qty">
                                <input type="hidden" :name="`items[${index}][variant]`" :value="item.variant">
                                <template x-for="addonId in item.addons" :key="addonId">
                                    <input type="hidden" :name="`items[${index}][addons][]`" :value="addonId">
                                </template>
                            </div>
                        </template>

                        {{-- Submit --}}
                        <button type="submit" x-bind:disabled="cart.length === 0"
                            class="w-full h-12 bg-primary text-primary-foreground text-xs font-black uppercase tracking-widest rounded-xl shadow-lg shadow-primary/25 hover:shadow-xl active:scale-[0.98] disabled:opacity-50 disabled:pointer-events-none transition-all duration-200">
                            {{ __('Charge Order') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            function orderForm() {
                return {
                    cart: [],
                    cartTotal: 0,

                    addToCart(item) {
                        // Check if exact same item exists (same product, variant, and addons)
                        const existingIdx = this.cart.findIndex(c =>
                            c.product_id === item.product_id &&
                            c.variant === item.variant &&
                            JSON.stringify(c.addons.sort()) === JSON.stringify(item.addons.sort())
                        );

                        if (existingIdx > -1) {
                            this.cart[existingIdx].qty += item.qty;
                        } else {
                            // Calculate unit price for display
                            item.unit_price = item.base_price + item.variant_price + item.addon_details.reduce((a, b) => a + b
                                .price, 0);
                            this.cart.push(item);
                        }
                        this.calculateTotal();
                    },

                    removeFromOrder(index) {
                        this.cart.splice(index, 1);
                        this.calculateTotal();
                    },

                    calculateTotal() {
                        this.cartTotal = this.cart.reduce((total, item) => total + (item.unit_price * item.qty), 0);
                    },

                    prepareSubmit(e) {
                        if (this.cart.length === 0) {
                            e.preventDefault();
                            alert('Please add at least one item to the cart.');
                        }
                    }
                }
            }
        </script>
    @endpush
@endsection

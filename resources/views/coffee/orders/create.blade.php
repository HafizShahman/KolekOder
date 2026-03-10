@extends('layouts.app')

@section('content')
    <div class="space-y-5 animate-in fade-in slide-in-from-bottom-4 duration-700" x-data="orderForm()">
        <div class="flex items-center gap-3">
            <a href="{{ route('coffee.orders.index') }}"
                class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-secondary text-foreground active:bg-secondary/80 transition-all shrink-0">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-foreground">{{ __('New Order') }}</h1>
                <p class="text-xs text-muted-foreground">{{ __('Tap items to add') }}</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500/20 p-3 rounded-xl">
                <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('coffee.orders.store') }}" method="POST" @submit="prepareSubmit">
            @csrf

            {{-- Menu Items --}}
            @foreach ($menuItems as $category => $items)
                <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden mb-4">
                    <div class="px-4 py-3 border-b border-border bg-muted/30">
                        <h3 class="text-xs font-bold text-foreground uppercase tracking-widest flex items-center gap-2">
                            @if ($category === 'Hot')
                                <span class="h-2 w-2 rounded-full bg-red-400"></span>
                            @elseif($category === 'Cold')
                                <span class="h-2 w-2 rounded-full bg-blue-400"></span>
                            @else
                                <span class="h-2 w-2 rounded-full bg-purple-400"></span>
                            @endif
                            {{ $category }}
                        </h3>
                    </div>
                    <div class="p-3 grid grid-cols-1 gap-2">
                        @foreach ($items as $item)
                            <div class="relative flex items-center justify-between p-3 rounded-xl border transition-all duration-200 cursor-pointer active:scale-[0.98]"
                                :class="getItemQty({{ $item->id }}) > 0 ? 'border-primary bg-primary/5 shadow-sm' :
                                    'border-border bg-card'"
                                @click="addItem({{ $item->id }},'{{ addslashes($item->name) }}',{{ $item->price }})">
                                <div>
                                    <p class="text-sm font-bold text-foreground">{{ $item->name }}</p>
                                    <p class="text-xs font-semibold text-primary">RM
                                        {{ number_format($item->price, 2) }}</p>
                                </div>
                                <div class="flex items-center gap-2" @click.stop>
                                    <template x-if="getItemQty({{ $item->id }})>0">
                                        <div class="flex items-center gap-1.5">
                                            <button type="button" @click="removeItem({{ $item->id }})"
                                                class="h-8 w-8 rounded-lg bg-secondary text-foreground flex items-center justify-center active:bg-destructive active:text-destructive-foreground transition-all text-sm font-bold">−</button>
                                            <span class="w-7 text-center text-sm font-black text-foreground"
                                                x-text="getItemQty({{ $item->id }})"></span>
                                            <button type="button"
                                                @click="addItem({{ $item->id }},'{{ addslashes($item->name) }}',{{ $item->price }})"
                                                class="h-8 w-8 rounded-lg bg-primary text-primary-foreground flex items-center justify-center active:bg-primary/80 transition-all text-sm font-bold">+</button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            {{-- Customer Select --}}
            <div class="bg-card border border-border rounded-2xl shadow-sm p-4 mb-4">
                <label class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground mb-1.5 block">
                    {{ __('Customer') }}
                </label>
                <select name="customer_id"
                    class="w-full h-10 px-4 rounded-xl bg-background border border-border text-sm focus:ring-2 focus:ring-primary/50 transition-all">
                    <option value="">Walk-in Customer</option>
                    @foreach ($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}
                            {{ $c->phone ? "({$c->phone})" : '' }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Notes --}}
            <div class="bg-card border border-border rounded-2xl shadow-sm p-4 mb-4">
                <label class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground mb-1.5 block">
                    {{ __('Notes') }}
                </label>
                <textarea name="notes" rows="2" placeholder="Special instructions..."
                    class="w-full px-4 py-2 rounded-xl bg-background border border-border text-sm placeholder:text-muted-foreground focus:ring-2 focus:ring-primary/50 resize-none"></textarea>
            </div>

            {{-- Order Summary — Sticky Bottom --}}
            <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden sticky bottom-20">
                <div class="px-4 py-3 border-b border-border bg-muted/30">
                    <h3 class="text-xs font-bold text-foreground uppercase tracking-widest">
                        {{ __('Order Summary') }}</h3>
                </div>
                <div class="p-4">
                    <template x-if="cart.length===0">
                        <div class="text-center py-4">
                            <p class="text-sm text-muted-foreground">{{ __('Tap items above to add') }}</p>
                        </div>
                    </template>
                    <template x-if="cart.length>0">
                        <div class="space-y-2">
                            <template x-for="item in cart" :key="item.id">
                                <div class="flex items-center justify-between py-1">
                                    <div>
                                        <p class="text-sm font-bold text-foreground" x-text="item.name"></p>
                                        <p class="text-[10px] text-muted-foreground">RM <span
                                                x-text="item.price.toFixed(2)"></span> × <span x-text="item.qty"></span></p>
                                    </div>
                                    <p class="text-sm font-bold text-foreground">RM <span
                                            x-text="(item.price*item.qty).toFixed(2)"></span></p>
                                </div>
                            </template>
                            <div class="h-px bg-border my-2"></div>
                            <div class="flex items-center justify-between">
                                <p class="text-xs font-bold text-muted-foreground uppercase tracking-widest">
                                    {{ __('Total Cups') }}</p>
                                <p class="text-base font-black text-foreground" x-text="totalCups"></p>
                            </div>
                            <div class="flex items-center justify-between">
                                <p class="text-xs font-bold text-muted-foreground uppercase tracking-widest">
                                    {{ __('Type') }}</p>
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold uppercase"
                                    :class="cart.length > 1 ?
                                        'bg-purple-500/10 text-purple-600 border border-purple-500/20' :
                                        'bg-sky-500/10 text-sky-600 border border-sky-500/20'"
                                    x-text="cart.length>1?'Bulk':'Single'"></span>
                            </div>
                            <div class="h-px bg-border my-2"></div>
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-black text-foreground uppercase">{{ __('Total') }}</p>
                                <p class="text-xl font-black text-primary">RM <span x-text="totalAmount.toFixed(2)"></span>
                                </p>
                            </div>
                        </div>
                    </template>
                    <div id="hidden-items"></div>
                    <button type="submit" :disabled="cart.length === 0"
                        class="mt-4 w-full h-12 rounded-xl bg-primary text-primary-foreground text-sm font-bold shadow-lg shadow-primary/20 active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed">{{ __('Place Order') }}</button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            function orderForm() {
                return {
                    cart: [],
                    addItem(id, name, price) {
                        const e = this.cart.find(i => i.id === id);
                        e ? e.qty++ : this.cart.push({
                            id,
                            name,
                            price,
                            qty: 1
                        })
                    },
                    removeItem(id) {
                        const e = this.cart.find(i => i.id === id);
                        e && (e.qty--, e.qty <= 0 && (this.cart = this.cart.filter(i => i.id !== id)))
                    },
                    getItemQty(id) {
                        const i = this.cart.find(x => x.id === id);
                        return i ? i.qty : 0
                    },
                    get totalCups() {
                        return this.cart.reduce((s, i) => s + i.qty, 0)
                    },
                    get totalAmount() {
                        return this.cart.reduce((s, i) => s + i.price * i.qty, 0)
                    },
                    prepareSubmit() {
                        const c = document.getElementById('hidden-items');
                        c.innerHTML = '';
                        this.cart.forEach((item, idx) => {
                            const a = document.createElement('input');
                            a.type = 'hidden';
                            a.name = `items[${idx}][menu_item_id]`;
                            a.value = item.id;
                            c.appendChild(a);
                            const b = document.createElement('input');
                            b.type = 'hidden';
                            b.name = `items[${idx}][quantity]`;
                            b.value = item.qty;
                            c.appendChild(b)
                        })
                    }
                }
            }
        </script>
    @endpush
@endsection

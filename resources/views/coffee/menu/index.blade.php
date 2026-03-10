@extends('layouts.app')
@section('content')
    <div class="space-y-5" x-data="{ showModal: false }">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-foreground">{{ __('Menu') }}</h1>
                <p class="mt-1 text-sm text-muted-foreground">{{ __('Manage your drinks') }}</p>
            </div>
            <button @click="showModal=true"
                class="inline-flex h-10 items-center justify-center rounded-xl bg-primary px-4 text-xs font-bold text-primary-foreground shadow-lg shadow-primary/20 active:scale-95 transition-all shrink-0">
                <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>{{ __('Add') }}
            </button>
        </div>

        @if (session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/20 p-3 rounded-xl">
                <p class="text-sm font-bold text-emerald-600">{{ session('success') }}</p>
            </div>
        @endif

        @foreach ($menuItems as $category => $items)
            <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-border bg-muted/30">
                    <h3 class="text-xs font-bold text-foreground uppercase tracking-widest flex items-center gap-2">
                        @if ($category === 'Hot')
                            <span class="h-2 w-2 rounded-full bg-red-400"></span>
                        @elseif($category === 'Cold')
                            <span class="h-2 w-2 rounded-full bg-blue-400"></span>
                        @else
                            <span class="h-2 w-2 rounded-full bg-purple-400"></span>
                        @endif
                        {{ $category }} <span class="text-muted-foreground font-normal">({{ $items->count() }})</span>
                    </h3>
                </div>
                <div class="divide-y divide-border/50">
                    @foreach ($items as $item)
                        <div class="px-4 py-3 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div
                                    class="h-9 w-9 rounded-xl {{ $item->is_available ? 'bg-primary/10' : 'bg-secondary' }} flex items-center justify-center shrink-0">
                                    <svg class="h-4 w-4 {{ $item->is_available ? 'text-primary' : 'text-muted-foreground' }}"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M17 8h1a4 4 0 110 8h-1M3 8h14v9a4 4 0 01-4 4H7a4 4 0 01-4-4V8zm0 0V6a2 2 0 012-2h10a2 2 0 012 2v2" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p
                                        class="text-sm font-bold text-foreground {{ !$item->is_available ? 'line-through opacity-50' : '' }} truncate">
                                        {{ $item->name }}</p>
                                    <p class="text-xs font-semibold text-primary">RM {{ number_format($item->price, 2) }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <form action="{{ route('coffee.menu.toggle', $item) }}" method="POST">@csrf
                                    @method('PATCH')
                                    <button
                                        class="h-8 px-2.5 rounded-lg text-[10px] font-bold uppercase transition-all {{ $item->is_available ? 'bg-emerald-500/10 text-emerald-600 active:bg-emerald-500/20' : 'bg-yellow-500/10 text-yellow-600 active:bg-yellow-500/20' }}">
                                        {{ $item->is_available ? 'On' : 'Off' }}
                                    </button>
                                </form>
                                <form action="{{ route('coffee.menu.destroy', $item) }}" method="POST"
                                    onsubmit="return confirm('Delete {{ $item->name }}?')">@csrf @method('DELETE')
                                    <button
                                        class="h-8 w-8 rounded-lg text-muted-foreground active:text-destructive active:bg-destructive/10 flex items-center justify-center transition-all">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        {{-- Add Menu Item Modal --}}
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
                <h3 class="text-xl font-bold text-foreground mb-5">Add Menu Item</h3>
                <form action="{{ route('coffee.menu.store') }}" method="POST" class="space-y-4">@csrf
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground mb-1 block">Name
                            *</label>
                        <input type="text" name="name" required
                            class="w-full h-11 px-4 rounded-xl bg-background border border-border text-sm focus:ring-2 focus:ring-primary/50"
                            placeholder="e.g. Kopi O">
                    </div>
                    <div>
                        <label
                            class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground mb-1 block">Category
                            *</label>
                        <select name="category" required
                            class="w-full h-11 px-4 rounded-xl bg-background border border-border text-sm focus:ring-2 focus:ring-primary/50">
                            <option value="Hot">Hot</option>
                            <option value="Cold">Cold</option>
                            <option value="Blended">Blended</option>
                        </select>
                    </div>
                    <div>
                        <label
                            class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground mb-1 block">Price
                            (RM) *</label>
                        <input type="number" name="price" step="0.01" min="0" required
                            class="w-full h-11 px-4 rounded-xl bg-background border border-border text-sm focus:ring-2 focus:ring-primary/50"
                            placeholder="0.00">
                    </div>
                    <button type="submit"
                        class="w-full h-12 rounded-xl bg-primary text-primary-foreground text-sm font-bold shadow-lg shadow-primary/20 active:scale-[0.98] transition-all">Add
                        Item</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.customer')

@section('content')
    <div class="space-y-5 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <h1 class="text-2xl font-extrabold tracking-tight text-foreground">{{ __('Favorite Orders') }}</h1>

        <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden">
            <div class="divide-y divide-border/50">
                @forelse($favorites as $fav)
                    <div class="px-4 py-3.5 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-foreground">{{ $fav->name }}</p>
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach ($fav->order_data as $item)
                                    <span
                                        class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-secondary text-foreground">{{ $item['name'] ?? '' }}
                                        ×{{ $item['quantity'] ?? 1 }}</span>
                                @endforeach
                            </div>
                        </div>
                        <form method="POST" action="{{ route('customer.favorites.destroy', $fav) }}"
                            onsubmit="return confirm('Remove this favorite?')">
                            @csrf @method('DELETE')
                            <button
                                class="h-8 w-8 rounded-lg bg-red-500/10 text-red-500 flex items-center justify-center text-xs">✕</button>
                        </form>
                    </div>
                @empty
                    <div class="px-4 py-10 text-center text-sm text-muted-foreground">{{ __('No favorites saved yet.') }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

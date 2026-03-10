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
            <h1 class="text-xl font-extrabold tracking-tight text-foreground">{{ __('User Settings') }}</h1>
        </div>

        <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden">
            <form method="POST" action="{{ route('shop.settings.update-user') }}" class="p-4 space-y-4">
                @csrf @method('PUT')
                <div>
                    <label
                        class="block text-[10px] font-black uppercase tracking-widest text-muted-foreground mb-2">Name</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                        class="w-full h-11 rounded-xl bg-background border border-border px-4 text-sm font-medium text-foreground focus:outline-none focus:ring-2 focus:ring-primary/50">
                    @error('name')
                        <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label
                        class="block text-[10px] font-black uppercase tracking-widest text-muted-foreground mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                        class="w-full h-11 rounded-xl bg-background border border-border px-4 text-sm font-medium text-foreground focus:outline-none focus:ring-2 focus:ring-primary/50">
                    @error('email')
                        <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                    class="w-full h-10 bg-primary text-primary-foreground text-xs font-bold uppercase rounded-xl shadow-lg shadow-primary/20">Save
                    Profile</button>
            </form>
        </div>
    </div>
@endsection

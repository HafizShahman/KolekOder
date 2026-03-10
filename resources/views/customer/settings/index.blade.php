@extends('layouts.customer')

@section('content')
    <div class="space-y-5 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <h1 class="text-2xl font-extrabold tracking-tight text-foreground">{{ __('User Settings') }}</h1>

        <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden">
            <form method="POST" action="{{ route('customer.settings.update') }}" class="p-4 space-y-4">
                @csrf @method('PUT')
                <div>
                    <label
                        class="block text-[10px] font-black uppercase tracking-widest text-muted-foreground mb-2">Name</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                        class="w-full h-11 rounded-xl bg-background border border-border px-4 text-sm font-medium text-foreground focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                    @error('name')
                        <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label
                        class="block text-[10px] font-black uppercase tracking-widest text-muted-foreground mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                        class="w-full h-11 rounded-xl bg-background border border-border px-4 text-sm font-medium text-foreground focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                    @error('email')
                        <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                    class="w-full h-10 bg-emerald-600 text-white text-xs font-bold uppercase rounded-xl shadow-lg shadow-emerald-600/20">Save
                    Profile</button>
            </form>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-[70vh] px-4">
    <div class="w-full max-w-sm">
        {{-- Logo & Welcome --}}
        <div class="text-center mb-8">
            <div class="inline-flex h-16 w-16 bg-primary rounded-2xl items-center justify-center shadow-xl shadow-primary/25 mb-5">
                <svg class="h-9 w-9 text-primary-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17 8h1a4 4 0 110 8h-1M3 8h14v9a4 4 0 01-4 4H7a4 4 0 01-4-4V8zm0 0V6a2 2 0 012-2h10a2 2 0 012 2v2" />
                </svg>
            </div>
            <h1 class="text-2xl font-black italic uppercase tracking-tight text-foreground">{{ config('app.name', 'KolekOder') }}</h1>
            <p class="text-sm text-muted-foreground mt-1.5">{{ __('Sign in to manage your orders') }}</p>
        </div>

        {{-- Login Card --}}
        <div class="glass rounded-2xl p-6 sm:p-8 shadow-xl">
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-[10px] font-black uppercase tracking-widest text-muted-foreground mb-2">{{ __('Email Address') }}</label>
                    <input id="email" type="email"
                        class="w-full h-12 px-4 rounded-xl bg-background border border-border text-foreground text-sm font-medium placeholder:text-muted-foreground/50 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all @error('email') border-destructive ring-2 ring-destructive/30 @enderror"
                        name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                        placeholder="you@example.com">

                    @error('email')
                        <p class="mt-1.5 text-xs font-semibold text-destructive">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-[10px] font-black uppercase tracking-widest text-muted-foreground">{{ __('Password') }}</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-[10px] font-bold text-primary hover:text-primary/80 transition-colors">
                                {{ __('Forgot?') }}
                            </a>
                        @endif
                    </div>
                    <input id="password" type="password"
                        class="w-full h-12 px-4 rounded-xl bg-background border border-border text-foreground text-sm font-medium placeholder:text-muted-foreground/50 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all @error('password') border-destructive ring-2 ring-destructive/30 @enderror"
                        name="password" required autocomplete="current-password"
                        placeholder="••••••••">

                    @error('password')
                        <p class="mt-1.5 text-xs font-semibold text-destructive">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center gap-2.5">
                    <input id="remember" type="checkbox" name="remember"
                        class="h-4 w-4 rounded-md border-border text-primary focus:ring-primary/50 transition-all cursor-pointer"
                        {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember" class="text-sm font-medium text-muted-foreground cursor-pointer select-none">
                        {{ __('Remember me') }}
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full h-12 bg-primary text-primary-foreground text-xs font-black uppercase tracking-widest rounded-xl shadow-lg shadow-primary/25 hover:shadow-xl hover:shadow-primary/30 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200">
                    {{ __('Sign In') }}
                </button>
            </form>
        </div>

        {{-- Register Link --}}
        @if (Route::has('register'))
            <p class="text-center text-sm text-muted-foreground mt-6">
                {{ __("Don't have an account?") }}
                <a href="{{ route('register') }}" class="font-bold text-primary hover:text-primary/80 transition-colors">
                    {{ __('Get Started') }}
                </a>
            </p>
        @endif
    </div>
</div>
@endsection

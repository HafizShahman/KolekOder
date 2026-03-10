@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-center min-h-[70vh] px-4">
        <div class="w-full max-w-sm">
            {{-- Logo & Welcome --}}
            <div class="text-center mb-8">
                <div
                    class="inline-flex h-16 w-16 bg-primary rounded-2xl items-center justify-center shadow-xl shadow-primary/25 mb-5">
                    <svg class="h-9 w-9 text-primary-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 8h1a4 4 0 110 8h-1M3 8h14v9a4 4 0 01-4 4H7a4 4 0 01-4-4V8zm0 0V6a2 2 0 012-2h10a2 2 0 012 2v2" />
                    </svg>
                </div>
                <h1 class="text-2xl font-black italic uppercase tracking-tight text-foreground">{{ config('app.name') }}
                </h1>
                <p class="text-sm text-muted-foreground mt-1.5">{{ __('Register your shop to get started') }}</p>
            </div>

            {{-- Register Card --}}
            <div class="glass rounded-2xl p-6 sm:p-8 shadow-xl">
                <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    {{-- Shop Details Section --}}
                    <div class="pb-4 border-b border-border">
                        <p class="text-[10px] font-black uppercase tracking-widest text-primary mb-3">
                            {{ __('Shop Details') }}</p>

                        <div class="space-y-3">
                            <div>
                                <label for="shop_name"
                                    class="block text-[10px] font-black uppercase tracking-widest text-muted-foreground mb-2">{{ __('Shop Name') }}</label>
                                <input id="shop_name" type="text"
                                    class="w-full h-12 px-4 rounded-xl bg-background border border-border text-foreground text-sm font-medium placeholder:text-muted-foreground/50 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all @error('shop_name') border-destructive ring-2 ring-destructive/30 @enderror"
                                    name="shop_name" value="{{ old('shop_name') }}" required placeholder="Your shop name">
                                @error('shop_name')
                                    <p class="mt-1.5 text-xs font-semibold text-destructive">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="shop_address"
                                    class="block text-[10px] font-black uppercase tracking-widest text-muted-foreground mb-2">{{ __('Shop Address') }}</label>
                                <textarea id="shop_address" name="shop_address" rows="2"
                                    class="w-full px-4 py-3 rounded-xl bg-background border border-border text-foreground text-sm font-medium placeholder:text-muted-foreground/50 focus:outline-none focus:ring-2 focus:ring-primary/50 resize-none"
                                    placeholder="Your shop location">{{ old('shop_address') }}</textarea>
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-black uppercase tracking-widest text-muted-foreground mb-2">{{ __('Shop Logo') }}</label>
                                <input type="file" name="shop_logo" accept="image/*"
                                    class="text-sm text-muted-foreground file:mr-3 file:rounded-lg file:border-0 file:bg-primary file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-primary-foreground">
                            </div>
                        </div>
                    </div>

                    {{-- User Details Section --}}
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-primary mb-3">
                            {{ __('User Details') }}</p>

                        <div class="space-y-3">
                            <div>
                                <label for="name"
                                    class="block text-[10px] font-black uppercase tracking-widest text-muted-foreground mb-2">{{ __('Name') }}</label>
                                <input id="name" type="text"
                                    class="w-full h-12 px-4 rounded-xl bg-background border border-border text-foreground text-sm font-medium placeholder:text-muted-foreground/50 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all @error('name') border-destructive ring-2 ring-destructive/30 @enderror"
                                    name="name" value="{{ old('name') }}" required autofocus
                                    placeholder="Your full name">
                                @error('name')
                                    <p class="mt-1.5 text-xs font-semibold text-destructive">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email"
                                    class="block text-[10px] font-black uppercase tracking-widest text-muted-foreground mb-2">{{ __('Email Address') }}</label>
                                <input id="email" type="email"
                                    class="w-full h-12 px-4 rounded-xl bg-background border border-border text-foreground text-sm font-medium placeholder:text-muted-foreground/50 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all @error('email') border-destructive ring-2 ring-destructive/30 @enderror"
                                    name="email" value="{{ old('email') }}" required placeholder="you@example.com">
                                @error('email')
                                    <p class="mt-1.5 text-xs font-semibold text-destructive">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password"
                                    class="block text-[10px] font-black uppercase tracking-widest text-muted-foreground mb-2">{{ __('Password') }}</label>
                                <input id="password" type="password"
                                    class="w-full h-12 px-4 rounded-xl bg-background border border-border text-foreground text-sm font-medium placeholder:text-muted-foreground/50 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all @error('password') border-destructive ring-2 ring-destructive/30 @enderror"
                                    name="password" required placeholder="••••••••">
                                @error('password')
                                    <p class="mt-1.5 text-xs font-semibold text-destructive">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password-confirm"
                                    class="block text-[10px] font-black uppercase tracking-widest text-muted-foreground mb-2">{{ __('Confirm Password') }}</label>
                                <input id="password-confirm" type="password"
                                    class="w-full h-12 px-4 rounded-xl bg-background border border-border text-foreground text-sm font-medium placeholder:text-muted-foreground/50 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all"
                                    name="password_confirmation" required placeholder="••••••••">
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full h-12 bg-primary text-primary-foreground text-xs font-black uppercase tracking-widest rounded-xl shadow-lg shadow-primary/25 hover:shadow-xl hover:shadow-primary/30 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200">
                        {{ __('Register Shop') }}
                    </button>
                </form>
            </div>

            <p class="text-center text-sm text-muted-foreground mt-6">
                {{ __('Already have an account?') }}
                <a href="{{ route('login') }}" class="font-bold text-primary hover:text-primary/80 transition-colors">
                    {{ __('Sign In') }}
                </a>
            </p>
        </div>
    </div>
@endsection

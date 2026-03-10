<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <title>{{ config('app.name', 'KolekOder') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-background text-foreground antialiased min-h-screen flex flex-col font-sans overflow-x-hidden"
    x-data="{ userMenuOpen: false }">
    @auth
        <div id="app" class="flex flex-col min-h-screen w-full">
            <!-- Top Header Bar -->
            <header class="sticky top-0 z-30 w-full glass border-b border-border/50 shrink-0">
                <div class="max-w-3xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between">
                    <a href="{{ route('coffee.dashboard') }}" class="flex items-center gap-2.5 group">
                        <div
                            class="h-8 w-8 bg-primary rounded-lg flex items-center justify-center shadow-md shadow-primary/20 group-hover:rotate-12 transition-transform duration-500 shrink-0">
                            <svg class="h-5 w-5 text-primary-foreground" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 8h1a4 4 0 110 8h-1M3 8h14v9a4 4 0 01-4 4H7a4 4 0 01-4-4V8zm0 0V6a2 2 0 012-2h10a2 2 0 012 2v2" />
                            </svg>
                        </div>
                        <span
                            class="text-lg font-black italic uppercase tracking-tighter text-foreground">{{ config('app.name', 'KolekOder') }}</span>
                    </a>

                    <!-- User Menu -->
                    <div class="relative" @click.away="userMenuOpen = false">
                        <button type="button" @click="userMenuOpen = !userMenuOpen"
                            class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-secondary/50 transition-all">
                            <div
                                class="h-8 w-8 bg-primary/10 rounded-lg flex items-center justify-center text-xs font-black text-primary">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <svg class="h-4 w-4 text-muted-foreground transition-transform"
                                :class="{ 'rotate-180': userMenuOpen }" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="userMenuOpen" x-cloak x-transition
                            class="absolute right-0 top-full mt-2 w-56 p-2 rounded-2xl shadow-2xl bg-card border border-border z-50 text-right">
                            <div class="px-3 py-2 mb-1">
                                <p class="text-xs font-bold text-foreground truncate">{{ Auth::user()->name }}</p>
                                <p class="text-[10px] text-muted-foreground truncate">{{ Auth::user()->email }}</p>
                            </div>
                            <div class="h-px bg-border"></div>
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                class="flex items-center justify-end gap-3 px-3 py-2.5 mt-1 rounded-xl text-[10px] font-black uppercase tracking-widest text-destructive hover:bg-destructive/10 transition-all">
                                {{ __('Logout') }}
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 w-full main-content-mobile">
                <div class="p-4 sm:p-6 w-full max-w-3xl mx-auto">
                    @yield('content')
                </div>
            </main>

            <!-- Bottom Tab Navigation -->
            <nav class="fixed bottom-0 left-0 right-0 z-40 glass border-t border-border/50 pb-safe">
                <div class="max-w-3xl mx-auto h-16" style="display: grid; grid-template-columns: repeat(4, 1fr);">
                    <a href="{{ route('coffee.dashboard') }}"
                        class="bottom-nav-item flex flex-col items-center justify-center gap-1 {{ request()->routeIs('coffee.dashboard') ? 'text-primary' : 'text-muted-foreground' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span class="text-[10px] font-bold uppercase tracking-wider">{{ __('Home') }}</span>
                        @if (request()->routeIs('coffee.dashboard'))
                            <span class="absolute top-1 h-1 w-6 rounded-full bg-primary"></span>
                        @endif
                    </a>
                    <a href="{{ route('coffee.orders.index') }}"
                        class="bottom-nav-item flex flex-col items-center justify-center gap-1 relative {{ request()->routeIs('coffee.orders.*') ? 'text-primary' : 'text-muted-foreground' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <span class="text-[10px] font-bold uppercase tracking-wider">{{ __('Orders') }}</span>
                        @if (request()->routeIs('coffee.orders.*'))
                            <span class="absolute top-1 h-1 w-6 rounded-full bg-primary"></span>
                        @endif
                    </a>
                    <a href="{{ route('coffee.customers.index') }}"
                        class="bottom-nav-item flex flex-col items-center justify-center gap-1 relative {{ request()->routeIs('coffee.customers.*') ? 'text-primary' : 'text-muted-foreground' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="text-[10px] font-bold uppercase tracking-wider">{{ __('Customers') }}</span>
                        @if (request()->routeIs('coffee.customers.*'))
                            <span class="absolute top-1 h-1 w-6 rounded-full bg-primary"></span>
                        @endif
                    </a>
                    <a href="{{ route('coffee.menu.index') }}"
                        class="bottom-nav-item flex flex-col items-center justify-center gap-1 relative {{ request()->routeIs('coffee.menu.*') ? 'text-primary' : 'text-muted-foreground' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 8h1a4 4 0 110 8h-1M3 8h14v9a4 4 0 01-4 4H7a4 4 0 01-4-4V8zm0 0V6a2 2 0 012-2h10a2 2 0 012 2v2" />
                        </svg>
                        <span class="text-[10px] font-bold uppercase tracking-wider">{{ __('Menu') }}</span>
                        @if (request()->routeIs('coffee.menu.*'))
                            <span class="absolute top-1 h-1 w-6 rounded-full bg-primary"></span>
                        @endif
                    </a>
                </div>
            </nav>
        </div>
    @endauth

    @guest
        <div id="app" class="flex-1 flex flex-col w-full min-h-screen relative">
            <header class="sticky top-0 z-50 w-full glass border-b border-border/50">
                <div class="max-w-3xl mx-auto px-4 sm:px-6">
                    <div class="flex justify-between h-16 items-center">
                        <a href="{{ url('/') }}"
                            class="flex items-center gap-2.5 group transition-transform hover:scale-105 active:scale-95 duration-300">
                            <div
                                class="h-8 w-8 bg-primary rounded-lg flex items-center justify-center shadow-lg shadow-primary/20 group-hover:rotate-12 transition-transform duration-500">
                                <svg class="h-5 w-5 text-primary-foreground" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17 8h1a4 4 0 110 8h-1M3 8h14v9a4 4 0 01-4 4H7a4 4 0 01-4-4V8zm0 0V6a2 2 0 012-2h10a2 2 0 012 2v2" />
                                </svg>
                            </div>
                            <span
                                class="text-lg font-black italic uppercase tracking-tighter text-foreground">KolekOder</span>
                        </a>
                        <div class="flex items-center gap-4">
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}"
                                    class="text-[10px] font-black uppercase tracking-widest text-muted-foreground hover:text-primary transition-all">{{ __('Login') }}</a>
                            @endif
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="inline-flex h-10 items-center justify-center rounded-xl bg-primary px-5 text-[10px] font-black uppercase tracking-widest text-primary-foreground shadow-lg shadow-primary/20 hover:scale-105 active:scale-95 transition-all">{{ __('Get Started') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </header>

            <main class="grow pb-16 pt-8 max-w-3xl mx-auto px-4 sm:px-6 w-full">
                @yield('content')
            </main>

            <footer class="border-t border-border pt-6 pb-4 px-4 sm:px-6 mt-auto w-full">
                <div class="max-w-3xl mx-auto flex flex-col items-center gap-3">
                    <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-muted-foreground/60">
                        &copy; {{ date('Y') }} KolekOder &mdash; {{ __('Street Coffee Order Tracker') }}
                    </span>
                </div>
            </footer>
        </div>
    @endguest

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('scripts')
</body>

</html>

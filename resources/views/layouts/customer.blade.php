<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <title>{{ config('app.name') }} — Customer</title>

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
    <div id="app" class="flex flex-col min-h-screen w-full">
        <header class="sticky top-0 z-30 w-full glass border-b border-border/50 shrink-0">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between">
                <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-2.5 group">
                    <div
                        class="h-8 w-8 bg-emerald-600 rounded-lg flex items-center justify-center shadow-md shadow-emerald-600/20 group-hover:rotate-12 transition-transform duration-500 shrink-0">
                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <span class="text-lg font-black italic uppercase tracking-tighter text-foreground">KolekOder</span>
                </a>

                <div class="relative" @click.away="userMenuOpen = false">
                    <button type="button" @click="userMenuOpen = !userMenuOpen"
                        class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-secondary/50 transition-all">
                        <div
                            class="h-8 w-8 bg-emerald-600/10 rounded-lg flex items-center justify-center text-xs font-black text-emerald-600">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
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
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 w-full main-content-mobile">
            <div class="p-4 sm:p-6 w-full max-w-3xl mx-auto">
                @if (session('success'))
                    <div
                        class="mb-4 p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-sm font-semibold text-emerald-600">
                        {{ session('success') }}
                    </div>
                @endif
                @yield('content')
            </div>
        </main>

        <nav class="fixed bottom-0 left-0 right-0 z-40 glass border-t border-border/50 pb-safe">
            <div class="max-w-3xl mx-auto h-16" style="display: grid; grid-template-columns: repeat(4, 1fr);">
                <a href="{{ route('customer.dashboard') }}"
                    class="bottom-nav-item flex flex-col items-center justify-center gap-1 relative {{ request()->routeIs('customer.dashboard') ? 'text-emerald-600' : 'text-muted-foreground' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="text-[10px] font-bold uppercase tracking-wider">Home</span>
                </a>
                <a href="{{ route('customer.orders.index') }}"
                    class="bottom-nav-item flex flex-col items-center justify-center gap-1 relative {{ request()->routeIs('customer.orders.*') ? 'text-emerald-600' : 'text-muted-foreground' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span class="text-[10px] font-bold uppercase tracking-wider">Orders</span>
                </a>
                <a href="{{ route('customer.favorites.index') }}"
                    class="bottom-nav-item flex flex-col items-center justify-center gap-1 relative {{ request()->routeIs('customer.favorites.*') ? 'text-emerald-600' : 'text-muted-foreground' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <span class="text-[10px] font-bold uppercase tracking-wider">Favorites</span>
                </a>
                <a href="{{ route('customer.settings.index') }}"
                    class="bottom-nav-item flex flex-col items-center justify-center gap-1 relative {{ request()->routeIs('customer.settings.*') ? 'text-emerald-600' : 'text-muted-foreground' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="text-[10px] font-bold uppercase tracking-wider">Settings</span>
                </a>
            </div>
        </nav>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('scripts')
</body>

</html>

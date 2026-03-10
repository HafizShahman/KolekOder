<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'KolekOder') }} - Smart Order System</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Vite Styles/Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Outfit', 'sans-serif'],
                        },
                    }
                }
            }
        </script>
    @endif
    <style>
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        }
        .glass-dark {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        }
        .text-gradient {
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .blob-1 {
            position: absolute;
            top: -10%;
            left: -10%;
            width: 50vw;
            height: 50vw;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(99,102,241,0.3) 0%, rgba(255,255,255,0) 70%);
            z-index: -1;
            animation: float 10s ease-in-out infinite;
        }
        .blob-2 {
            position: absolute;
            bottom: -10%;
            right: -10%;
            width: 60vw;
            height: 60vw;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(236,72,153,0.2) 0%, rgba(255,255,255,0) 70%);
            z-index: -1;
            animation: float 14s ease-in-out infinite reverse;
        }
        @keyframes float {
            0% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-20px) scale(1.05); }
            100% { transform: translateY(0) scale(1); }
        }
    </style>
</head>
<body class="font-sans antialiased text-slate-800 bg-slate-50 relative overflow-x-hidden min-h-screen flex flex-col selection:bg-indigo-500 selection:text-white">
    <!-- Background Blobs -->
    <div class="blob-1"></div>
    <div class="blob-2"></div>

    <!-- Navigation -->
    <nav class="fixed w-full z-50 transition-all duration-300 glass" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex-shrink-0 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-indigo-500/30">
                        K
                    </div>
                    <span class="font-bold text-2xl tracking-tight bg-gradient-to-r from-slate-900 to-indigo-900 text-gradient">KolekOder</span>
                </div>
                <div class="hidden md:flex space-x-8">
                    <a href="#features" class="text-slate-600 hover:text-indigo-600 font-medium transition-colors">Features</a>
                    <a href="#how-it-works" class="text-slate-600 hover:text-indigo-600 font-medium transition-colors">How it Works</a>
                </div>
                <div class="flex items-center space-x-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/home') }}" class="px-6 py-2.5 rounded-full font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 shadow-md shadow-indigo-500/30 hover:shadow-lg hover:shadow-indigo-500/40 transition-all transform hover:-translate-y-0.5">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="hidden sm:inline-block px-5 py-2.5 rounded-full font-medium text-slate-700 hover:text-indigo-600 transition-colors">
                                Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-6 py-2.5 rounded-full font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 shadow-md shadow-indigo-500/30 hover:shadow-lg hover:shadow-indigo-500/40 transition-all transform hover:-translate-y-0.5">
                                    Register
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="flex-grow flex items-center pt-32 pb-20 lg:pt-48 lg:pb-32 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass mb-8 text-sm font-medium text-indigo-700 border border-indigo-200/50">
                <span class="flex h-2 w-2 relative">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                </span>
                The Modern Order Management System
            </div>
            
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold tracking-tight mb-8">
                Seamless Orders.<br/>
                <span class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 text-gradient">Effortless Collections.</span>
            </h1>
            
            <p class="mt-4 max-w-2xl text-xl text-slate-600 mx-auto mb-10 leading-relaxed">
                Connect your shop with customers instantly. Track orders, manage favorites, and streamline your entire collection process in one beautiful platform.
            </p>
            
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                @auth
                    <a href="{{ url('/home') }}" class="px-8 py-4 rounded-full font-semibold text-white text-lg bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 shadow-xl shadow-indigo-500/30 hover:shadow-indigo-500/40 hover:-translate-y-1 transition-all duration-300">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}" class="px-8 py-4 rounded-full font-semibold text-white text-lg bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 shadow-xl shadow-indigo-500/30 hover:shadow-indigo-500/40 hover:-translate-y-1 transition-all duration-300">
                        Get Started Free
                    </a>
                    <a href="#features" class="px-8 py-4 rounded-full font-semibold text-slate-700 text-lg glass hover:bg-white/90 transition-all duration-300 flex items-center justify-center gap-2">
                        Learn More
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M14.707 12.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @endauth
            </div>
        </div>
    </main>

    <!-- Features Section -->
    <section id="features" class="py-24 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Features designed for everyone</h2>
                <p class="text-lg text-slate-600 max-w-2xl mx-auto">Powerful tools custom-built for both shop owners and customers, wrapped in a beautiful, easy-to-use interface.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 mt-12 cursor-default">
                
                <!-- For Shops -->
                <div class="glass p-8 rounded-3xl transition-transform duration-300 hover:-translate-y-2 group relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-bl-full opacity-50 transition-transform duration-500 group-hover:scale-110"></div>
                    <div class="relative z-10">
                        <div class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-500 shadow-inner">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .414.336.75.75.75z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-3 text-slate-800">For Shop Owners</h3>
                        <p class="text-slate-600 mb-6 leading-relaxed">Streamline your business operations with powerful tools tailored for modern shops.</p>
                        <ul class="space-y-4">
                            <li class="flex items-start gap-4 text-slate-700">
                                <div class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <span class="font-medium text-slate-700">Manage incoming orders in real-time instantly.</span>
                            </li>
                            <li class="flex items-start gap-4 text-slate-700">
                                <div class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <span class="font-medium text-slate-700">Custom Order ID prefixes to suit your branding.</span>
                            </li>
                            <li class="flex items-start gap-4 text-slate-700">
                                <div class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <span class="font-medium text-slate-700">Customize shop colors and profiles simply.</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- For Customers -->
                <div class="glass p-8 rounded-3xl transition-transform duration-300 hover:-translate-y-2 group relative overflow-hidden border-2 border-transparent hover:border-purple-200">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-purple-50 rounded-bl-full opacity-50 transition-transform duration-500 group-hover:scale-110"></div>
                    <div class="relative z-10">
                        <div class="w-14 h-14 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-500 shadow-inner">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-3 text-slate-800">For Customers</h3>
                        <p class="text-slate-600 mb-6 leading-relaxed">Enjoy a seamless ordering and collection experience from your favorite local shops.</p>
                        <ul class="space-y-4">
                            <li class="flex items-start gap-4 text-slate-700">
                                <div class="w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <span class="font-medium text-slate-700">Track your active orders visually and easily.</span>
                            </li>
                            <li class="flex items-start gap-4 text-slate-700">
                                <div class="w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <span class="font-medium text-slate-700">Save favorite shops & prior orders with one click.</span>
                            </li>
                            <li class="flex items-start gap-4 text-slate-700">
                                <div class="w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <span class="font-medium text-slate-700">Access a clean, beautiful, mobile-friendly interface.</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-24 relative z-10">
        <div class="absolute inset-0 bg-slate-900/5 backdrop-blur-3xl border-y border-slate-200/50 -z-10"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">How It Works</h2>
                <p class="text-lg text-slate-600">Three simple steps to a superior ordering experience.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
                <!-- Connecting Line for Desktop -->
                <div class="hidden md:block absolute top-12 left-[15%] right-[15%] h-1 bg-gradient-to-r from-indigo-200 via-purple-300 to-pink-200 z-0 rounded-full opacity-50"></div>
                
                <!-- Step 1 -->
                <div class="relative z-10 flex flex-col items-center text-center group">
                    <div class="w-24 h-24 rounded-full glass mb-6 flex items-center justify-center shadow-lg border-2 border-indigo-100 bg-white/80 group-hover:scale-110 transition-transform duration-300">
                        <span class="text-3xl font-bold bg-gradient-to-br from-indigo-600 to-purple-600 text-gradient">1</span>
                    </div>
                    <h4 class="text-xl font-bold mb-2">Create Order</h4>
                    <p class="text-slate-600 px-4">Shop owner creates a new order, or a customer selects items from the menu.</p>
                </div>

                <!-- Step 2 -->
                <div class="relative z-10 flex flex-col items-center text-center mt-12 md:mt-0 group">
                    <div class="w-24 h-24 rounded-full glass mb-6 flex items-center justify-center shadow-lg border-2 border-purple-100 bg-white/80 group-hover:scale-110 transition-transform duration-300">
                        <span class="text-3xl font-bold bg-gradient-to-br from-purple-600 to-pink-600 text-gradient">2</span>
                    </div>
                    <h4 class="text-xl font-bold mb-2">Track Status</h4>
                    <p class="text-slate-600 px-4">Customer receives a beautiful tracking dashboard to monitor preparation.</p>
                </div>

                <!-- Step 3 -->
                <div class="relative z-10 flex flex-col items-center text-center mt-12 md:mt-0 group">
                    <div class="w-24 h-24 rounded-full glass mb-6 flex items-center justify-center shadow-lg border-2 border-pink-100 bg-white/80 group-hover:scale-110 transition-transform duration-300">
                        <span class="text-3xl font-bold bg-gradient-to-br from-pink-600 to-rose-500 text-gradient">3</span>
                    </div>
                    <h4 class="text-xl font-bold mb-2">Ready to Collect</h4>
                    <p class="text-slate-600 px-4">Get notified instantly when the order is ready for pickup. Zero waiting time.</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- CTA Banner -->
    <section class="py-20 relative z-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="glass p-10 md:p-14 rounded-3xl text-center bg-gradient-to-br from-indigo-50/80 to-purple-50/80 border border-indigo-100 shadow-xl overflow-hidden relative">
                <div class="absolute top-0 right-0 w-64 h-64 bg-pink-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob"></div>
                <div class="absolute top-0 -left-4 w-72 h-72 bg-purple-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-2000"></div>
                
                <div class="relative z-10">
                    <h2 class="text-3xl md:text-5xl font-extrabold mb-6 tracking-tight text-slate-800">Ready to transform your service?</h2>
                    <p class="text-lg text-slate-600 mb-8 max-w-2xl mx-auto">Join KolekOder today and experience the easiest way to manage your orders and collections.</p>
                    <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-4 rounded-full font-bold text-white text-lg bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 shadow-xl shadow-indigo-500/30 hover:shadow-indigo-500/40 hover:-translate-y-1 transition-all duration-300 gap-2">
                        Get Started Now
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="mt-auto pt-16 pb-8 border-t border-slate-200/60 glass z-10 relative bg-white/40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6 mb-8">
                <div class="flex flex-col items-center md:items-start gap-2">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                            K
                        </div>
                        <span class="font-bold text-xl text-slate-800 tracking-tight">KolekOder</span>
                    </div>
                    <p class="text-slate-500 text-sm mt-2 text-center md:text-left max-w-sm">
                        Simplifying the order and collection process for modern shops and their customers.
                    </p>
                </div>
                <div class="flex flex-wrap justify-center gap-6 text-sm font-medium text-slate-600">
                    <a href="#" class="hover:text-indigo-600 transition-colors">Features</a>
                    <a href="#" class="hover:text-indigo-600 transition-colors">How it works</a>
                    <a href="#" class="hover:text-indigo-600 transition-colors">Privacy</a>
                    <a href="#" class="hover:text-indigo-600 transition-colors">Terms</a>
                    <a href="#" class="hover:text-indigo-600 transition-colors">Contact</a>
                </div>
            </div>
            
            <div class="pt-8 border-t border-slate-200/60 text-center flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-slate-500 text-sm">
                    &copy; {{ date('Y') }} KolekOder System. All rights reserved.
                </p>
                <div class="flex gap-4">
                    <!-- Social icons placeholders -->
                    <a href="#" class="text-slate-400 hover:text-indigo-600 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg>
                    </a>
                    <a href="#" class="text-slate-400 hover:text-indigo-600 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" /></svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Script to add shadow on scroll -->
    <script>
        window.addEventListener('scroll', () => {
            const nav = document.getElementById('navbar');
            if (window.scrollY > 10) {
                nav.classList.add('shadow-md', 'bg-white/90');
                nav.classList.remove('glass', 'shadow-lg');
            } else {
                nav.classList.remove('shadow-md', 'bg-white/90');
                nav.classList.add('glass');
            }
        });
    </script>
</body>
</html>

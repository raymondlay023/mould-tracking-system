<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Mould Tracking System') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700|space-mono:400,700" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                /*! tailwindcss v4.0.7 | MIT License | https://tailwindcss.com */@layer theme{:root,:host{--font-sans:'Outfit',ui-sans-serif,system-ui,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";--font-mono:'Space Mono',ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace}}@layer base{*,:after,:before,::backdrop{box-sizing:border-box;border:0 solid;margin:0;padding:0}html,:host{-webkit-text-size-adjust:100%;-moz-tab-size:4;tab-size:4;line-height:1.5;font-family:var(--default-font-family,ui-sans-serif,system-ui,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji");-webkit-tap-highlight-color:transparent}body{line-height:inherit;margin:0}}*,:after,:before{box-sizing:border-box}:where([hidden]:not([hidden=until-found])){display:none!important}}@layer utilities{.text-balance{text-wrap:balance}}
            </style>
        @endif
        
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap');
            
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: 'Outfit', sans-serif;
                background: linear-gradient(135deg, #0F172A 0%, #1E293B 50%, #0F172A 100%);
                min-height: 100vh;
                overflow-x: hidden;
            }
            
            /* Animated background */
            .bg-grid {
                background-image: 
                    linear-gradient(rgba(20, 184, 166, 0.03) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(20, 184, 166, 0.03) 1px, transparent 1px);
                background-size: 60px 60px;
                animation: gridMove 20s linear infinite;
            }
            
            @keyframes gridMove {
                0% { transform: translate(0, 0); }
                100% { transform: translate(60px, 60px); }
            }
            
            /* Floating animation */
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-20px); }
            }
            
            @keyframes float-delay {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-15px); }
            }
            
            .animate-float {
                animation: float 6s ease-in-out infinite;
            }
            
            .animate-float-delay {
                animation: float-delay 7s ease-in-out infinite;
                animation-delay: 1s;
            }
            
            /* Glow effect */
            .glow-teal {
                box-shadow: 0 0 60px rgba(20, 184, 166, 0.3);
            }
            
            .glow-gold {
                box-shadow: 0 0 40px rgba(245, 158, 11, 0.2);
            }
            
            /* Glass morphism */
            .glass {
                background: rgba(255, 255, 255, 0.05);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
            
            .glass-dark {
                background: rgba(15, 23, 42, 0.8);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(20, 184, 166, 0.2);
            }
            
            /* Gradient text */
            .text-gradient {
                background: linear-gradient(135deg, #14B8A6 0%, #F59E0B 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
            
            /* Button styles */
            .btn-primary {
                background: linear-gradient(135deg, #14B8A6 0%, #0D9488 100%);
                transition: all 0.3s ease;
            }
            
            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 40px rgba(20, 184, 166, 0.4);
            }
            
            .btn-secondary {
                background: transparent;
                border: 1px solid rgba(255, 255, 255, 0.2);
                transition: all 0.3s ease;
            }
            
            .btn-secondary:hover {
                background: rgba(255, 255, 255, 0.1);
                border-color: rgba(20, 184, 166, 0.5);
            }
            
            /* Card hover */
            .feature-card {
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .feature-card:hover {
                transform: translateY(-8px);
                border-color: rgba(20, 184, 166, 0.4);
                box-shadow: 0 20px 60px rgba(20, 184, 166, 0.15);
            }
            
            /* Pulse ring */
            @keyframes pulse-ring {
                0% { transform: scale(0.8); opacity: 1; }
                100% { transform: scale(1.4); opacity: 0; }
            }
            
            .pulse-ring {
                animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            }
            
            /* Fade in up */
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .animate-fade-in {
                animation: fadeInUp 0.8s ease-out forwards;
            }
            
            .delay-100 { animation-delay: 0.1s; }
            .delay-200 { animation-delay: 0.2s; }
            .delay-300 { animation-delay: 0.3s; }
            .delay-400 { animation-delay: 0.4s; }
            .delay-500 { animation-delay: 0.5s; }
        </style>
    </head>
    <body class="bg-[#0F172A] min-h-screen">
        <!-- Background grid -->
        <div class="fixed inset-0 bg-grid pointer-events-none"></div>
        
        <!-- Gradient orbs -->
        <div class="fixed top-20 left-20 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl animate-float"></div>
        <div class="fixed bottom-20 right-20 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl animate-float-delay"></div>
        
        <!-- Navigation -->
        <nav class="relative z-50 flex items-center justify-between px-8 py-6 lg:px-16">
            <div class="flex items-center gap-3">
                <!-- Logo Icon -->
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-500 to-teal-600 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                </div>
                <span class="text-xl font-semibold text-white">{{ config('app.name', 'Mould Tracking') }}</span>
            </div>
            
            @if (Route::has('login'))
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 text-white/80 hover:text-white transition-colors text-sm font-medium">
                            Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 text-white/80 hover:text-white transition-colors text-sm font-medium">
                                Log Out
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="px-5 py-2.5 text-white/70 hover:text-white transition-colors text-sm font-medium">
                            Log in
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-6 py-2.5 bg-gradient-to-r from-teal-500 to-teal-600 text-white rounded-lg text-sm font-semibold hover:shadow-lg hover:shadow-teal-500/30 transition-all transform hover:-translate-y-0.5">
                                Get Started
                            </a>
                        @endif
                    @endauth
                </div>
            @endif
        </nav>
        
        <!-- Main Content -->
        <main class="relative z-10 flex flex-col lg:flex-row items-center min-h-[calc(100vh-80px)] px-8 lg:px-16 pb-16">
            
            <!-- Left Side - Visual -->
            <div class="w-full lg:w-3/5 flex items-center justify-center py-12 lg:py-0">
                <div class="relative w-full max-w-xl aspect-square">
                    <!-- Main mould icon -->
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="relative w-80 h-80">
                            <!-- Outer glow ring -->
                            <div class="absolute inset-0 rounded-full bg-teal-500/20 pulse-ring"></div>
                            
                            <!-- Main circle -->
                            <div class="absolute inset-8 rounded-full glass glow-teal flex items-center justify-center">
                                <svg class="w-32 h-32 text-teal-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 5.653c0-1.426 1.156-2.583 2.583-2.583h9.624c1.427 0 2.583 1.157 2.583 2.583v10.694c0 1.427-1.156 2.583-2.583 2.583H7.183c-1.427 0-2.583-1.156-2.583-2.583V5.653z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6M12 9v6"/>
                                </svg>
                            </div>
                            
                            <!-- Floating elements -->
                            <div class="absolute -top-4 -right-4 w-16 h-16 glass rounded-2xl flex items-center justify-center animate-float">
                                <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            
                            <div class="absolute -bottom-2 -left-6 w-14 h-14 glass rounded-xl flex items-center justify-center animate-float-delay">
                                <svg class="w-7 h-7 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            
                            <div class="absolute top-1/3 -right-8 w-12 h-12 glass rounded-full flex items-center justify-center animate-float">
                                <div class="w-3 h-3 bg-teal-400 rounded-full"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Side - Content -->
            <div class="w-full lg:w-2/5 lg:pl-12">
                <div class="max-w-lg">
                    <!-- Badge -->
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 glass rounded-full mb-6 animate-fade-in">
                        <span class="w-2 h-2 bg-teal-400 rounded-full animate-pulse"></span>
                        <span class="text-sm text-teal-300 font-medium">Manufacturing Excellence</span>
                    </div>
                    
                    <!-- Main Heading -->
                    <h1 class="text-4xl lg:text-5xl xl:text-6xl font-bold text-white mb-6 animate-fade-in delay-100">
                        Precision <span class="text-gradient">Mould</span> Tracking System
                    </h1>
                    
                    <!-- Subtitle -->
                    <p class="text-lg text-white/60 mb-8 animate-fade-in delay-200">
                        Streamline your manufacturing process with real-time mould tracking, maintenance scheduling, and production analytics.
                    </p>
                    
                    <!-- Feature Cards -->
                    <div class="grid gap-4 mb-10">
                        <div class="feature-card glass rounded-2xl p-5 animate-fade-in delay-300">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-xl bg-teal-500/20 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-white font-semibold mb-1">Smart Inventory</h3>
                                    <p class="text-sm text-white/50">Track moulds across locations with QR scanning</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="feature-card glass rounded-2xl p-5 animate-fade-in delay-400">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-xl bg-amber-500/20 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.573-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-white font-semibold mb-1">PM Scheduling</h3>
                                    <p class="text-sm text-white/50">Automated preventive maintenance reminders</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="feature-card glass rounded-2xl p-5 animate-fade-in delay-500">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-xl bg-teal-500/20 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-white font-semibold mb-1">Production Analytics</h3>
                                    <p class="text-sm text-white/50">Real-time OEE metrics and defect tracking</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-primary px-8 py-3.5 text-white rounded-xl font-semibold text-center">
                                Start Free Trial
                            </a>
                        @endif
                        <a href="{{ route('login') }}" class="btn-secondary px-8 py-3.5 text-white rounded-xl font-semibold text-center">
                            Sign In
                        </a>
                    </div>
                </div>
            </div>
        </main>
        
        <!-- Footer -->
        <footer class="relative z-10 px-8 lg:px-16 py-6">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-4">
                <p class="text-sm text-white/40">
                    © {{ date('Y') }} {{ config('app.name', 'Mould Tracking System') }}. All rights reserved.
                </p>
                <div class="flex items-center gap-6">
                    <a href="#" class="text-sm text-white/40 hover:text-teal-400 transition-colors">Privacy</a>
                    <a href="#" class="text-sm text-white/40 hover:text-teal-400 transition-colors">Terms</a>
                    <a href="#" class="text-sm text-white/40 hover:text-teal-400 transition-colors">Contact</a>
                </div>
            </div>
        </footer>
    </body>
</html>

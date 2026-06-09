<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-800 bg-slate-50">
        <div class="min-h-screen flex flex-col lg:flex-row bg-gradient-to-br from-slate-50 to-slate-100" x-data="{ sidebarOpen: false }">
            
            <!-- Sidebar Navigation -->
            @include('layouts.navigation')

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0 min-h-screen">
                
                <!-- Top Header Bar -->
                <header class="bg-white/80 backdrop-blur-md border-b border-slate-150 flex items-center justify-between px-6 py-4 sticky top-0 z-40 h-16 flex-shrink-0">
                    <div class="flex items-center gap-4">
                        <!-- Hamburger Menu Trigger (Mobile only) -->
                        <button @click="sidebarOpen = true" class="lg:hidden p-1.5 rounded-lg text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition focus:outline-none focus:ring-2 focus:ring-slate-500">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        
                        <!-- Dynamic Page Header -->
                        @isset($header)
                            <div class="flex items-center">
                                {{ $header }}
                            </div>
                        @endisset
                    </div>

                    <!-- Top Bar Actions (Notifications & User Menu) -->
                    <div class="flex items-center gap-3">
                        <livewire:partials.notification-bell />

                        <!-- Settings Dropdown -->
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center gap-2 px-3 py-1.5 border border-slate-100 text-sm leading-4 font-medium rounded-full text-slate-600 bg-slate-50 hover:bg-slate-100 hover:text-slate-800 transition ease-in-out duration-150 focus:outline-none">
                                    <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>
                                    <div class="max-w-[100px] truncate">{{ Auth::user()->name }}</div>
                                    <svg class="fill-current h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">
                                    {{ __('Profile') }}
                                </x-dropdown-link>

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </header>

                <!-- Page Content Body -->
                <main class="flex-1">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>

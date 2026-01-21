<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

        <title>Mould Track Mobile</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-800 bg-gray-50 pb-20"> {{-- pb-20 for bottom nav --}}
        
        {{-- Top Bar --}}
        <header class="bg-white shadow-sm px-4 py-3 flex items-center justify-between sticky top-0 z-50">
            <div class="flex items-center gap-2">
                <x-application-logo class="h-6 w-auto fill-current text-blue-600" />
                <span class="font-bold text-lg tracking-tight">MouldTrack</span>
            </div>
            <div class="flex items-center gap-2">
                 <div class="h-8 w-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-600">
                    {{ substr(auth()->user()->name, 0, 2) }}
                 </div>
            </div>
        </header>

        {{-- Main Content --}}
        <main class="p-4">
            {{ $slot }}
        </main>

        {{-- Bottom Nav --}}
        <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 flex justify-around items-center px-2 py-2 pb-safe z-50 shadow-[0_-2px_10px_rgba(0,0,0,0.05)]">
            
            {{-- Home --}}
            <a href="{{ route('mobile.dashboard') }}" class="flex flex-col items-center p-2 {{ request()->routeIs('mobile.dashboard') ? 'text-blue-600' : 'text-gray-400 hover:text-gray-600' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span class="text-[10px] font-medium mt-0.5">Home</span>
            </a>

            {{-- Scan (Center Prominent) --}}
            <a href="{{ route('mobile.scanner') }}" class="flex flex-col items-center -mt-6">
                <div class="bg-blue-600 rounded-full p-4 shadow-lg ring-4 ring-gray-50 {{ request()->routeIs('mobile.scanner') ? 'bg-blue-700' : 'hover:bg-blue-500' }}">
                     <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 16h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                </div>
                <span class="text-[10px] font-medium mt-1 text-gray-500">Scan</span>
            </a>

            {{-- Jobs/Runs --}}
            <a href="#" class="flex flex-col items-center p-2 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                <span class="text-[10px] font-medium mt-0.5">Jobs</span>
            </a>
            
        </nav>
    </body>
</html>

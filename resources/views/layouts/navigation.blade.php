<!-- Mobile backdrop overlay -->
<div x-show="sidebarOpen" 
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm lg:hidden" 
     @click="sidebarOpen = false"
     style="display: none;"></div>

<!-- Sidebar container -->
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
       class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-white border-r border-slate-150 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 lg:h-screen lg:sticky lg:top-0">
    
    <!-- Sidebar Header (Logo & Brand) -->
    <div class="flex h-16 items-center justify-between px-6 border-b border-slate-100 flex-shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 transition transform hover:scale-[1.02]">
            <x-application-logo class="block h-8 w-auto fill-current text-blue-600" />
            <span class="font-bold text-lg text-slate-800 tracking-tight">MouldTrack</span>
        </a>
        
        <!-- Close button for mobile drawer -->
        <button @click="sidebarOpen = false" class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:bg-slate-50 hover:text-slate-600 transition">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 space-y-6 overflow-y-auto px-4 py-6 scrollbar-thin scrollbar-thumb-slate-200">
        <!-- GENERAL -->
        <div class="space-y-1">
            <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <x-slot name="icon">
                    <svg class="w-full h-full" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                </x-slot>
                {{ __('Dashboard') }}
            </x-sidebar-link>
        </div>

        <!-- OPERATIONS -->
        @can('access_operations')
            <div class="space-y-1">
                <p class="px-4 text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Operations') }}</p>
                
                <x-sidebar-link :href="route('moulds.index')" :active="request()->routeIs('moulds.*')">
                    <x-slot name="icon">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                    </x-slot>
                    {{ __('Moulds Registry') }}
                </x-sidebar-link>

                <x-sidebar-link :href="route('runs.active')" :active="request()->routeIs('runs.*')">
                    <x-slot name="icon">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                        </svg>
                    </x-slot>
                    <span class="flex items-center justify-between w-full">
                        <span>{{ __('Active Runs') }}</span>
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                        </span>
                    </span>
                </x-sidebar-link>

                <x-sidebar-link :href="route('maintenance.work-orders')" :active="request()->routeIs('maintenance.work-orders')">
                    <x-slot name="icon">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </x-slot>
                    {{ __('Work Orders') }}
                </x-sidebar-link>

                <x-sidebar-link :href="route('maintenance.index')" :active="request()->routeIs('maintenance.index')">
                    <x-slot name="icon">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l-2.072-2.072a1.282 1.282 0 00-1.813 0l-4.57 4.57a1.28 1.28 0 000 1.813l1.813 1.813a1.28 1.28 0 001.813 0l4.57-4.57a1.282 1.282 0 000-1.813l-2.072-2.072zm0 0l5.47-5.47M17 12l-3-3m-1.5-1.5L17 3m-1.5 1.5L14 6" />
                        </svg>
                    </x-slot>
                    {{ __('Maint. Events') }}
                </x-sidebar-link>

                <x-sidebar-link :href="route('locations.move')" :active="request()->routeIs('locations.move')">
                    <x-slot name="icon">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                        </svg>
                    </x-slot>
                    {{ __('Mould Movement') }}
                </x-sidebar-link>
            </div>
        @endcan

        <!-- DEPARTMENT PORTALS -->
        <div class="space-y-1">
            <p class="px-4 text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Portals') }}</p>
            
            @can('view_production_section')
                <x-sidebar-link :href="route('production.index')" :active="request()->routeIs('production.index')">
                    <x-slot name="icon">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                        </svg>
                    </x-slot>
                    {{ __('Production') }}
                </x-sidebar-link>
            @endcan

            @can('view_maintenance_section')
                <x-sidebar-link :href="route('maintenance.home')" :active="request()->routeIs('maintenance.home')">
                    <x-slot name="icon">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l-2.072-2.072a1.282 1.282 0 00-1.813 0l-4.57 4.57a1.28 1.28 0 000 1.813l1.813 1.813a1.28 1.28 0 001.813 0l4.57-4.57a1.282 1.282 0 000-1.813l-2.072-2.072z" />
                        </svg>
                    </x-slot>
                    {{ __('Maintenance') }}
                </x-sidebar-link>
            @endcan

            @can('view_qa_section')
                <x-sidebar-link :href="route('qa.index')" :active="request()->routeIs('qa.index')">
                    <x-slot name="icon">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0110 21a3.745 3.745 0 01-3.068-1.593 3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0114 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                        </svg>
                    </x-slot>
                    {{ __('QA') }}
                </x-sidebar-link>
            @endcan
        </div>

        <!-- ANALYTICS & REPORTS -->
        @can('access_operations')
            <div class="space-y-1">
                <p class="px-4 text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Analytics & Reports') }}</p>
                
                <x-sidebar-link :href="route('reports.production')" :active="request()->routeIs('reports.production')">
                    <x-slot name="icon">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z" />
                        </svg>
                    </x-slot>
                    {{ __('Production Reports') }}
                </x-sidebar-link>

                <x-sidebar-link :href="route('reports.maintenance')" :active="request()->routeIs('reports.maintenance')">
                    <x-slot name="icon">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </x-slot>
                    {{ __('Maintenance Reports') }}
                </x-sidebar-link>

                <x-sidebar-link :href="route('alerts.pm_due')" :active="request()->routeIs('alerts.pm_due')">
                    <x-slot name="icon">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                    </x-slot>
                    {{ __('PM Due Alerts') }}
                </x-sidebar-link>
            </div>
        @endcan

        <!-- SYSTEM ADMINISTRATION -->
        @can('view_admin_panel')
            <div class="space-y-1">
                <p class="px-4 text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('Administration') }}</p>
                
                <x-sidebar-link :href="route('admin.index')" :active="request()->routeIs('admin.index')">
                    <x-slot name="icon">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                    </x-slot>
                    {{ __('Admin Dashboard') }}
                </x-sidebar-link>

                <x-sidebar-link :href="route('import.moulds')" :active="request()->routeIs('import.moulds')">
                    <x-slot name="icon">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </x-slot>
                    {{ __('Mould Importer') }}
                </x-sidebar-link>

                <x-sidebar-link :href="route('qr.moulds')" :active="request()->routeIs('qr.moulds')">
                    <x-slot name="icon">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5zM13.5 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5z" />
                        </svg>
                    </x-slot>
                    {{ __('QR Utility') }}
                </x-sidebar-link>

                <x-sidebar-link :href="route('plants.index')" :active="request()->routeIs('plants.*')">
                    <x-slot name="icon">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1m1.5 1.85V21M.75 12h3.375c.621 0 1.125-.504 1.125-1.125V7.5L2.25 9m0 0v12" />
                        </svg>
                    </x-slot>
                    {{ __('Plants Registry') }}
                </x-sidebar-link>

                <x-sidebar-link :href="route('zones.index')" :active="request()->routeIs('zones.*')">
                    <x-slot name="icon">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6z" />
                        </svg>
                    </x-slot>
                    {{ __('Zones Registry') }}
                </x-sidebar-link>

                <x-sidebar-link :href="route('machines.index')" :active="request()->routeIs('machines.*')">
                    <x-slot name="icon">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                        </svg>
                    </x-slot>
                    {{ __('Machines Registry') }}
                </x-sidebar-link>

                <x-sidebar-link :href="route('audit.index')" :active="request()->routeIs('audit.index')">
                    <x-slot name="icon">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                    </x-slot>
                    {{ __('Audit Logs') }}
                </x-sidebar-link>
            </div>
        @endcan
    </nav>

    <!-- Sidebar Footer (User Settings / Context) -->
    <div class="border-t border-slate-100 p-4 bg-slate-50/50 flex-shrink-0">
        <div class="flex items-center gap-3 truncate">
            <div class="h-9 w-9 rounded-full bg-blue-100 text-blue-600 font-bold flex items-center justify-center flex-shrink-0 text-sm">
                {{ substr(Auth::user()->name, 0, 2) }}
            </div>
            <div class="truncate flex-1">
                <p class="text-sm font-semibold text-slate-700 truncate leading-tight">{{ Auth::user()->name }}</p>
                <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email }}</p>
            </div>
        </div>
    </div>
</aside>

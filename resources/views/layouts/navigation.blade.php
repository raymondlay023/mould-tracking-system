<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    {{-- Dashboard --}}
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    {{-- Role landing --}}
                    @role('Admin')
                        <x-nav-link :href="route('admin.index')" :active="request()->routeIs('admin.index')">
                            {{ __('Admin') }}
                        </x-nav-link>
                    @endrole

                    @role('Production|Admin')
                        <x-nav-link :href="route('production.index')" :active="request()->routeIs('production.index')">
                            {{ __('Production') }}
                        </x-nav-link>
                    @endrole

                    @role('Maintenance|Admin')
                        <x-nav-link :href="route('maintenance.home')" :active="request()->routeIs('maintenance.home')">
                            {{ __('Maintenance') }}
                        </x-nav-link>
                    @endrole

                    @role('QA|Admin')
                        <x-nav-link :href="route('qa.index')" :active="request()->routeIs('qa.index')">
                            {{ __('QA') }}
                        </x-nav-link>
                    @endrole

                    {{-- Operasional --}}
                    @role('Admin|Production|Maintenance|QA|Viewer')
                        <x-nav-link :href="route('moulds.index')" :active="request()->routeIs('moulds.*')">
                            {{ __('Moulds') }}
                        </x-nav-link>

                        <x-nav-link :href="route('runs.active')" :active="request()->routeIs('runs.*')">
                            {{ __('Runs') }}
                        </x-nav-link>

                        <x-nav-link :href="route('maintenance.index')" :active="request()->routeIs('maintenance.index')">
                            {{ __('Maintenance Events') }}
                        </x-nav-link>

                        <x-nav-link :href="route('locations.move')" :active="request()->routeIs('locations.move')">
                            {{ __('Move Location') }}
                        </x-nav-link>

                        <x-nav-link :href="route('alerts.pm_due')" :active="request()->routeIs('alerts.pm_due')">
                            {{ __('PM Due') }}
                        </x-nav-link>

                        <x-nav-link :href="route('reports.production')" :active="request()->routeIs('reports.production*')">
                            {{ __('Production Report') }}
                        </x-nav-link>

                        <x-nav-link :href="route('reports.maintenance')" :active="request()->routeIs('reports.maintenance*')">
                            {{ __('Maintenance Report') }}
                        </x-nav-link>
                    @endrole

                    {{-- Admin tools --}}
                    @role('Admin')
                        <x-nav-link :href="route('import.moulds')" :active="request()->routeIs('import.moulds')">
                            {{ __('Import Mould') }}
                        </x-nav-link>

                        <x-nav-link :href="route('qr.moulds')" :active="request()->routeIs('qr.moulds')">
                            {{ __('QR Mould') }}
                        </x-nav-link>

                        <x-nav-link :href="route('audit.index')" :active="request()->routeIs('audit.index')">
                            {{ __('Audit Log') }}
                        </x-nav-link>

                        <x-nav-link :href="route('plants.index')" :active="request()->routeIs('plants.index')">
                            {{ __('Plants') }}
                        </x-nav-link>

                        <x-nav-link :href="route('zones.index')" :active="request()->routeIs('zones.index')">
                            {{ __('Zones') }}
                        </x-nav-link>

                        <x-nav-link :href="route('machines.index')" :active="request()->routeIs('machines.index')">
                            {{ __('Machines') }}
                        </x-nav-link>
                    @endrole
                </div>

            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

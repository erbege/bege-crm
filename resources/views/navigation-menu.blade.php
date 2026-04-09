<nav x-data="{ open: false }"
    class="bg-white/80 dark:bg-gray-800/90 backdrop-blur-xl sticky top-0 z-40 border-b border-gray-100 dark:border-gray-700/50 transition-all duration-300 supports-[backdrop-filter]:bg-white/60 dark:supports-[backdrop-filter]:bg-gray-900/60">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate>
                        <x-application-mark class="block h-9 w-auto" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <!-- Langganan Dropdown -->
                    <div class="hidden sm:flex sm:items-center" x-data="{ open: false }">
                        <div class="relative">
                            <button @click="open = !open" @click.away="open = false"
                                class="inline-flex items-center px-1 pt-1 text-xs font-bold uppercase tracking-widest leading-5 {{ request()->routeIs('customers.*') || request()->routeIs('subscriptions.*') ? 'text-gray-900 dark:text-gray-100 border-b-2 border-indigo-600 dark:border-indigo-500' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 border-b-2 border-transparent' }} focus:outline-none transition duration-150 ease-in-out">
                                {{ __('Langganan') }}
                                <svg class="ms-1 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute z-50 mt-2 w-48 rounded-xl shadow-2xl py-1 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/50">
                                <a href="{{ route('customers.index') }}" wire:navigate
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('customers.*') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                    {{ __('Daftar Pelanggan') }}
                                </a>
                                <a href="{{ route('subscriptions.index') }}" wire:navigate
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('subscriptions.index') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                    {{ __('Daftar Berlangganan') }}
                                </a>
                                <a href="{{ route('subscriptions.online') }}" wire:navigate
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('subscriptions.online') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                    {{ __('Pelanggan Online') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Hotspot Dropdown -->
                    <div class="hidden sm:flex sm:items-center" x-data="{ open: false }">
                        <div class="relative">
                            <button @click="open = !open" @click.away="open = false"
                                class="inline-flex items-center px-1 pt-1 text-xs font-bold uppercase tracking-widest leading-5 {{ request()->routeIs('hotspot.*') ? 'text-gray-900 dark:text-gray-100 border-b-2 border-indigo-600 dark:border-indigo-500' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 border-b-2 border-transparent' }} focus:outline-none transition duration-150 ease-in-out">
                                Hotspot
                                <svg class="ms-1 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute z-50 mt-2 w-48 rounded-xl shadow-2xl py-1 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/50">
                                <a href="{{ route('hotspot.profiles') }}" wire:navigate
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('hotspot.profiles') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                    Profil Hotspot
                                </a>
                                <a href="{{ route('hotspot.vouchers') }}" wire:navigate
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('hotspot.vouchers') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                    Daftar Voucher
                                </a>
                                <a href="{{ route('hotspot.templates') }}" wire:navigate
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('hotspot.templates') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                    Template Voucher
                                </a>
                                <a href="{{ route('hotspot.generate') }}" wire:navigate
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('hotspot.generate') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                    Generate Voucher
                                </a>
                                <a href="{{ route('hotspot.active-sessions') }}" wire:navigate
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('hotspot.active-sessions') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                    Monitor Sesi Aktif
                                </a>
                            </div>
                        </div>
                    </div>

                    <x-nav-link href="{{ route('invoices.index') }}" :active="request()->routeIs('invoices.*')"
                        wire:navigate>
                        {{ __('Tagihan') }}
                    </x-nav-link>

                    <x-nav-link href="{{ route('notifications.index') }}"
                        :active="request()->routeIs('notifications.*')" wire:navigate>
                        {{ __('Notifikasi') }}
                    </x-nav-link>

                    <!-- Reports Dropdown -->
                    <div id="reports-nav-dropdown" class="hidden sm:flex sm:items-center space-x-8 sm:-my-px"
                        x-data="{ open: false }">
                        <div class="relative">
                            <button @click="open = !open" @click.away="open = false"
                                class="inline-flex items-center px-1 pt-1 text-xs font-bold uppercase tracking-widest leading-5 {{ request()->routeIs('reports.*') ? 'text-gray-900 dark:text-gray-100 border-b-2 border-indigo-600 dark:border-indigo-500' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 border-b-2 border-transparent' }} focus:outline-none transition duration-150 ease-in-out">
                                <span class="flex items-center">
                                    <!-- <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg> -->
                                    {{ __('Laporan') }}
                                </span>
                                <svg class="ms-1 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute z-50 mt-2 w-48 rounded-xl shadow-2xl py-1 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/50">
                                <a href="{{ route('reports.index') }}" wire:navigate
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('reports.index') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                    {{ __('Ringkasan') }}
                                </a>
                                <a href="{{ route('reports.financial') }}" wire:navigate
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('reports.financial') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                    {{ __('Keuangan') }}
                                </a>
                                <a href="{{ route('reports.customers') }}" wire:navigate
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('reports.customers') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                    {{ __('Pelanggan') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Master Data Dropdown -->
                    <div class="hidden sm:flex sm:items-center" x-data="{ open: false }">
                        <div class="relative">
                            <button @click="open = !open" @click.away="open = false"
                                class="inline-flex items-center px-1 pt-1 text-xs font-bold uppercase tracking-widest leading-5 {{ request()->routeIs('master-data.*') ? 'text-gray-900 dark:text-gray-100 border-b-2 border-indigo-600 dark:border-indigo-500' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 border-b-2 border-transparent' }} focus:outline-none transition duration-150 ease-in-out">
                                Master Data
                                <svg class="ms-1 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute z-50 mt-2 w-48 rounded-xl shadow-2xl py-1 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/50">
                                <a href="{{ route('master-data.nas') }}" wire:navigate
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('master-data.nas') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                    NAS / Mikrotik
                                </a>

                                <a href="{{ route('master-data.bw-profiles') }}" wire:navigate
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('master-data.bw-profiles') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                    Profil Bandwidth
                                </a>
                                <a href="{{ route('master-data.packages') }}" wire:navigate
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('master-data.bw-profiles') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                    Paket Internet
                                </a>
                                <a href="{{ route('master-data.olts') }}" wire:navigate
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('master-data.olts') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                    OLT Management
                                </a>

                                <!-- Coverage Submenu (Lev 2) -->
                                <div
                                    class="relative group px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer flex justify-between items-center">
                                    <span>Coverage</span>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>

                                    <!-- Submenu Content -->
                                    <div
                                        class="absolute left-full top-0 w-48 rounded-xl shadow-2xl py-1 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/50 hidden group-hover:block ml-1">
                                        <a href="{{ route('coverage.areas') }}" wire:navigate
                                            class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('coverage.areas') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                            Wilayah
                                        </a>
                                        <a href="{{ route('coverage.points') }}" wire:navigate
                                            class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('coverage.points') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                            Titik ODP/ODC
                                        </a>
                                        <a href="{{ route('coverage.map') }}" wire:navigate
                                            class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ request()->routeIs('coverage.map') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                            Peta Coverage
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <x-nav-link href="{{ route('tickets.index') }}" :active="request()->routeIs('tickets.*')"
                        wire:navigate>
                        {{ __('Tiket') }}
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Theme Switcher -->
                <button x-data="{
                    darkMode: localStorage.getItem('color-theme') === 'dark' || (!localStorage.getItem('color-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),
                    toggle() {
                        this.darkMode = !this.darkMode;
                        localStorage.setItem('color-theme', this.darkMode ? 'dark' : 'light');
                        
                        // Disable transitions temporarily to prevent flashing/lag
                        const style = document.createElement('style');
                        style.textContent = '*, *::before, *::after { transition: none !important; }';
                        document.head.appendChild(style);
                        
                        document.documentElement.classList.toggle('dark', this.darkMode);
                        
                        // Re-enable transitions
                        setTimeout(() => {
                            style.remove();
                        }, 50);
                    }
                }" x-init="document.documentElement.classList.toggle('dark', darkMode)" @click="toggle()"
                    class="p-2 rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition"
                    title="Toggle dark/light mode">
                    <!-- Sun icon (shown in dark mode) -->
                    <svg x-show="darkMode" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <!-- Moon icon (shown in light mode) -->
                    <svg x-show="!darkMode" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 0 0 8.354-5.646z" />
                    </svg>
                </button>
                <!-- Teams Dropdown -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="ms-3 relative">
                        <x-dropdown align="right" width="60">
                            <x-slot name="trigger">
                                <span class="inline-flex rounded-md">
                                    <button type="button"
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                        {{ Auth::user()->currentTeam->name }}

                                        <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                        </svg>
                                    </button>
                                </span>
                            </x-slot>

                            <x-slot name="content">
                                <div class="w-60">
                                    <!-- Team Management -->
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        {{ __('Manage Team') }}
                                    </div>

                                    <!-- Team Settings -->
                                    <x-dropdown-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">
                                        {{ __('Team Settings') }}
                                    </x-dropdown-link>

                                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                        <x-dropdown-link href="{{ route('teams.create') }}">
                                            {{ __('Create New Team') }}
                                        </x-dropdown-link>
                                    @endcan

                                    <!-- Team Switcher -->
                                    @if (Auth::user()->allTeams()->count() > 1)
                                        <div class="border-t border-gray-200 dark:border-gray-600"></div>

                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            {{ __('Switch Teams') }}
                                        </div>

                                        @foreach (Auth::user()->allTeams() as $team)
                                            <x-switchable-team :team="$team" />
                                        @endforeach
                                    @endif
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endif

                <!-- Settings Dropdown -->
                <div class="ms-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                <img class="size-8 rounded-full object-cover"
                                    src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <!-- Account Management -->
                            <x-dropdown-link href="{{ route('profile.show') }}" wire:navigate>
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <div class="border-t border-gray-200 dark:border-gray-600"></div>

                            <!-- Administration -->
                            @role('super-admin|admin')
                            <x-dropdown-link href="{{ route('admin.users') }}" wire:navigate>
                                {{ __('Users') }}
                            </x-dropdown-link>
                            @endrole

                            @role('super-admin')
                            <x-dropdown-link href="{{ route('admin.roles') }}" wire:navigate>
                                {{ __('Roles & Permissions') }}
                            </x-dropdown-link>
                            @endrole

                            @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin'))
                                <div class="border-t border-gray-200 dark:border-gray-600"></div>
                            @endif

                            <!-- Settings -->
                            <x-dropdown-link href="{{ route('settings.index') }}" wire:navigate>
                                {{ __('Settings') }}
                            </x-dropdown-link>

                            <div class="border-t border-gray-200 dark:border-gray-600"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf

                                <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
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
            <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')"
                wire:navigate>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <!-- Langganan Links -->
            <div class="block px-4 py-2 text-xs text-gray-400 uppercase">
                {{ __('Langganan') }}
            </div>
            <x-responsive-nav-link href="{{ route('customers.index') }}" :active="request()->routeIs('customers.*')"
                wire:navigate>
                {{ __('Daftar Pelanggan') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('subscriptions.index') }}"
                :active="request()->routeIs('subscriptions.index')" wire:navigate>
                {{ __('Daftar Berlangganan') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('subscriptions.online') }}"
                :active="request()->routeIs('subscriptions.online')" wire:navigate>
                {{ __('Pelanggan Online') }}
            </x-responsive-nav-link>

            <!-- Hotspot Links -->
            <div class="block px-4 py-2 text-xs text-gray-400 uppercase">
                Hotspot
            </div>
            <x-responsive-nav-link href="{{ route('hotspot.profiles') }}"
                :active="request()->routeIs('hotspot.profiles')" wire:navigate>
                Profil Hotspot
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('hotspot.vouchers') }}"
                :active="request()->routeIs('hotspot.vouchers')" wire:navigate>
                Daftar Voucher
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('hotspot.templates') }}"
                :active="request()->routeIs('hotspot.templates')" wire:navigate>
                Template Voucher
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('hotspot.generate') }}"
                :active="request()->routeIs('hotspot.generate')" wire:navigate>
                Generate Voucher
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('hotspot.active-sessions') }}"
                :active="request()->routeIs('hotspot.active-sessions')" wire:navigate>
                Monitor Sesi Aktif
            </x-responsive-nav-link>

            <x-responsive-nav-link href="{{ route('invoices.index') }}" :active="request()->routeIs('invoices.*')"
                wire:navigate>
                {{ __('Tagihan') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link href="{{ route('notifications.index') }}"
                :active="request()->routeIs('notifications.*')" wire:navigate>
                {{ __('Notifikasi') }}
            </x-responsive-nav-link>

            <!-- Laporan Links -->
            <div class="block px-4 py-2 text-xs text-gray-400 uppercase">
                {{ __('Laporan') }}
            </div>
            <x-responsive-nav-link href="{{ route('reports.index') }}" :active="request()->routeIs('reports.index')"
                wire:navigate>
                {{ __('Ringkasan') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('reports.financial') }}"
                :active="request()->routeIs('reports.financial')" wire:navigate>
                {{ __('Keuangan') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('reports.customers') }}"
                :active="request()->routeIs('reports.customers')" wire:navigate>
                {{ __('Pelanggan') }}
            </x-responsive-nav-link>



            <!-- Master Data Links -->
            <div class="block px-4 py-2 text-xs text-gray-400 uppercase">
                Master Data
            </div>
            <x-responsive-nav-link href="{{ route('master-data.nas') }}" :active="request()->routeIs('master-data.nas')"
                wire:navigate>
                NAS / Mikrotik
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('master-data.bw-profiles') }}"
                :active="request()->routeIs('master-data.bw-profiles')" wire:navigate>
                Profil Bandwidth
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('master-data.packages') }}"
                :active="request()->routeIs('master-data.packages')" wire:navigate>
                Paket Internet
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('master-data.olts') }}"
                :active="request()->routeIs('master-data.olts')" wire:navigate>
                OLT Management
            </x-responsive-nav-link>

            <!-- Coverage Subsection -->
            <div class="block px-4 py-2 text-xs text-gray-400 uppercase bg-gray-50 dark:bg-gray-800/50 mt-2">
                Coverage
            </div>
            <x-responsive-nav-link href="{{ route('coverage.areas') }}" :active="request()->routeIs('coverage.areas')"
                wire:navigate class="pl-8">
                Wilayah
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('coverage.points') }}" :active="request()->routeIs('coverage.points')"
                wire:navigate class="pl-8">
                Titik ODP/ODC
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('coverage.map') }}" :active="request()->routeIs('coverage.map')"
                wire:navigate class="pl-8">
                Peta Coverage
            </x-responsive-nav-link>

            <x-responsive-nav-link href="{{ route('tickets.index') }}" :active="request()->routeIs('tickets.*')"
                wire:navigate>
                {{ __('Tiket') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <!-- Theme Switcher (Mobile) -->
            <div class="px-4 py-2">
                <button x-data="{
                    darkMode: localStorage.getItem('color-theme') === 'dark' || (!localStorage.getItem('color-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),
                    toggle() {
                        this.darkMode = !this.darkMode;
                        localStorage.setItem('color-theme', this.darkMode ? 'dark' : 'light');

                        // Disable transitions temporarily
                        const style = document.createElement('style');
                        style.textContent = '*, *::before, *::after { transition: none !important; }';
                        document.head.appendChild(style);

                        document.documentElement.classList.toggle('dark', this.darkMode);

                        setTimeout(() => {
                            style.remove();
                        }, 50);
                    }
                }" x-init="document.documentElement.classList.toggle('dark', darkMode)" @click="toggle()"
                    class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <!-- Sun icon -->
                    <svg x-show="darkMode" class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <!-- Moon icon -->
                    <svg x-show="!darkMode" class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 0 0 8.354-5.646z" />
                    </svg>
                    <span x-text="darkMode ? 'Light Mode' : 'Dark Mode'"></span>
                </button>
            </div>
            <div class="flex items-center px-4">
                <div class="shrink-0 me-3">
                    <img class="size-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}"
                        alt="{{ Auth::user()->name }}" />
                </div>

                <div>
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Account Management -->
                <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')"
                    wire:navigate>
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <div class="border-t border-gray-200 dark:border-gray-600"></div>

                <!-- Administration -->
                @role('super-admin|admin')
                <x-responsive-nav-link href="{{ route('admin.users') }}" :active="request()->routeIs('admin.users')"
                    wire:navigate>
                    {{ __('Users') }}
                </x-responsive-nav-link>
                @endrole

                @role('super-admin')
                <x-responsive-nav-link href="{{ route('admin.roles') }}" :active="request()->routeIs('admin.roles')"
                    wire:navigate>
                    {{ __('Roles & Permissions') }}
                </x-responsive-nav-link>
                @endrole

                @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin'))
                    <div class="border-t border-gray-200 dark:border-gray-600"></div>
                @endif


                <!-- Settings -->
                <x-responsive-nav-link href="{{ route('settings.index') }}" :active="request()->routeIs('settings.*')"
                    wire:navigate>
                    {{ __('Settings') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf

                    <x-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>

                <!-- Team Management -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="border-t border-gray-200 dark:border-gray-600"></div>

                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Manage Team') }}
                    </div>

                    <!-- Team Settings -->
                    <x-responsive-nav-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}"
                        :active="request()->routeIs('teams.show')">
                        {{ __('Team Settings') }}
                    </x-responsive-nav-link>

                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                        <x-responsive-nav-link href="{{ route('teams.create') }}" :active="request()->routeIs('teams.create')">
                            {{ __('Create New Team') }}
                        </x-responsive-nav-link>
                    @endcan

                    <!-- Team Switcher -->
                    @if (Auth::user()->allTeams()->count() > 1)
                        <div class="border-t border-gray-200 dark:border-gray-600"></div>

                        <div class="block px-4 py-2 text-xs text-gray-400">
                            {{ __('Switch Teams') }}
                        </div>

                        @foreach (Auth::user()->allTeams() as $team)
                            <x-switchable-team :team="$team" component="responsive-nav-link" />
                        @endforeach
                    @endif
                @endif
            </div>
        </div>
    </div>
</nav>
<nav x-data="{ open: false }"
     class="bg-gradient-to-br from-primary-dark to-primary-soft shadow-xl text-white font-semibold">

    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center gap-4">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-accent" />
                    </a>
                </div>
                <!-- Navigation Links -->
                <div class="hidden sm:flex gap-2 ml-10">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-white border-b-2 border-transparent hover:border-accent hover:text-accent transition-all duration-200 font-bold px-2 py-1">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('entradas.create')" :active="request()->routeIs('entradas.create')" class="text-white border-b-2 border-transparent hover:border-accent hover:text-accent transition-all duration-200 font-bold px-2 py-1">
                        {{ __('Entrada Productos') }}
                    </x-nav-link>
                    <x-nav-link :href="route('ventas.create')" :active="request()->routeIs('ventas.create')" class="text-white border-b-2 border-transparent hover:border-accent hover:text-accent transition-all duration-200 font-bold px-2 py-1">
                        {{ __('Venta Productos') }}
                    </x-nav-link>
                    <x-nav-link :href="route('traslados.create')" :active="request()->routeIs('traslados.create')" class="text-white border-b-2 border-transparent hover:border-accent hover:text-accent transition-all duration-200 font-bold px-2 py-1">
                        {{ __('Traslado Productos') }}
                    </x-nav-link>
                    <x-nav-link :href="route('devoluciones.create')" :active="request()->routeIs('devoluciones.create')" class="text-white border-b-2 border-transparent hover:border-accent hover:text-accent transition-all duration-200 font-bold px-2 py-1">
                        {{ __('Devolución Productos') }}
                    </x-nav-link>
                </div>
            </div>
            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center gap-2">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 rounded-xl text-white bg-primary-soft hover:bg-accent hover:text-primary-dark shadow transition-all duration-200">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-2">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-xl text-primary hover:bg-accent hover:text-primary-dark focus:outline-none transition-all duration-200">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-primary-dark text-white border-t-2 border-primary">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="hover:bg-accent hover:text-primary-dark transition-all duration-200">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('entradas.create')" :active="request()->routeIs('entradas.create')" class="hover:bg-accent hover:text-primary-dark transition-all duration-200">
                {{ __('Entrada Productos') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('ventas.create')" :active="request()->routeIs('ventas.create')" class="hover:bg-accent hover:text-primary-dark transition-all duration-200">
                {{ __('Ventas Productos') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('traslados.create')" :active="request()->routeIs('traslados.create')" class="hover:bg-accent hover:text-primary-dark transition-all duration-200">
                {{ __('Traslado Productos') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('devoluciones.create')" :active="request()->routeIs('devoluciones.create')" class="hover:bg-accent hover:text-primary-dark transition-all duration-200">
                {{ __('Devolución Productos') }}
            </x-responsive-nav-link>
        </div>
        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t-2 border-primary-soft">
            <div class="px-4">
                <div class="font-medium text-base text-accent">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-primary-light">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="hover:bg-accent hover:text-primary-dark transition-all duration-200">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
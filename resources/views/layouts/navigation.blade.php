<nav x-data="{ open: false }" class="bg-black border-b border-zinc-900">
    <!-- Primary Navigation Menu -->
    <div class="max-w-6xl mx-auto px-6">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-10">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                        <x-application-logo class="block h-8 w-auto fill-current text-white" />
                        <span class="hidden sm:block text-sm font-semibold tracking-tight text-white">StudioTrack</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:flex items-center gap-6">
                    <a href="{{ route('home') }}"
                       class="text-sm font-semibold {{ request()->routeIs('home') ? 'text-white' : 'text-zinc-300 hover:text-white' }}">
                        Cerca
                    </a>

                    <a href="{{ route('dashboard') }}"
                       class="text-sm font-semibold {{ request()->routeIs('dashboard') ? 'text-white' : 'text-zinc-300 hover:text-white' }}">
                        Dashboard
                    </a>
                </div>
            </div>

            <!-- Right side -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2">
                @auth
                    <!-- Settings Dropdown -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-2 rounded-xl border border-zinc-800 bg-zinc-950/60 px-3 py-2 text-sm font-semibold text-zinc-200 hover:bg-zinc-900 hover:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400/40 transition">
                                <span class="max-w-[160px] truncate">{{ Auth::user()->name }}</span>

                                <svg class="h-4 w-4 text-zinc-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="bg-zinc-950 text-zinc-200 border border-zinc-800 rounded-xl shadow-xl overflow-hidden">
                                <x-dropdown-link :href="route('profile.edit')"
                                    class="block px-4 py-2 text-sm text-zinc-200 hover:bg-zinc-900 focus:bg-zinc-900 focus:text-white">
                                    {{ __('Profile') }}
                                </x-dropdown-link>

                                <div class="border-t border-zinc-900"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                        class="block px-4 py-2 text-sm text-zinc-200 hover:bg-zinc-900 focus:bg-zinc-900 focus:text-white"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </div>
                        </x-slot>
                    </x-dropdown>
                @endauth

                @guest
                    <a href="{{ route('login') }}"
                       class="rounded-xl border border-zinc-800 bg-zinc-950/60 px-4 py-2 text-sm font-semibold text-zinc-200 hover:bg-zinc-900 hover:text-white transition">
                        Accedi
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-black hover:bg-zinc-200 transition">
                            Registrati
                        </a>
                    @endif
                @endguest
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-xl text-zinc-300 hover:text-white hover:bg-zinc-900 focus:outline-none focus:ring-2 focus:ring-indigo-400/40 transition">
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
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-zinc-900">
        <div class="px-6 pt-3 pb-3 space-y-1">
            <a href="{{ route('home') }}"
               class="block rounded-xl px-3 py-2 text-sm font-semibold {{ request()->routeIs('home') ? 'bg-zinc-900 text-white' : 'text-zinc-300 hover:bg-zinc-900 hover:text-white' }}">
                Cerca
            </a>

            <a href="{{ route('dashboard') }}"
               class="block rounded-xl px-3 py-2 text-sm font-semibold {{ request()->routeIs('dashboard') ? 'bg-zinc-900 text-white' : 'text-zinc-300 hover:bg-zinc-900 hover:text-white' }}">
                Dashboard
            </a>
        </div>

        @auth
            <div class="pt-4 pb-3 border-t border-zinc-900">
                <div class="px-6">
                    <div class="font-semibold text-base text-white">{{ Auth::user()->name }}</div>
                    <div class="text-sm text-zinc-400">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 px-6 space-y-1">
                    <a href="{{ route('profile.edit') }}"
                       class="block rounded-xl px-3 py-2 text-sm font-semibold text-zinc-300 hover:bg-zinc-900 hover:text-white">
                        {{ __('Profile') }}
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}"
                           class="block rounded-xl px-3 py-2 text-sm font-semibold text-zinc-300 hover:bg-zinc-900 hover:text-white"
                           onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </a>
                    </form>
                </div>
            </div>
        @endauth

        @guest
            <div class="pt-4 pb-3 border-t border-zinc-900 px-6 space-y-2">
                <a href="{{ route('login') }}"
                   class="block rounded-xl px-3 py-2 text-sm font-semibold text-zinc-300 hover:bg-zinc-900 hover:text-white">
                    Accedi
                </a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                       class="block rounded-xl px-3 py-2 text-sm font-semibold text-zinc-300 hover:bg-zinc-900 hover:text-white">
                        Registrati
                    </a>
                @endif
            </div>
        @endguest
    </div>
</nav>

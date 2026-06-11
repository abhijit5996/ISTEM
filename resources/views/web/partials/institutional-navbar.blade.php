{{-- Premium Institutional Navbar - Research Centre Landing --}}
<header class="sticky top-0 z-50 border-b border-slate-200/50 bg-white/95 backdrop-blur-md transition-all duration-200 dark:border-white/5 dark:bg-slate-950/80 dark:backdrop-blur-xl" x-data="{ mobileNavOpen: false }">
    <div class="mx-auto w-full max-w-7xl px-4 md:px-8">
        <div class="flex h-14 items-center justify-between gap-6 md:gap-8" data-institutional-navbar>
            {{-- LEFT: Logo & Branding --}}
            <div class="flex shrink-0 items-center gap-2.5">
                <a href="{{ route('web.home') }}" class="group flex items-center gap-2">
                    <img src="{{ asset('frontend/assets/adamas-logo.png') }}" alt="Adamas University Logo" class="h-9 w-auto object-contain">
                    <span class="hidden lg:block">
                        <span class="block text-sm font-bold text-slate-900 dark:text-white leading-tight">Biomaterials Laboratory</span>
                        <span class="block text-xs font-medium text-slate-500 dark:text-slate-400">Research & Innovation</span>
                    </span>
                </a>
            </div>

            {{-- CENTER: Navigation Menu --}}
            <nav class="flex-1 hidden md:flex items-center justify-center gap-0.5" aria-label="Primary navigation">
                <a href="{{ route('web.home') }}" class="nav-premium {{ request()->routeIs('web.home') ? 'nav-premium-active' : '' }}" {{ request()->routeIs('web.home') ? 'aria-current="page"' : '' }}>Home</a>
                <a href="#team" class="nav-premium">Team</a>
                <a href="#publications" class="nav-premium">Publications</a>
                <a href="#projects" class="nav-premium">Projects</a>
                <a href="#research" class="nav-premium">Research Areas</a>
                <a href="{{ route('web.instruments') }}" class="nav-premium">Instruments</a>
                <a href="#contact" class="nav-premium">Contact</a>
            </nav>

            {{-- RIGHT: Auth & Controls --}}
            <div class="ml-auto flex items-center gap-3">
                {{-- Auth Controls --}}
                @if(session('web_user_id'))
                    <a href="{{ route('web.profile') }}" class="hidden md:inline-flex btn-institutional-login whitespace-nowrap">
                        My Profile
                    </a>
                @else
                    <a href="{{ route('web.login') }}" class="hidden md:inline-flex btn-institutional-login whitespace-nowrap">
                        Login
                    </a>
                @endif

                {{-- Dark Mode Toggle --}}
                <button type="button" class="institutional-icon-btn" data-theme-toggle title="Toggle dark mode" aria-label="Toggle dark mode">
                    <i data-lucide="moon" class="h-4 w-4"></i>
                    <span class="sr-only">Toggle dark mode</span>
                </button>

                {{-- Mobile Menu Toggle --}}
                <button
                    type="button"
                    class="lg:hidden institutional-icon-btn"
                    @click="mobileNavOpen = !mobileNavOpen"
                    :aria-expanded="mobileNavOpen.toString()"
                    aria-controls="institutional-mobile-nav"
                    title="Menu"
                    aria-label="Toggle menu"
                >
                    <i data-lucide="menu" class="h-4 w-4"></i>
                    <span class="sr-only">Open menu</span>
                </button>
            </div>
        </div>

        {{-- Mobile Navigation (Hidden by default) --}}
        <div
            id="institutional-mobile-nav"
            x-cloak
            x-show="mobileNavOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="border-t border-slate-200 py-4 dark:border-white/5"
            data-mobile-nav
        >
            <nav class="space-y-1" aria-label="Mobile navigation">
                <a href="{{ route('web.home') }}" class="nav-mobile">Home</a>
                <a href="#team" class="nav-mobile">Team</a>
                <a href="#publications" class="nav-mobile">Publications</a>
                <a href="#projects" class="nav-mobile">Projects</a>
                <a href="#research" class="nav-mobile">Research Areas</a>
                <a href="{{ route('web.instruments') }}" class="nav-mobile">Instruments</a>
                <a href="#contact" class="nav-mobile">Contact</a>
                @if(session('web_user_id'))
                    <a href="{{ route('web.profile') }}" class="btn-institutional-login mt-4 w-full text-center">
                        My Profile
                    </a>
                @else
                    <a href="{{ route('web.login') }}" class="btn-institutional-login mt-4 w-full text-center">
                        Login
                    </a>
                @endif
            </nav>
        </div>
    </div>
</header>

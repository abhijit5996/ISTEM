<!doctype html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Biomaterials Laboratory (BL) | Biomaterials Research & Instrument Booking' }}</title>
    <script>
        (function () {
            try {
                const storageKey = 'istem-theme';
                const savedTheme = localStorage.getItem(storageKey);
                const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const theme = savedTheme || (systemPrefersDark ? 'dark' : 'light');
                document.documentElement.classList.toggle('dark', theme === 'dark');
                document.documentElement.style.colorScheme = theme;
            } catch (error) {
                document.documentElement.classList.add('dark');
                document.documentElement.style.colorScheme = 'dark';
            }
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
@php
    $isHome = request()->routeIs('web.home');
    $activeSearch = (string) request()->query('search', '');
    $activeLocation = (string) request()->query('location', '');
@endphp
<body class="{{ $isHome ? 'home-page min-h-screen antialiased transition-colors duration-300' : 'app-shell min-h-screen antialiased transition-colors duration-300' }}">
<a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-70 focus:rounded-full focus:bg-slate-900 focus:px-4 focus:py-2 focus:text-white">Skip to content</a>

@if(session('web_admin_id') && request()->is('admin*'))
    {{-- Admin / Legacy header preserved for admin sessions --}}
    <header class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 backdrop-blur-md transition-all duration-200 dark:border-white/10 dark:bg-slate-950/65 dark:backdrop-blur-2xl" data-main-header>
    <div class="mx-auto w-full max-w-7xl px-4 md:px-8">
        <div class="flex h-16 items-center justify-between gap-3 md:gap-4" data-main-header-inner>
            <div class="flex min-w-0 shrink-0 items-center gap-2">
                <a href="{{ route('web.home') }}" class="group flex items-center gap-2.5 text-left">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-linear-to-br from-cyan-500 to-indigo-600 text-[10px] font-bold text-white shadow-sm dark:from-cyan-400 dark:via-sky-500 dark:to-indigo-500 dark:text-slate-950">BL</span>
                    <span class="hidden sm:block">
                        <span class="block text-sm font-bold leading-tight text-slate-900 dark:text-white">Biomaterials Laboratory</span>
                        <span class="block text-xs font-medium text-slate-600 dark:text-slate-300">Research & Instrument Booking</span>
                    </span>
                </a>
            </div>

            <div class="hidden min-w-0 flex-1 justify-center lg:flex">
                <form method="GET" action="{{ route('web.instruments') }}" class="global-search-shell flex w-full max-w-sm items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-2 py-1 shadow-xs dark:border-white/10 dark:bg-white/6">
                    <i data-lucide="search" class="h-3.5 w-3.5 text-slate-400 dark:text-slate-500"></i>
                    <input
                        type="search"
                        name="search"
                        value="{{ $activeSearch }}"
                        class="search-combo-input w-full border-0 bg-transparent px-1 py-1 text-xs text-slate-900 outline-none ring-0 placeholder:text-slate-400 dark:text-slate-100 dark:placeholder:text-slate-500"
                        placeholder="Search instruments..."
                        list="navbar-search-suggestions"
                    >
                    <select name="location" class="rounded-md border border-slate-200 bg-white px-2 py-0.5 text-[11px] font-semibold text-slate-600 dark:border-white/10 dark:bg-slate-900 dark:text-slate-300" data-location-dynamic>
                        <option value="">All Locations</option>
                        @foreach($navLocations as $location)
                            <option value="{{ $location }}" @selected($activeLocation === $location)>{{ $location }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-pill btn-primary h-8 px-3 text-xs">Search</button>
                </form>
            </div>

            <div class="ml-auto hidden items-center gap-3 lg:flex">
                <nav class="hidden items-center gap-0.5 border-r border-slate-200 pr-3 xl:flex dark:border-white/10" aria-label="Primary navigation">
                    <a href="{{ route('web.home') }}" class="nav-pill {{ request()->routeIs('web.home') ? 'nav-pill-active' : '' }}">Home</a>
                    <a href="{{ route('web.instruments') }}" class="nav-pill {{ request()->routeIs('web.instruments', 'web.instrument', 'web.category') ? 'nav-pill-active' : '' }}">Instruments</a>
                    <a href="{{ route('web.instruments') }}" class="nav-pill">Categories</a>
                </nav>

                <div class="flex items-center gap-1.5 border-r border-slate-200 pr-3 dark:border-white/10">
                    <a href="{{ route('web.favorites') }}" class="icon-btn relative" title="Favorites">
                        <i data-lucide="heart" class="h-3.5 w-3.5"></i>
                        @if(($navFavoriteCount ?? 0) > 0)
                            <span class="absolute -right-0.5 -top-0.5 inline-flex h-3.5 min-w-3.5 items-center justify-center rounded-full bg-rose-500 px-0.5 text-[8px] font-bold text-white" data-nav-favorites-count>{{ $navFavoriteCount }}</span>
                        @else
                            <span class="absolute -right-0.5 -top-0.5 hidden h-3.5 min-w-3.5 items-center justify-center rounded-full bg-rose-500 px-0.5 text-[8px] font-bold text-white" data-nav-favorites-count>0</span>
                        @endif
                    </a>
                    <button type="button" class="icon-btn relative" data-notification-toggle>
                        <i data-lucide="bell" class="h-3.5 w-3.5"></i>
                        <span class="sr-only">Notifications</span>
                        @if(($navNotificationCount ?? 0) > 0)
                            <span class="absolute -right-0.5 -top-0.5 inline-flex h-3.5 min-w-3.5 items-center justify-center rounded-full bg-cyan-500 px-0.5 text-[8px] font-bold text-white" data-nav-notification-count>{{ $navNotificationCount }}</span>
                        @else
                            <span class="absolute -right-0.5 -top-0.5 hidden h-3.5 min-w-3.5 items-center justify-center rounded-full bg-cyan-500 px-0.5 text-[8px] font-bold text-white" data-nav-notification-count>0</span>
                        @endif
                    </button>
                    <a href="{{ route('web.bag') }}" class="icon-btn relative" title="Bag">
                        <i data-lucide="shopping-bag" class="h-3.5 w-3.5"></i>
                        @if(($navBagCount ?? 0) > 0)
                            <span class="absolute -right-0.5 -top-0.5 inline-flex h-3.5 min-w-3.5 items-center justify-center rounded-full bg-slate-900 px-0.5 text-[8px] font-bold text-white dark:bg-slate-100 dark:text-slate-900" data-nav-bag-count>{{ $navBagCount }}</span>
                        @else
                            <span class="absolute -right-0.5 -top-0.5 hidden h-3.5 min-w-3.5 items-center justify-center rounded-full bg-slate-900 px-0.5 text-[8px] font-bold text-white dark:bg-slate-100 dark:text-slate-900" data-nav-bag-count>0</span>
                        @endif
                    </a>
                    <button type="button" class="icon-btn" data-theme-toggle>
                        <i data-lucide="moon" class="h-3.5 w-3.5"></i>
                        <span class="sr-only">Toggle dark mode</span>
                    </button>
                </div>

                @if(session('web_user_id'))
                    <div class="relative" data-profile-menu-wrap>
                        <button type="button" class="flex h-9 items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-1.5 py-0.5 dark:border-white/10 dark:bg-white/6" data-profile-menu-toggle>
                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-linear-to-br from-cyan-500 to-indigo-600 text-[10px] font-bold text-white">{{ strtoupper(substr(explode('@', session('web_user_email') ?: 'user@example.com')[0], 0, 1)) }}</span>
                            <span class="hidden max-w-20 truncate text-[11px] font-semibold text-slate-700 lg:inline-block dark:text-slate-300">{{ session('web_user_name') }}</span>
                            <i data-lucide="chevron-down" class="h-3.5 w-3.5 text-slate-400 dark:text-slate-500"></i>
                        </button>

                        <div class="profile-dropdown hidden" data-profile-dropdown>
                            <div class="border-b border-slate-200 px-3 py-2 dark:border-white/10">
                                <p class="text-xs font-semibold text-slate-900 dark:text-white" data-profile-name>{{ session('web_user_name') }}</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400" data-profile-email>{{ session('web_user_email') }}</p>
                            </div>
                            <a href="{{ route('web.profile') }}" class="profile-link">My Profile</a>
                            <a href="{{ route('web.my-bookings') }}" class="profile-link">My Bookings</a>
                            <a href="{{ route('web.favorites') }}" class="profile-link">Favorites</a>
                            <a href="{{ route('web.bag') }}" class="profile-link">Cart</a>
                            <a href="{{ route('web.queue-status') }}" class="profile-link">Queue Status</a>
                            <a href="{{ route('web.profile') }}" class="profile-link">Settings</a>
                            <div class="border-t border-slate-200 px-2.5 py-2 dark:border-white/10">
                                <form method="POST" action="{{ route('web.logout') }}">
                                    @csrf
                                    <button class="btn-pill btn-ghost w-full text-xs" type="submit">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('web.login') }}" class="btn-pill btn-ghost text-xs">Login</a>
                        <a href="{{ route('web.signup') }}" class="btn-pill btn-primary text-xs">Sign Up</a>
                    </div>
                @endif
            </div>

            <div class="ml-auto flex items-center gap-1.5 lg:hidden">
                <button
                    type="button"
                    class="icon-btn"
                    data-nav-search-toggle
                    aria-expanded="false"
                    aria-controls="global-mobile-search"
                    aria-label="Toggle search"
                >
                    <i data-lucide="search" class="h-3.5 w-3.5"></i>
                </button>
                <button
                    type="button"
                    class="icon-btn"
                    data-nav-menu-toggle
                    aria-expanded="false"
                    aria-controls="global-mobile-menu"
                    aria-label="Toggle navigation menu"
                >
                    <i data-lucide="menu" class="h-3.5 w-3.5"></i>
                    <span class="sr-only">Toggle navigation</span>
                </button>
            </div>
        </div>

        <datalist id="navbar-search-suggestions">
            @foreach($navCategories as $category)
                <option value="{{ $category }}"></option>
            @endforeach
            @foreach($navLocations as $location)
                <option value="{{ $location }}"></option>
            @endforeach
        </datalist>
    </div>

    <div id="global-mobile-search" class="hidden border-t border-slate-200 px-4 py-2 lg:hidden dark:border-white/10" data-nav-mobile-search>
        <form method="GET" action="{{ route('web.instruments') }}" class="mx-auto grid w-full max-w-7xl grid-cols-1 gap-2">
            <input type="search" name="search" value="{{ $activeSearch }}" class="input-ui" placeholder="Search instruments">
            <select name="location" class="select-ui" data-location-dynamic>
                <option value="">All Locations</option>
                @foreach($navLocations as $location)
                    <option value="{{ $location }}" @selected($activeLocation === $location)>{{ $location }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-pill btn-primary text-xs">Search</button>
        </form>
    </div>

    <div id="global-mobile-menu" class="hidden border-t border-slate-200 px-4 py-2 lg:hidden dark:border-white/10" data-nav-mobile-menu>
        <nav class="mx-auto grid w-full max-w-7xl grid-cols-2 gap-1" aria-label="Primary navigation mobile">
            <a href="{{ route('web.home') }}" class="nav-pill {{ request()->routeIs('web.home') ? 'nav-pill-active' : '' }} text-center text-xs">Home</a>
            <a href="{{ route('web.instruments') }}" class="nav-pill {{ request()->routeIs('web.instruments', 'web.instrument', 'web.category') ? 'nav-pill-active' : '' }} text-center text-xs">Instruments</a>
            <a href="{{ route('web.instruments') }}" class="nav-pill text-center text-xs">Categories</a>
            <a href="{{ route('web.bag') }}" class="nav-pill {{ request()->routeIs('web.bag', 'web.booking.form', 'web.booking.confirmation') ? 'nav-pill-active' : '' }} text-center text-xs">Bag</a>
            @if(session('web_user_id'))
                <a href="{{ route('web.profile') }}" class="nav-pill {{ request()->routeIs('web.profile', 'web.my-bookings') ? 'nav-pill-active' : '' }} text-center">Profile</a>
                <a href="{{ route('web.queue-status') }}" class="nav-pill {{ request()->routeIs('web.queue-status') ? 'nav-pill-active' : '' }} text-center">Queue</a>
            @else
                <a href="{{ route('web.login') }}" class="nav-pill text-center">Login</a>
                <a href="{{ route('web.signup') }}" class="nav-pill text-center">Sign Up</a>
            @endif
        </nav>

        <div class="mx-auto mt-3 flex w-full max-w-7xl flex-wrap items-center gap-2">
            <button type="button" class="icon-btn" data-notification-toggle>
                <i data-lucide="bell" class="h-4 w-4"></i>
                <span class="sr-only">Notifications</span>
            </button>
            <a href="{{ route('web.favorites') }}" class="icon-btn" title="Favorites">
                <i data-lucide="heart" class="h-4 w-4"></i>
            </a>
            <a href="{{ route('web.bag') }}" class="icon-btn" title="Bag">
                <i data-lucide="shopping-bag" class="h-4 w-4"></i>
            </a>
            <button type="button" class="icon-btn" data-theme-toggle>
                <i data-lucide="moon" class="h-4 w-4"></i>
                <span class="sr-only">Toggle dark mode</span>
            </button>
        </div>
    </div>

    <div class="hidden border-t border-slate-200/80 px-4 py-3 lg:hidden dark:border-slate-800" data-notification-dropdown>
        <div class="mx-auto w-full max-w-7xl">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Notifications</p>
        <div class="mt-2 space-y-2 text-sm" data-notification-list>
            <p class="rounded-xl border border-slate-200 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-800">Booking updates appear here</p>
            <p class="rounded-xl border border-slate-200 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-800">Queue updates appear here</p>
        </div>
        </div>
    </div>
</header>
@else
    {{-- Institution-styled global header for public pages --}}
    @include('web.partials.institutional-navbar')
@endif

@if($isHome)
    <main id="main-content" class="home-shell">
        @yield('content')
    </main>
@else
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-32 left-1/2 h-112 w-md -translate-x-1/2 rounded-full bg-cyan-200/60 blur-3xl dark:bg-cyan-500/20"></div>
        <div class="absolute bottom-0 right-0 h-80 w-80 rounded-full bg-amber-200/50 blur-3xl dark:bg-amber-400/10"></div>
    </div>

    <div class="relative mx-auto w-full max-w-7xl px-4 py-6 md:px-8 md:py-8">
        @if(session('web_admin_id') && request()->is('admin*'))
            <div x-data="{ mobileSidebarOpen: false }" x-init="$watch('mobileSidebarOpen', (val) => { if (typeof window.gsap === 'undefined') return; if (val) { $nextTick(() => window.gsap.to($refs.sidebar, { x: 0, duration: 0.36, ease: 'power3.out' })); } else { window.gsap.to($refs.sidebar, { x: '100%', duration: 0.32, ease: 'power3.in' }); } })" class="admin-shell grid gap-6 lg:grid-cols-[260px_1fr]">
                <aside class="hidden lg:block">
                    @include('components.ui.admin-sidebar')
                </aside>

                {{-- Mobile sidebar toggle and slide-over --}}
                <div class="lg:hidden mb-4">
                    <button @click="mobileSidebarOpen = true" class="icon-btn">
                        <i data-lucide="menu" class="h-4 w-4"></i>
                        <span class="sr-only">Open admin menu</span>
                    </button>
                </div>

                <div x-show="mobileSidebarOpen" x-cloak x-ref="mobileSidebar" class="fixed inset-0 z-90 flex lg:hidden">
                    <div @click="mobileSidebarOpen = false" class="fixed inset-0 bg-black/40 backdrop-blur-sm"></div>
                    <div x-ref="sidebar" style="transform: translateX(100%);" class="relative z-50 w-80 max-w-full" aria-hidden="true">
                        <div class="h-full overflow-y-auto">
                            @include('components.ui.admin-sidebar')
                        </div>
                    </div>
                </div>

                <main id="main-content" class="space-y-8">
                    @yield('breadcrumbs')
                    @yield('content')
                </main>
            </div>
        @else
            <main id="main-content" class="space-y-8">
                @yield('breadcrumbs')
                @yield('content')
            </main>
        @endif
    </div>
@endif

<footer class="mt-12 border-t border-slate-200 bg-white/80 backdrop-blur dark:border-slate-800 dark:bg-slate-900/70">
    <div class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="footer-shell grid grid-cols-1 gap-8 rounded-4xl p-6 lg:grid-cols-[1.15fr_0.85fr_0.85fr] lg:p-8">
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <span class="footer-mark">IS</span>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300">Biomaterials Laboratory</p>
                        <p class="text-sm text-slate-300">Biomaterials research, lab access, and translational innovation.</p>
                    </div>
                </div>
                <p class="max-w-xl text-sm leading-7 text-slate-300">
                    A premium institutional interface for discovering advanced instruments, coordinating research access, and keeping laboratory operations seamless.
                </p>
            </div>

            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Quick Links</p>
                <div class="mt-4 grid gap-3 text-sm text-slate-200">
                    <a href="{{ route('web.home') }}" class="footer-link">Home</a>
                    <a href="{{ route('web.instruments') }}" class="footer-link">Instruments</a>
                    <a href="{{ route('web.bag') }}" class="footer-link">Bag</a>
                    <a href="{{ session('web_user_id') ? route('web.profile') : route('web.login') }}" class="footer-link">Profile</a>
                </div>
                <div class="mt-5">
                    <a
                        href="{{ session('web_admin_id') ? route('web.admin.dashboard') : route('web.admin.login') }}"
                        class="btn-pill btn-ghost footer-action"
                    >
                        Admin Access
                    </a>
                </div>
            </div>

            <div class="lg:text-right">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Need Help?</p>
                <p class="mt-4 text-lg font-semibold text-white">support@istem.local</p>
                <p class="mt-2 text-sm text-slate-300">Mon - Sat, 9:00 AM to 6:00 PM</p>
                <div class="mt-6 flex flex-wrap gap-2 lg:justify-end">
                    <span class="footer-chip">Research-ready</span>
                    <span class="footer-chip">Mobile-first</span>
                    <span class="footer-chip">Production safe</span>
                </div>
            </div>
        </div>
    </div>
</footer>

@include('web.partials.flash-toasts')

<div class="fixed inset-0 z-80 hidden items-center justify-center bg-slate-900/50 p-4" data-global-modal>
    <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-3xl border border-white/20 bg-white p-5 shadow-2xl dark:border-slate-700 dark:bg-slate-900" data-global-modal-content></div>
</div>

@include('web.partials.notification-dropdown')

<button type="button" class="scroll-top-btn hidden" data-scroll-top aria-label="Scroll to top">
    <i data-lucide="arrow-up" class="h-4 w-4"></i>
</button>

@stack('scripts')
</body>
</html>

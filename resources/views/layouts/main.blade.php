<!doctype html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'ISTEM Catalyst Center - Instrument Booking System' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
@php
    $isHome = request()->routeIs('web.home');
    $activeSearch = (string) request()->query('search', '');
    $activeLocation = (string) request()->query('location', '');
@endphp
<body class="{{ $isHome ? 'home-page min-h-screen text-slate-900 antialiased transition-colors duration-300' : 'min-h-screen bg-slate-100 text-slate-900 antialiased transition-colors duration-300 dark:bg-slate-950 dark:text-slate-100' }}">
<a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-70 focus:rounded-full focus:bg-slate-900 focus:px-4 focus:py-2 focus:text-white">Skip to content</a>

<header class="sticky top-0 z-50 border-b border-slate-200/60 bg-white/80 backdrop-blur-xl transition-all duration-200 dark:border-slate-800 dark:bg-slate-900/80" data-main-header>
    <div class="mx-auto flex w-full max-w-7xl flex-wrap items-center gap-3 px-4 py-3 sm:px-6" data-main-header-inner>
        <a href="{{ route('web.home') }}" class="group flex shrink-0 items-center gap-3 text-left">
            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-linear-to-br from-cyan-500 to-teal-500 text-white shadow-lg shadow-cyan-500/35">IC</span>
            <span class="leading-tight">
                <span class="block text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700 dark:text-cyan-300">ISTEM Catalyst Center</span>
                <span class="block text-sm font-medium text-slate-700 dark:text-slate-300">Instrument Booking System</span>
            </span>
        </a>

        <form method="GET" action="{{ route('web.instruments') }}" class="hidden min-w-[20rem] flex-1 items-center gap-2 rounded-full border border-slate-200 bg-white px-2 py-1 shadow-sm lg:flex dark:border-slate-700 dark:bg-slate-900">
            <i data-lucide="search" class="h-4 w-4 text-slate-400"></i>
            <input
                type="search"
                name="search"
                value="{{ $activeSearch }}"
                class="w-full border-0 bg-transparent px-1 py-1.5 text-sm text-slate-700 outline-none ring-0 placeholder:text-slate-400 dark:text-slate-200"
                placeholder="Search instruments, categories, locations"
                list="navbar-search-suggestions"
            >
            <select name="location" class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                <option value="">All Locations</option>
                @foreach($navLocations as $location)
                    <option value="{{ $location }}" @selected($activeLocation === $location)>{{ $location }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-pill btn-primary px-3 py-1.5 text-xs">Search</button>
        </form>

        <datalist id="navbar-search-suggestions">
            @foreach($navCategories as $category)
                <option value="{{ $category }}"></option>
            @endforeach
            @foreach($navLocations as $location)
                <option value="{{ $location }}"></option>
            @endforeach
        </datalist>

        <button
            type="button"
            class="icon-btn ml-auto lg:hidden"
            data-nav-menu-toggle
            aria-expanded="false"
            aria-controls="global-mobile-menu"
            aria-label="Toggle navigation menu"
        >
            <i data-lucide="menu" class="h-4 w-4"></i>
            <span class="sr-only">Toggle navigation</span>
        </button>

        <nav class="hidden items-center gap-2 lg:flex" aria-label="Primary navigation">
            <a href="{{ route('web.home') }}" class="nav-pill {{ request()->routeIs('web.home') ? 'nav-pill-active' : '' }}">Home</a>
            <a href="{{ route('web.instruments') }}" class="nav-pill {{ request()->routeIs('web.instruments', 'web.instrument', 'web.category') ? 'nav-pill-active' : '' }}">Instruments</a>
            <a href="{{ route('web.instruments') }}" class="nav-pill">Categories</a>
            <a href="{{ route('web.bag') }}" class="nav-pill {{ request()->routeIs('web.bag', 'web.booking.form', 'web.booking.confirmation') ? 'nav-pill-active' : '' }}">Bag</a>
            <a href="{{ route('web.home') }}#services-heading" class="nav-pill">Services</a>
            @if(session('web_user_id'))
                <a href="{{ route('web.dashboard') }}" class="nav-pill {{ request()->routeIs('web.dashboard') ? 'nav-pill-active' : '' }}">Dashboard</a>
                <a href="{{ route('web.favorites') }}" class="nav-pill {{ request()->routeIs('web.favorites') ? 'nav-pill-active' : '' }}">Favorites</a>
                <a href="{{ route('web.queue-status') }}" class="nav-pill {{ request()->routeIs('web.queue-status') ? 'nav-pill-active' : '' }}">Queue</a>
                <a href="{{ route('web.profile') }}" class="nav-pill {{ request()->routeIs('web.profile', 'web.my-bookings') ? 'nav-pill-active' : '' }}">Profile</a>
            @endif
            @if(session('web_admin_id'))
                <a href="{{ route('web.admin.dashboard') }}" class="nav-pill {{ request()->routeIs('web.admin.*') ? 'nav-pill-active' : '' }}">Admin</a>
            @endif
        </nav>

        <div class="hidden items-center gap-2 lg:ml-auto lg:flex">
            <a href="{{ route('web.favorites') }}" class="icon-btn relative" title="Favorites">
                <i data-lucide="heart" class="h-4 w-4"></i>
                @if(($navFavoriteCount ?? 0) > 0)
                    <span class="absolute -right-1 -top-1 inline-flex h-4 min-w-4 items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold text-white">{{ $navFavoriteCount }}</span>
                @endif
            </a>
            <button type="button" class="icon-btn relative" data-notification-toggle>
                <i data-lucide="bell" class="h-4 w-4"></i>
                <span class="sr-only">Notifications</span>
                @if(($navNotificationCount ?? 0) > 0)
                    <span class="absolute -right-1 -top-1 inline-flex h-4 min-w-4 items-center justify-center rounded-full bg-cyan-500 px-1 text-[10px] font-bold text-white">{{ $navNotificationCount }}</span>
                @endif
            </button>
            <a href="{{ route('web.bag') }}" class="icon-btn relative" title="Bag">
                <i data-lucide="shopping-bag" class="h-4 w-4"></i>
                @if(($navBagCount ?? 0) > 0)
                    <span class="absolute -right-1 -top-1 inline-flex h-4 min-w-4 items-center justify-center rounded-full bg-slate-900 px-1 text-[10px] font-bold text-white dark:bg-slate-100 dark:text-slate-900">{{ $navBagCount }}</span>
                @endif
            </a>
            <button type="button" class="icon-btn" data-theme-toggle>
                <i data-lucide="moon" class="h-4 w-4"></i>
                <span class="sr-only">Toggle dark mode</span>
            </button>

            @if(session('web_user_id'))
                <div class="relative" data-profile-menu-wrap>
                    <button type="button" class="flex items-center gap-2 rounded-full border border-slate-200 bg-white px-2 py-1 dark:border-slate-700 dark:bg-slate-900" data-profile-menu-toggle>
                        <span class="hidden h-9 w-9 items-center justify-center rounded-full bg-slate-900 text-xs font-semibold text-white sm:inline-flex">{{ strtoupper(substr(explode('@', session('web_user_email') ?: 'user@example.com')[0], 0, 1)) }}</span>
                        <span class="hidden max-w-28 truncate text-xs font-semibold text-slate-600 sm:inline-block dark:text-slate-300">{{ session('web_user_name') }}</span>
                        <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                    </button>

                    <div class="profile-dropdown hidden" data-profile-dropdown>
                        <div class="border-b border-slate-200 px-4 py-3 dark:border-slate-700">
                            <p class="text-sm font-semibold">{{ session('web_user_name') }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ session('web_user_email') }}</p>
                        </div>
                        <a href="{{ route('web.profile') }}" class="profile-link">My Profile</a>
                        <a href="{{ route('web.my-bookings') }}" class="profile-link">My Bookings</a>
                        <a href="{{ route('web.favorites') }}" class="profile-link">Favorites</a>
                        <a href="{{ route('web.bag') }}" class="profile-link">Cart (Bag)</a>
                        <a href="{{ route('web.queue-status') }}" class="profile-link">Queue Status</a>
                        <a href="{{ route('web.profile') }}" class="profile-link">Settings</a>
                        @if(session('web_admin_id'))
                            <a href="{{ route('web.admin.dashboard') }}" class="profile-link">Admin Panel</a>
                        @else
                            <a href="{{ route('web.admin.login') }}" class="profile-link text-[11px] text-slate-400" title="Restricted">Support Access</a>
                        @endif
                        <div class="border-t border-slate-200 px-3 py-2 dark:border-slate-700">
                            <form method="POST" action="{{ route('web.logout') }}">
                                @csrf
                                <button class="btn-pill btn-ghost w-full" type="submit">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ route('web.login') }}" class="btn-pill btn-ghost hidden sm:inline-flex">Login</a>
                <a href="{{ route('web.signup') }}" class="btn-pill btn-primary hidden sm:inline-flex">Sign Up</a>
            @endif

            @if(session('web_admin_id'))
                <form method="POST" action="{{ route('web.admin.logout') }}">
                    @csrf
                    <button class="btn-pill btn-warn" type="submit">Admin Logout</button>
                </form>
            @endif
        </div>
    </div>

    <div id="global-mobile-menu" class="hidden border-t border-slate-200/80 px-4 py-3 lg:hidden dark:border-slate-800" data-nav-mobile-menu>
        <form method="GET" action="{{ route('web.instruments') }}" class="mb-3 grid grid-cols-1 gap-2 sm:grid-cols-[1fr_auto]">
            <input type="search" name="search" value="{{ $activeSearch }}" class="input-ui" placeholder="Search instruments">
            <select name="location" class="select-ui">
                <option value="">All Locations</option>
                @foreach($navLocations as $location)
                    <option value="{{ $location }}" @selected($activeLocation === $location)>{{ $location }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-pill btn-primary sm:col-span-2">Search</button>
        </form>

        <nav class="grid grid-cols-2 gap-2" aria-label="Primary navigation mobile">
            <a href="{{ route('web.home') }}" class="nav-pill {{ request()->routeIs('web.home') ? 'nav-pill-active' : '' }} text-center">Home</a>
            <a href="{{ route('web.instruments') }}" class="nav-pill {{ request()->routeIs('web.instruments', 'web.instrument', 'web.category') ? 'nav-pill-active' : '' }} text-center">Instruments</a>
            <a href="{{ route('web.bag') }}" class="nav-pill {{ request()->routeIs('web.bag', 'web.booking.form', 'web.booking.confirmation') ? 'nav-pill-active' : '' }} text-center">Bag</a>
            <a href="{{ route('web.instruments') }}" class="nav-pill text-center">Categories</a>
            @if(session('web_user_id'))
                <a href="{{ route('web.dashboard') }}" class="nav-pill {{ request()->routeIs('web.dashboard') ? 'nav-pill-active' : '' }} text-center">Dashboard</a>
                <a href="{{ route('web.favorites') }}" class="nav-pill {{ request()->routeIs('web.favorites') ? 'nav-pill-active' : '' }} text-center">Favorites</a>
                <a href="{{ route('web.queue-status') }}" class="nav-pill {{ request()->routeIs('web.queue-status') ? 'nav-pill-active' : '' }} text-center">Queue</a>
                <a href="{{ route('web.profile') }}" class="nav-pill {{ request()->routeIs('web.profile', 'web.my-bookings') ? 'nav-pill-active' : '' }} text-center">Profile</a>
            @endif
            <a href="{{ session('web_admin_id') ? route('web.admin.dashboard') : route('web.admin.login') }}" class="nav-pill {{ request()->routeIs('web.admin.*') ? 'nav-pill-active' : '' }} text-center">Admin</a>
        </nav>

        <div class="mt-3 flex flex-wrap items-center gap-2">
            <button type="button" class="icon-btn" data-notification-toggle>
                <i data-lucide="bell" class="h-4 w-4"></i>
                <span class="sr-only">Notifications</span>
            </button>
            <button type="button" class="icon-btn" data-theme-toggle>
                <i data-lucide="moon" class="h-4 w-4"></i>
                <span class="sr-only">Toggle dark mode</span>
            </button>

            @if(session('web_user_id'))
                <form method="POST" action="{{ route('web.logout') }}">
                    @csrf
                    <button class="btn-pill btn-ghost" type="submit">Logout</button>
                </form>
            @else
                <a href="{{ route('web.login') }}" class="btn-pill btn-ghost">Login</a>
                <a href="{{ route('web.signup') }}" class="btn-pill btn-primary">Sign Up</a>
            @endif
        </div>
    </div>

    <div class="hidden border-t border-slate-200/80 px-4 py-3 lg:hidden dark:border-slate-800" data-notification-dropdown>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Notifications</p>
        <div class="mt-2 space-y-2 text-sm">
            <p class="rounded-xl border border-slate-200 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-800">Booking updates appear here</p>
            <p class="rounded-xl border border-slate-200 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-800">Queue updates appear here</p>
        </div>
    </div>
</header>

@if($isHome)
    <main id="main-content" class="home-shell">
        @yield('content')
    </main>
@else
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-32 left-1/2 h-112 w-md -translate-x-1/2 rounded-full bg-cyan-200/60 blur-3xl dark:bg-cyan-500/20"></div>
        <div class="absolute bottom-0 right-0 h-80 w-80 rounded-full bg-amber-200/50 blur-3xl dark:bg-amber-400/10"></div>
    </div>

    <div class="relative mx-auto w-full max-w-7xl px-4 py-6 sm:px-6">
        <main id="main-content" class="space-y-6">
            @yield('breadcrumbs')
            @yield('content')
        </main>
    </div>
@endif

<footer class="mt-12 border-t border-slate-200 bg-white/80 backdrop-blur dark:border-slate-800 dark:bg-slate-900/70">
    <div class="mx-auto grid w-full max-w-7xl grid-cols-1 gap-4 px-4 py-6 text-sm text-slate-600 sm:px-6 md:grid-cols-3 dark:text-slate-300">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-cyan-700 dark:text-cyan-300">ISTEM Platform</p>
            <p class="mt-2">E-commerce style instrument discovery and booking for lab operations.</p>
        </div>
        <div>
            <p class="font-semibold text-slate-800 dark:text-slate-100">Quick Links</p>
            <div class="mt-2 flex flex-wrap gap-3">
                <a href="{{ route('web.home') }}" class="hover:text-cyan-700">Home</a>
                <a href="{{ route('web.instruments') }}" class="hover:text-cyan-700">Instruments</a>
                <a href="{{ route('web.bag') }}" class="hover:text-cyan-700">Bag</a>
                <a href="{{ session('web_user_id') ? route('web.profile') : route('web.login') }}" class="hover:text-cyan-700">Profile</a>
            </div>
        </div>
        <div class="md:text-right">
            <p class="font-semibold text-slate-800 dark:text-slate-100">Need help?</p>
            <p class="mt-2">support@istem.local</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">Mon - Sat, 9:00 AM to 6:00 PM</p>
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

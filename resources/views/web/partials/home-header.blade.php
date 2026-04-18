@php
    $userEmail = session('web_user_email');
    $userHandle = $userEmail ? explode('@', $userEmail)[0] : '';
    $userInitial = $userHandle !== '' ? strtoupper(substr($userHandle, 0, 1)) : 'U';
@endphp

<header class="home-header">
    <div class="home-header-inner">
        <a href="{{ route('web.home') }}" class="home-brand" aria-label="Go to homepage">
            <span class="home-brand-mark">IS</span>
            <span class="home-brand-copy">
                <span class="home-brand-name">ISTEM</span>
                <span class="home-brand-subtitle">Instrument Booking System</span>
            </span>
        </a>

        <button
            type="button"
            class="home-hamburger-btn lg:hidden"
            data-home-menu-toggle
            aria-expanded="false"
            aria-controls="home-mobile-menu"
            aria-label="Toggle mobile menu"
        >
            <i data-lucide="menu" class="h-5 w-5"></i>
        </button>

        <nav class="home-nav" aria-label="Primary navigation">
            <div class="home-nav-list">
                @foreach($navItems as $navItem)
                    <a href="{{ $navItem['url'] }}" class="home-nav-link">{{ $navItem['label'] }}</a>
                @endforeach
            </div>
        </nav>

        <div class="home-actions" aria-label="Quick actions">
            <div class="home-action-icons">
                <a href="{{ route('web.home') }}#popular-product" class="home-icon-btn" aria-label="Browse instruments">
                    <i data-lucide="search" class="h-4 w-4"></i>
                </a>
                <a href="{{ session('web_user_id') ? route('web.dashboard') : route('web.login') }}" class="home-icon-btn" aria-label="User account">
                    <i data-lucide="user" class="h-4 w-4"></i>
                </a>
                <a href="{{ session('web_user_id') ? route('web.favorites') : route('web.login') }}" class="home-icon-btn" aria-label="Favorites">
                    <i data-lucide="heart" class="h-4 w-4"></i>
                </a>
                <a href="{{ route('web.bag') }}" class="home-icon-btn" aria-label="Booking bag">
                    <i data-lucide="shopping-cart" class="h-4 w-4"></i>
                </a>
            </div>

            <div class="home-auth-controls">
                @if(session('web_user_id'))
                    <span class="home-user-badge" title="{{ $userEmail ?: 'User' }}" aria-label="Logged in user">{{ $userInitial }}</span>
                    <form method="POST" action="{{ route('web.logout') }}">
                        @csrf
                        <button class="home-auth-link" type="submit">Logout</button>
                    </form>
                @else
                    <a href="{{ route('web.login') }}" class="home-auth-link">Login</a>
                    <a href="{{ route('web.signup') }}" class="home-auth-link home-auth-link-primary">Sign Up</a>
                @endif
            </div>
        </div>
    </div>

    <div id="home-mobile-menu" class="home-mobile-panel hidden lg:hidden" data-home-mobile-menu>
        <nav class="home-mobile-nav" aria-label="Primary navigation mobile">
            @foreach($navItems as $navItem)
                <a href="{{ $navItem['url'] }}" class="home-mobile-nav-link">{{ $navItem['label'] }}</a>
            @endforeach
        </nav>

        <div class="home-mobile-menu-actions" aria-label="Mobile quick actions">
            <a href="{{ route('web.home') }}#popular-product" class="home-icon-btn" aria-label="Browse instruments">
                <i data-lucide="search" class="h-4 w-4"></i>
            </a>
            <a href="{{ session('web_user_id') ? route('web.dashboard') : route('web.login') }}" class="home-icon-btn" aria-label="User account">
                <i data-lucide="user" class="h-4 w-4"></i>
            </a>
            <a href="{{ session('web_user_id') ? route('web.favorites') : route('web.login') }}" class="home-icon-btn" aria-label="Favorites">
                <i data-lucide="heart" class="h-4 w-4"></i>
            </a>
            <a href="{{ route('web.bag') }}" class="home-icon-btn" aria-label="Booking bag">
                <i data-lucide="shopping-cart" class="h-4 w-4"></i>
            </a>
        </div>

        <div class="home-mobile-auth-controls">
            @if(session('web_user_id'))
                <span class="home-user-badge" title="{{ $userEmail ?: 'User' }}" aria-label="Logged in user">{{ $userInitial }}</span>
                <form method="POST" action="{{ route('web.logout') }}">
                    @csrf
                    <button class="home-auth-link" type="submit">Logout</button>
                </form>
            @else
                <a href="{{ route('web.login') }}" class="home-auth-link">Login</a>
                <a href="{{ route('web.signup') }}" class="home-auth-link home-auth-link-primary">Sign Up</a>
            @endif
        </div>
    </div>
</header>
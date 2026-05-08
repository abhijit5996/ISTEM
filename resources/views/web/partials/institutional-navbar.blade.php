{{-- Premium Institutional Navbar - Research Centre Landing --}}
<header class="sticky top-0 z-50 border-b border-slate-200/50 bg-white/95 backdrop-blur-md transition-all duration-200 dark:border-white/5 dark:bg-slate-950/80 dark:backdrop-blur-xl">
    <div class="mx-auto w-full max-w-7xl px-4 md:px-8">
        <div class="flex h-14 items-center justify-between gap-6 md:gap-8" data-institutional-navbar>
            {{-- LEFT: Logo & Branding --}}
            <div class="flex shrink-0 items-center gap-2.5">
                <a href="{{ route('web.home') }}" class="group flex items-center gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-linear-to-br from-cyan-500 to-indigo-600 text-xs font-bold text-white shadow-sm dark:from-cyan-400 dark:via-sky-500 dark:to-indigo-500 dark:text-slate-950">BL</span>
                    <span class="hidden lg:block">
                        <span class="block text-sm font-bold text-slate-900 dark:text-white leading-tight">Biomaterials Laboratory</span>
                        <span class="block text-xs font-medium text-slate-500 dark:text-slate-400">Research & Innovation</span>
                    </span>
                </a>
            </div>

            {{-- CENTER: Navigation Menu --}}
            <nav class="flex-1 hidden md:flex items-center justify-center gap-0.5" aria-label="Primary navigation">
                <a href="{{ route('web.home') }}" class="nav-premium {{ request()->routeIs('web.home') ? 'nav-premium-active' : '' }}">Home</a>
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
                <button type="button" class="institutional-icon-btn" data-theme-toggle title="Toggle dark mode">
                    <i data-lucide="moon" class="h-4 w-4"></i>
                    <span class="sr-only">Toggle dark mode</span>
                </button>

                {{-- Mobile Menu Toggle --}}
                <button type="button" class="lg:hidden institutional-icon-btn" data-mobile-nav-toggle title="Menu">
                    <i data-lucide="menu" class="h-4 w-4"></i>
                    <span class="sr-only">Open menu</span>
                </button>
            </div>
        </div>

        {{-- Mobile Navigation (Hidden by default) --}}
        <div class="hidden border-t border-slate-200 py-4 dark:border-white/5" data-mobile-nav>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileToggle = document.querySelector('[data-mobile-nav-toggle]');
    const mobileNav = document.querySelector('[data-mobile-nav]');

    if (mobileToggle && mobileNav) {
        mobileToggle.addEventListener('click', function() {
            mobileNav.classList.toggle('hidden');
        });

        // Close on navigation
        mobileNav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function() {
                mobileNav.classList.add('hidden');
            });
        });
    }
});
</script>

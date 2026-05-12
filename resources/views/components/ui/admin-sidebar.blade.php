<aside class="panel sticky top-20 h-[calc(100vh-5rem)] overflow-y-auto pr-0">
    <nav aria-label="Admin navigation" class="space-y-4">
        <div class="px-1">
            <a href="{{ route('web.admin.dashboard') }}" class="block rounded-xl px-3 py-2 text-sm font-semibold text-slate-900 hover:bg-slate-100 dark:text-slate-100"> 
                <i data-lucide="home" class="inline-block h-4 w-4 mr-2"></i> Dashboard
            </a>
            <a href="{{ route('web.admin.instruments') }}" class="block rounded-xl px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:text-slate-300"> 
                <i data-lucide="shield" class="inline-block h-4 w-4 mr-2"></i> Instruments
            </a>
            <a href="{{ route('web.admin.bookings') }}" class="block rounded-xl px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:text-slate-300"> 
                <i data-lucide="calendar" class="inline-block h-4 w-4 mr-2"></i> Bookings
            </a>
            <a href="{{ route('web.admin.queue') }}" class="block rounded-xl px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:text-slate-300"> 
                <i data-lucide="users" class="inline-block h-4 w-4 mr-2"></i> Queue
            </a>
            <a href="{{ route('web.admin.analytics') }}" class="block rounded-xl px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:text-slate-300"> 
                <i data-lucide="bar-chart-2" class="inline-block h-4 w-4 mr-2"></i> Analytics
            </a>
            <a href="{{ route('web.admin.users') }}" class="block rounded-xl px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:text-slate-300"> 
                <i data-lucide="user-check" class="inline-block h-4 w-4 mr-2"></i> Users
            </a>
        </div>

        <div class="border-t border-slate-200 pt-4 dark:border-white/10">
            <form method="POST" action="{{ route('web.admin.logout') }}">
                @csrf
                <button type="submit" class="btn-pill btn-ghost w-full text-left">Sign out</button>
            </form>
        </div>
    </nav>
</aside>

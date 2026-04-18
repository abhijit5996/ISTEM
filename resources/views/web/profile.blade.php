@extends('layouts.main')

@section('content')
    <x-ui.page-header
        kicker="User Account"
        title="Profile"
        subtitle="Personal info, booking insights, queue tracking, and exports."
    >
        <a href="{{ route('web.profile.export-bookings') }}" class="btn-pill btn-ghost">Export Booking History (CSV)</a>
    </x-ui.page-header>

    <section class="grid grid-cols-1 gap-6 lg:grid-cols-[0.8fr_1.2fr]">
        <x-ui.card>
            <div class="flex items-center gap-4">
                <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-3xl bg-linear-to-br from-cyan-500 to-teal-500 text-3xl font-semibold text-white">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="truncate text-lg font-semibold">{{ $user->name }}</p>
                    <p class="truncate text-sm text-slate-500 dark:text-slate-400">{{ $user->email }}</p>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 p-3 dark:border-slate-700">
                    <p class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Phone</p>
                    <p class="mt-1 font-semibold">{{ $user->phone ?: 'N/A' }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-3 dark:border-slate-700">
                    <p class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Email Verified</p>
                    <p class="mt-1 font-semibold">{{ $user->email_verified ? 'Yes' : 'No' }}</p>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card title="Quick Access" subtitle="Navigate to related account sections.">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <a href="{{ route('web.dashboard') }}" class="rounded-2xl border border-slate-200 p-4 transition hover:border-cyan-400 dark:border-slate-700">
                    <p class="font-semibold">Dashboard</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Overview cards and recent activity.</p>
                </a>
                <a href="{{ route('web.my-bookings') }}" class="rounded-2xl border border-slate-200 p-4 transition hover:border-cyan-400 dark:border-slate-700">
                    <p class="font-semibold">Booking History</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">See approved, pending, and rejected bookings.</p>
                </a>
                <a href="{{ route('web.queue-status') }}" class="rounded-2xl border border-slate-200 p-4 transition hover:border-cyan-400 dark:border-slate-700">
                    <p class="font-semibold">Queue Tracking</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Monitor position and estimated wait.</p>
                </a>
                <a href="{{ route('web.favorites') }}" class="rounded-2xl border border-slate-200 p-4 transition hover:border-cyan-400 dark:border-slate-700">
                    <p class="font-semibold">Favorites</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">View saved instruments and book quickly.</p>
                </a>
            </div>
        </x-ui.card>
    </section>
@endsection

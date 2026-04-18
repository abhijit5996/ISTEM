@extends('layouts.main')

@section('content')
    <x-ui.page-header
        kicker="Administration"
        title="Admin Dashboard"
        subtitle="Overview of users, instruments, bookings, and operational activity."
    />

    <section class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-6">
        <x-ui.card class="metric-card"><p class="text-sm text-slate-500 dark:text-slate-400">Total Users</p><p class="mt-2 text-3xl font-semibold">{{ $users }}</p></x-ui.card>
        <x-ui.card class="metric-card"><p class="text-sm text-slate-500 dark:text-slate-400">Total Instruments</p><p class="mt-2 text-3xl font-semibold">{{ $instruments }}</p></x-ui.card>
        <x-ui.card class="metric-card"><p class="text-sm text-slate-500 dark:text-slate-400">Total Bookings</p><p class="mt-2 text-3xl font-semibold">{{ $bookings }}</p></x-ui.card>
        <x-ui.card class="metric-card"><p class="text-sm text-slate-500 dark:text-slate-400">Pending</p><p class="mt-2 text-3xl font-semibold">{{ $pending }}</p></x-ui.card>
        <x-ui.card class="metric-card"><p class="text-sm text-slate-500 dark:text-slate-400">Approved</p><p class="mt-2 text-3xl font-semibold">{{ $approved }}</p></x-ui.card>
        <x-ui.card class="metric-card"><p class="text-sm text-slate-500 dark:text-slate-400">Rejected</p><p class="mt-2 text-3xl font-semibold">{{ $rejected }}</p></x-ui.card>
    </section>

    <section class="panel">
        <div class="flex flex-wrap gap-2">
            <a class="btn-pill btn-primary" href="{{ route('web.admin.instruments') }}">Manage Instruments</a>
            <a class="btn-pill btn-primary" href="{{ route('web.admin.bookings') }}">Manage Bookings</a>
            <a class="btn-pill btn-primary" href="{{ route('web.admin.queue') }}">Manage Queue</a>
            <a class="btn-pill btn-primary" href="{{ route('web.admin.analytics') }}">Analytics</a>
            <a class="btn-pill btn-ghost" href="{{ route('web.admin.users') }}">User Management</a>
        </div>
    </section>
@endsection

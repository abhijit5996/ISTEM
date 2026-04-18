@extends('layouts.main')

@section('content')
    <x-ui.page-header
        kicker="Booking Submitted"
        title="Request Confirmed"
        subtitle="Your booking has been received and is pending approval workflow."
    />

    <x-ui.card class="text-center">
        <div class="mx-auto mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">
            <i data-lucide="check" class="h-8 w-8"></i>
        </div>
        <h3 class="text-2xl font-semibold">Booking Recorded</h3>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">You can track status, queue movement, and approval updates from your dashboard.</p>

        <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 p-3 dark:border-slate-700">
                <p class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Request ID</p>
                <p class="mt-1 font-semibold">{{ $booking->id }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 p-3 dark:border-slate-700">
                <p class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Instrument</p>
                <p class="mt-1 font-semibold">{{ $booking->instrument?->name }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 p-3 dark:border-slate-700">
                <p class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Status</p>
                <p class="mt-1"><span class="status-chip status-pending">{{ ucfirst($booking->status) }}</span></p>
            </div>
            <div class="rounded-2xl border border-slate-200 p-3 dark:border-slate-700">
                <p class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Date Range</p>
                <p class="mt-1 font-semibold">{{ $booking->start_date }} to {{ $booking->end_date }}</p>
            </div>
        </div>

        <div class="mt-5 flex flex-wrap justify-center gap-2">
            <a href="{{ route('web.my-bookings') }}" class="btn-pill btn-primary">View My Bookings</a>
            <a href="{{ route('web.dashboard') }}" class="btn-pill btn-ghost">Open Dashboard</a>
            <a href="{{ route('web.home') }}" class="btn-pill btn-ghost">Back to Instruments</a>
        </div>
    </x-ui.card>
@endsection

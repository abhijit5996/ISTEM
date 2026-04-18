@extends('layouts.main')

@section('content')
    <x-ui.page-header
        kicker="User Dashboard"
        title="Welcome, {{ session('web_user_name') }}"
        subtitle="Track booking performance, queue activity, and recent actions in one place."
    />

    <section class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
        <x-ui.card class="metric-card">
            <p class="text-sm text-slate-500 dark:text-slate-400">Total Bookings</p>
            <p class="mt-2 text-3xl font-semibold">{{ $totalBookings }}</p>
        </x-ui.card>
        <x-ui.card class="metric-card">
            <p class="text-sm text-slate-500 dark:text-slate-400">Active Bookings</p>
            <p class="mt-2 text-3xl font-semibold">{{ $activeBookings }}</p>
        </x-ui.card>
        <x-ui.card class="metric-card">
            <p class="text-sm text-slate-500 dark:text-slate-400">Rejected Bookings</p>
            <p class="mt-2 text-3xl font-semibold">{{ $rejectedBookings }}</p>
        </x-ui.card>
        <x-ui.card class="metric-card">
            <p class="text-sm text-slate-500 dark:text-slate-400">Queue Status</p>
            <p class="mt-2 text-3xl font-semibold">{{ $queueStatus }}</p>
        </x-ui.card>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <x-ui.card title="Recent Booking Activity" subtitle="Latest five booking requests.">
            <div class="space-y-2">
                @forelse($bookings->take(5) as $booking)
                    <div class="flex items-center justify-between rounded-2xl border border-slate-200 p-3 dark:border-slate-700">
                        <div>
                            <p class="font-semibold">{{ $booking->instrument?->name ?: $booking->instrument_id }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $booking->start_date }} to {{ $booking->end_date }}</p>
                        </div>
                        <span class="status-chip status-{{ strtolower($booking->status) }}">{{ ucfirst($booking->status) }}</span>
                    </div>
                @empty
                    <x-ui.empty-state title="No bookings yet" description="Submit a booking request to populate this activity feed." />
                @endforelse
            </div>
        </x-ui.card>

        <x-ui.card title="Queue Snapshot" subtitle="Recent queue entries and estimated wait.">
            <div class="space-y-3">
                @forelse($queueItems->take(5) as $item)
                    <div class="rounded-2xl border border-slate-200 p-3 dark:border-slate-700">
                        <div class="flex items-center justify-between gap-2">
                            <p class="font-semibold">{{ $item->instrument_id }}</p>
                            <span class="status-chip status-{{ strtolower($item->status) }}">{{ ucfirst($item->status) }}</span>
                        </div>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Position {{ $item->queue_position }} | ETA {{ max(1, (int) $item->queue_position * 5) }} mins</p>
                        <div class="mt-2">
                            <x-ui.progress-bar :value="max(5, 100 - ((int) $item->queue_position * 10))" label="Queue" />
                        </div>
                    </div>
                @empty
                    <x-ui.empty-state title="No queue items" description="Join queue from an instrument details page." />
                @endforelse
            </div>
        </x-ui.card>
    </section>
@endsection

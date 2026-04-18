@extends('layouts.main')

@section('content')
    <x-ui.page-header
        kicker="Administration"
        title="Analytics"
        subtitle="Operational metrics for booking status and demand trends."
    />

    <section class="ui-grid-4">
        @foreach($bookingsByStatus as $status => $total)
            <x-ui.card class="metric-card">
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ ucfirst($status) }}</p>
                <p class="mt-2 text-3xl font-semibold">{{ $total }}</p>
            </x-ui.card>
        @endforeach
    </section>

    <section class="ui-grid-2">
        <x-ui.card title="Status Distribution" subtitle="Share of all bookings by status">
            <div class="space-y-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800/40">
                @forelse($bookingsByStatus as $status => $total)
                    @php($percent = $totalBookings > 0 ? round(($total / $totalBookings) * 100, 1) : 0)
                    <div>
                        <div class="mb-1 flex items-center justify-between text-sm">
                            <span class="font-medium text-slate-700 dark:text-slate-200">{{ ucfirst($status) }}</span>
                            <span class="text-slate-500 dark:text-slate-400">{{ $total }} ({{ $percent }}%)</span>
                        </div>
                        <div class="h-2.5 w-full rounded-full bg-slate-200 dark:bg-slate-700">
                            <div class="h-2.5 rounded-full bg-linear-to-r from-cyan-500 to-teal-500" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">No booking data yet.</p>
                @endforelse
            </div>
        </x-ui.card>

        <x-ui.card title="Booking Trend" subtitle="Monthly bookings over the last 6 months">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800/40">
                <div class="grid grid-cols-6 items-end gap-2">
                    @foreach($monthlyTrend as $point)
                        @php($height = (int) round(($point['total'] / $trendPeak) * 100))
                        <div class="flex flex-col items-center gap-2">
                            <div class="w-full rounded-t-lg bg-linear-to-t from-cyan-500 to-teal-400" style="height: {{ max($height, 6) }}px"></div>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400">{{ $point['label'] }}</span>
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-200">{{ $point['total'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </x-ui.card>
    </section>

    <x-ui.table :headers="['Instrument', 'Total Bookings']" class="mt-6">
        @forelse($bookingsByInstrument as $row)
            <tr>
                <td>{{ $row->instrument?->name ?: $row->instrument_id }}</td>
                <td>{{ $row->total }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="2">
                    <x-ui.empty-state title="No booking data yet" description="Booking analytics will appear once transactions exist." />
                </td>
            </tr>
        @endforelse
    </x-ui.table>
@endsection

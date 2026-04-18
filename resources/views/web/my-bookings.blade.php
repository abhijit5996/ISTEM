@extends('layouts.main')

@section('content')
    <x-ui.page-header
        kicker="User Activity"
        title="Booking History"
        subtitle="Track submitted requests, approval status, and timeline."
    />

    <x-ui.table :headers="['ID', 'Instrument', 'From', 'To', 'Status']">
        @forelse($bookings as $booking)
            <tr>
                <td>{{ $booking->id }}</td>
                <td class="font-semibold">{{ $booking->instrument?->name ?: $booking->instrument_id }}</td>
                <td>{{ $booking->start_date }}</td>
                <td>{{ $booking->end_date }}</td>
                <td>
                    <span class="status-chip status-{{ strtolower($booking->status) }}">{{ ucfirst($booking->status) }}</span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5">
                    <x-ui.empty-state title="No bookings found" description="Submit a booking request to populate this section." />
                </td>
            </tr>
        @endforelse
    </x-ui.table>

    @if($bookings->hasPages())
        <div class="mt-4 flex justify-center">
            {{ $bookings->links() }}
        </div>
    @endif
@endsection

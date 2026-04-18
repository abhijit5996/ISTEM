@extends('layouts.main')

@section('content')
    <x-ui.page-header
        kicker="Administration"
        title="Booking Management"
        subtitle="Approve, reject, and annotate booking requests."
    >
        <a href="{{ route('web.admin.bookings.export') }}" class="btn-pill btn-ghost">Export CSV</a>
    </x-ui.page-header>

    <x-ui.table :headers="['ID', 'User', 'Instrument', 'Time Slot', 'Status', 'Actions']" class="mt-6">
        @foreach($bookings as $booking)
            <tr>
                <td>{{ $booking->id }}</td>
                <td>
                    <p class="font-semibold">{{ $booking->name }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $booking->email }}</p>
                </td>
                <td>{{ $booking->instrument?->name ?: $booking->instrument_id }}</td>
                <td>{{ $booking->start_date }} to {{ $booking->end_date }}</td>
                <td><span class="status-chip status-{{ strtolower($booking->status) }}">{{ ucfirst($booking->status) }}</span></td>
                <td>
                    <div class="space-y-3">
                        @if($booking->status === 'pending')
                            <form method="POST" action="{{ route('web.admin.bookings.approve', $booking->id) }}" class="space-y-2">
                                @csrf
                                <input type="text" name="admin_comment" class="input-ui" placeholder="Approval notes (optional)">
                                <button class="btn-pill btn-primary" type="submit">Approve</button>
                            </form>

                            <form method="POST" action="{{ route('web.admin.bookings.reject', $booking->id) }}" class="space-y-2">
                                @csrf
                                <input type="text" name="rejection_reason" class="input-ui" placeholder="Reject reason" required>
                                <input type="text" name="admin_comment" class="input-ui" placeholder="Admin comments (optional)">
                                <button class="btn-pill btn-warn" type="submit">Reject</button>
                            </form>
                        @else
                            @if($booking->rejection_reason)
                                <p class="text-xs text-rose-600"><span class="font-semibold">Reason:</span> {{ $booking->rejection_reason }}</p>
                            @endif
                            @if($booking->admin_comment)
                                <p class="text-xs text-slate-600 dark:text-slate-300"><span class="font-semibold">Admin Note:</span> {{ $booking->admin_comment }}</p>
                            @endif
                            @if(!$booking->rejection_reason && !$booking->admin_comment)
                                <p class="text-xs text-slate-500 dark:text-slate-400">No admin notes available.</p>
                            @endif
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </x-ui.table>

    @if($bookings->hasPages())
        <div class="mt-6 flex justify-center">
            {{ $bookings->links() }}
        </div>
    @endif
@endsection

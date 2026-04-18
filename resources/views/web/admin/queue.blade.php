@extends('layouts.main')

@section('content')
    <x-ui.page-header
        kicker="Administration"
        title="Queue Management"
        subtitle="Handle queue approvals and rejections for instrument access."
    />

    <x-ui.table :headers="['ID', 'Instrument', 'User', 'Position', 'Slot', 'Status', 'Actions']">
        @foreach($queueItems as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->instrument?->name ?: $item->instrument_id }}</td>
                <td>
                    <p class="font-semibold">{{ $item->user_name }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $item->email }}</p>
                </td>
                <td>{{ $item->queue_position }}</td>
                <td>{{ $item->date }} {{ $item->time_slot }}</td>
                <td><span class="status-chip status-{{ strtolower($item->status) }}">{{ ucfirst($item->status) }}</span></td>
                <td>
                    <div class="flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('web.admin.queue.approve') }}">
                            @csrf
                            <input type="hidden" name="id" value="{{ $item->id }}">
                            <button class="btn-pill btn-primary" type="submit">Approve</button>
                        </form>
                        <form method="POST" action="{{ route('web.admin.queue.reject') }}">
                            @csrf
                            <input type="hidden" name="id" value="{{ $item->id }}">
                            <button class="btn-pill btn-warn" type="submit">Reject</button>
                        </form>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-ui.table>

    @if($queueItems->hasPages())
        <div class="mt-4 flex justify-center">
            {{ $queueItems->links() }}
        </div>
    @endif
@endsection

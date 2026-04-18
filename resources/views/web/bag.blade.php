@extends('layouts.main')

@section('content')
    <x-ui.page-header
        kicker="Bag / Cart"
        title="Booking Bag"
        subtitle="Review selected instruments, adjust items, and proceed to booking form."
    />

    @if(count($bag) === 0)
        <x-ui.empty-state title="Your bag is empty" description="Add instruments from the listing to continue.">
            <a class="btn-pill btn-primary" href="{{ route('web.home') }}">Browse Instruments</a>
        </x-ui.empty-state>
    @else
        <x-ui.table :headers="['Instrument', 'From', 'To', 'Actions']" class="mt-2">
            @foreach($bag as $item)
                <tr>
                    <td class="font-semibold">{{ $item['instrument_name'] }}</td>
                    <td>{{ $item['start_date'] }}</td>
                    <td>{{ $item['end_date'] }}</td>
                    <td>
                        <form method="POST" action="{{ route('web.bag.remove', $item['instrument_id']) }}" data-ajax-bag-remove="{{ $item['instrument_id'] }}">
                            @csrf
                            <button class="btn-pill btn-warn" type="submit">Remove</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </x-ui.table>

        <div class="panel mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ count($bag) }} item(s) selected</p>
            <div class="flex flex-wrap gap-2">
                @if(session('web_user_id'))
                    <a href="{{ route('web.booking.form') }}" class="btn-pill btn-primary">Proceed to Booking</a>
                @else
                    <a href="{{ route('web.login') }}" class="btn-pill btn-primary">Login to Continue</a>
                @endif
                <a class="btn-pill btn-ghost" href="{{ route('web.home') }}">Add More</a>
            </div>
        </div>
    @endif
@endsection

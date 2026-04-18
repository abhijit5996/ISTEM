@extends('layouts.main')

@section('breadcrumbs')
    <x-ui.breadcrumbs :items="[
        ['label' => 'Home', 'url' => route('web.home')],
        ['label' => 'Instruments', 'url' => route('web.instruments')],
        ['label' => trim(preg_replace('/\s{2,}/', ' ', preg_replace('/\s*#?\d+$/', '', $instrument->name ?? 'Instrument')))]
    ]" />
@endsection

@section('content')
    @php
        $displayName = trim(preg_replace('/\s{2,}/', ' ', preg_replace('/\s*#?\d+$/', '', $instrument->name ?? 'Instrument')));
        $isAvailable = $instrument->is_available;
        $statusClass = $isAvailable ? 'status-available' : 'status-booked';
        $statusText = $isAvailable ? 'Available' : 'Booked';
        $displayPrice = number_format((float) ($instrument->usage_cost ?? 0), 2);
    @endphp

    <x-ui.page-header
        kicker="Instrument Details"
        title="{{ $displayName }}"
        subtitle="{{ $instrument->description ?: 'No detailed description available for this instrument.' }}"
    >
        <a href="{{ route('web.home') }}" class="btn-pill btn-ghost">Back to Listing</a>
    </x-ui.page-header>

    <section class="grid grid-cols-1 gap-4 xl:grid-cols-[1.2fr_0.8fr]">
        <x-ui.card>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <img src="{{ $instrument->image_url ?? asset('frontend/assets/hero-lab-DpylzpE1.jpg') }}" alt="{{ $instrument->name }}" class="h-72 w-full rounded-2xl object-cover sm:col-span-2">
                <div class="space-y-3">
                    <img src="{{ $instrument->image_url ?? asset('frontend/assets/hero-lab-DpylzpE1.jpg') }}" alt="Gallery image" class="h-[8.4rem] w-full rounded-2xl object-cover">
                    <img src="{{ asset('frontend/assets/hero-lab-DpylzpE1.jpg') }}" alt="Gallery image" class="h-[8.4rem] w-full rounded-2xl object-cover">
                </div>
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-2">
                <span class="status-chip {{ $statusClass }}">{{ $statusText }}</span>
                <span class="status-chip status-pending">Category: {{ $instrument->category ?: 'General' }}</span>
                <span class="status-chip status-pending">Location: {{ $instrument->location ?: 'Main Lab' }}</span>
                <span class="status-chip status-pending">Status: {{ ucfirst($instrument->status ?: 'available') }}</span>
                <span class="status-chip status-approved">Pricing: ${{ $displayPrice }} / day</span>
                <a href="{{ route('web.instrument.guidelines', $instrument->id) }}" class="btn-pill btn-ghost">Download Usage Guidelines</a>
            </div>
        </x-ui.card>

        <div class="space-y-4">
            <x-ui.card title="Book Now">
                <form method="POST" action="{{ route('web.bag.add', $instrument->id) }}" class="grid grid-cols-1 gap-3">
                    @csrf
                    <x-ui.date-picker label="From Date" name="start_date" required />
                    <x-ui.date-picker label="To Date" name="end_date" required />
                    <button class="btn-pill btn-primary" type="submit">Add to Bag</button>
                </form>
            </x-ui.card>

            <x-ui.card title="Join Queue" subtitle="Queue submission uses existing backend logic.">
                @if(session('web_user_id'))
                    <form method="POST" action="{{ route('web.queue.join', $instrument->id) }}" class="grid grid-cols-1 gap-3">
                        @csrf
                        <x-ui.date-picker label="From Date" name="start_date" required />
                        <x-ui.date-picker label="To Date" name="end_date" required />
                        <button class="btn-pill btn-ghost" type="submit">Join Queue</button>
                    </form>
                @else
                    <p class="text-sm text-slate-500 dark:text-slate-400">Please <a href="{{ route('web.login') }}" class="font-semibold text-cyan-700 dark:text-cyan-300">login</a> to join queue.</p>
                @endif

                <div class="mt-3 flex flex-wrap gap-2">
                    <a href="{{ route('web.bag') }}" class="btn-pill btn-primary">Book Now</a>
                    @if(session('web_user_id'))
                        <form method="POST" action="{{ route('web.favorites.add', $instrument->id) }}">
                            @csrf
                            <button type="submit" class="btn-pill btn-ghost">Add to Favorites</button>
                        </form>
                    @endif
                </div>
            </x-ui.card>
        </div>
    </section>

    <section class="panel">
        <x-ui.section-heading
            kicker="Queue"
            title="Current Queue"
            subtitle="Top 10 queue entries for this instrument."
        />

        <div class="mt-4 space-y-3">
            @forelse($queue as $item)
                <div class="rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <p class="text-sm font-semibold">#{{ $item->queue_position }} {{ $item->user_name }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $item->email }} | {{ $item->date }} {{ $item->time_slot ? '(' . $item->time_slot . ')' : '' }}</p>
                        </div>
                        <span class="status-chip status-pending">{{ ucfirst($item->status) }}</span>
                    </div>
                    <div class="mt-3">
                        <x-ui.progress-bar :value="max(5, 100 - ((int) $item->queue_position * 8))" label="Queue Progress" />
                    </div>
                </div>
            @empty
                <x-ui.empty-state title="No queue entries yet" description="Users joining queue will appear here in realtime-friendly cards." />
            @endforelse
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <x-ui.card title="Admin Notes / Comments" subtitle="Operational guidance and review comments for this instrument.">
            <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-300">
                <li class="rounded-xl border border-slate-200 p-3 dark:border-slate-700">Follow safety onboarding checklist before usage.</li>
                <li class="rounded-xl border border-slate-200 p-3 dark:border-slate-700">Maintenance slot may block bookings during periodic calibration.</li>
                <li class="rounded-xl border border-slate-200 p-3 dark:border-slate-700">For premium modules, attach experiment objective in booking reason.</li>
            </ul>
        </x-ui.card>

        <x-ui.card title="Selected Duration" subtitle="Clear date range selection and total days.">
            <p class="text-sm text-slate-500 dark:text-slate-400">Once you select From and To dates, duration will be validated in booking flow.</p>
            <div class="mt-3 rounded-2xl border border-slate-200 p-4 text-sm dark:border-slate-700">
                <p><span class="font-semibold">From:</span> Choose start date</p>
                <p class="mt-1"><span class="font-semibold">To:</span> Choose end date</p>
                <p class="mt-2 text-cyan-700 dark:text-cyan-300">Unavailable dates are blocked by booking conflict validation.</p>
            </div>
        </x-ui.card>
    </section>

    <section class="panel">
        <x-ui.section-heading
            kicker="Related"
            title="Related Instruments"
            subtitle="Explore similar items from the same category."
        />

        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            @forelse($relatedInstruments as $related)
                <x-ui.instrument-card :instrument="$related" />
            @empty
                <div class="md:col-span-2 xl:col-span-4">
                    <x-ui.empty-state title="No related instruments" description="More items in this category will appear here." />
                </div>
            @endforelse
        </div>
    </section>

@endsection

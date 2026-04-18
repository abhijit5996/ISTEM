@props([
    'instrument',
])

@php
    $instrumentImage = $instrument->image_url ?? asset('frontend/assets/hero-lab-DpylzpE1.jpg');
    $statusLabel = $instrument->is_available ? 'Available' : 'Booked';
    $statusClass = $instrument->is_available ? 'status-available' : 'status-booked';
    $displayName = preg_replace('/\s*#?\d+$/', '', $instrument->name ?? 'Instrument');
    $displayName = trim(preg_replace('/\s{2,}/', ' ', $displayName));
@endphp

<article {{ $attributes->merge(['class' => 'panel group flex h-full flex-col']) }}>
    <div class="relative overflow-hidden rounded-2xl">
        <img src="{{ $instrumentImage }}" alt="{{ $instrument->name }}" class="h-48 w-full object-cover transition duration-500 group-hover:scale-105">
        <span class="status-chip {{ $statusClass }} absolute left-3 top-3">{{ $statusLabel }}</span>
        <span class="absolute right-3 top-3 rounded-full bg-slate-900/70 px-3 py-1 text-xs font-semibold text-white">{{ $instrument->category ?: 'General' }}</span>
    </div>

    <div class="mt-4 flex flex-1 flex-col">
        <div class="mb-3 flex items-start justify-between gap-3">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">{{ $displayName }}</h3>
            @if(session('web_user_id'))
                <form method="POST" action="{{ route('web.favorites.add', $instrument->id) }}">
                    @csrf
                    <button type="submit" class="icon-btn" title="Add to favorites">
                        <i data-lucide="heart" class="h-4 w-4"></i>
                    </button>
                </form>
            @endif
        </div>

        <p class="text-sm text-slate-500 dark:text-slate-400">{{ \Illuminate\Support\Str::limit($instrument->description ?: 'High precision laboratory instrument available for institutional booking.', 92) }}</p>
        <p class="mt-2 text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Location: {{ $instrument->location ?: 'Main Lab' }}</p>

        <div class="mt-4 flex flex-wrap gap-2">
            <a class="btn-pill btn-primary" href="{{ route('web.instrument', $instrument->id) }}">Book Now</a>
            <button type="button" class="btn-pill btn-ghost" data-open-modal="add-to-bag-{{ $instrument->id }}">Add to Bag</button>
        </div>
    </div>
</article>

<x-ui.modal id="add-to-bag-{{ $instrument->id }}" title="Add {{ $displayName }} to Bag">
    <form method="POST" action="{{ route('web.bag.add', $instrument->id) }}" class="grid grid-cols-1 gap-3 sm:grid-cols-2">
        @csrf
        <x-ui.date-picker label="From Date" name="start_date" required />
        <x-ui.date-picker label="To Date" name="end_date" required />
        <div class="sm:col-span-2 flex justify-end gap-2">
            <button type="button" class="btn-pill btn-ghost" data-close-modal>Cancel</button>
            <button type="submit" class="btn-pill btn-primary">Add to Bag</button>
        </div>
    </form>
</x-ui.modal>
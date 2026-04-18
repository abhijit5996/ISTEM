@props([
    'instrument',
])

@php
    $instrumentImage = $instrument->image_url ?? asset('frontend/assets/hero-lab-DpylzpE1.jpg');
    $statusLabel = $instrument->is_available ? 'Available' : 'Booked';
    $statusClass = $instrument->is_available ? 'status-available' : 'status-booked';
    $displayName = preg_replace('/\s*#?\d+$/', '', $instrument->name ?? 'Instrument');
    $displayName = trim(preg_replace('/\s{2,}/', ' ', $displayName));
    $displayPrice = '₹' . number_format((float) ($instrument->usage_cost ?? 0), 2);
@endphp

<article {{ $attributes->merge(['class' => 'panel group mx-auto flex h-full w-full max-w-sm flex-col p-4']) }}>
    <div class="relative aspect-video max-h-52 overflow-hidden rounded-2xl">
        <img src="{{ $instrumentImage }}" alt="{{ $instrument->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
        <span class="status-chip {{ $statusClass }} absolute left-3 top-3">{{ $statusLabel }}</span>
        <span class="absolute right-3 top-3 rounded-full bg-slate-900/70 px-2 py-1 text-[11px] font-semibold text-white">{{ $instrument->category ?: 'General' }}</span>
    </div>

    <div class="mt-3 flex flex-1 flex-col">
        <div class="mb-2 flex items-start justify-between gap-2">
            <h3 class="line-clamp-2 text-base font-semibold leading-6 text-slate-900 dark:text-slate-100">{{ $displayName }}</h3>
            @if(session('web_user_id'))
                <form method="POST" action="{{ route('web.favorites.add', $instrument->id) }}" data-ajax-favorite="{{ $instrument->id }}">
                    @csrf
                    <button type="submit" class="icon-btn h-8 w-8" title="Add to favorites" data-favorite-button>
                        <i data-lucide="heart" class="h-3.5 w-3.5"></i>
                    </button>
                </form>
            @endif
        </div>

        <p class="text-sm text-slate-500 dark:text-slate-400">{{ \Illuminate\Support\Str::limit($instrument->description ?: 'High precision laboratory instrument available for institutional booking.', 72) }}</p>

        <div class="mt-2 flex items-center justify-between gap-2">
            <p class="text-base font-semibold text-teal-700">{{ $displayPrice }}</p>
            <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">{{ $instrument->location ?: 'Main Lab' }}</p>
        </div>

        <div class="mt-3 flex flex-wrap gap-2">
            <a class="btn-pill btn-primary h-9 px-3 text-xs" href="{{ route('web.instrument', $instrument->id) }}">Book</a>
            <button type="button" class="btn-pill btn-ghost h-9 px-3 text-xs" data-open-modal="add-to-bag-{{ $instrument->id }}">Add</button>
        </div>
    </div>
</article>

<x-ui.modal id="add-to-bag-{{ $instrument->id }}" title="Add {{ $displayName }} to Bag">
    <form method="POST" action="{{ route('web.bag.add', $instrument->id) }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2" data-ajax-bag-add="{{ $instrument->id }}">
        @csrf
        <x-ui.date-picker label="From Date" name="start_date" required />
        <x-ui.date-picker label="To Date" name="end_date" required />
        <div class="sm:col-span-2 flex flex-wrap justify-end gap-2">
            <button type="button" class="btn-pill btn-ghost" data-close-modal>Cancel</button>
            <button type="submit" class="btn-pill btn-primary">Add to Bag</button>
        </div>
    </form>
</x-ui.modal>
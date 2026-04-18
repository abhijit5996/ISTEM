@extends('layouts.main')

@section('content')
    <x-ui.page-header
        kicker="Wishlist"
        title="Favorites"
        subtitle="Saved instruments for quick booking. This can later be backed by persistent user-level storage."
    />

    <section class="grid grid-cols-1 gap-4 md:grid-cols-2 2xl:grid-cols-3">
        @forelse($favorites as $instrument)
            <x-ui.card>
                <img src="{{ $instrument->image_url ?? asset('frontend/assets/hero-lab-DpylzpE1.jpg') }}" alt="{{ $instrument->name }}" class="h-44 w-full rounded-2xl object-cover">
                <div class="mt-3">
                    <p class="text-lg font-semibold">{{ trim(preg_replace('/\s{2,}/', ' ', preg_replace('/\s*#?\d+$/', '', $instrument->name ?? 'Instrument'))) }}</p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ \Illuminate\Support\Str::limit($instrument->description ?: 'Instrument available for booking.', 90) }}</p>
                    <p class="mt-2 text-xs font-semibold uppercase tracking-[0.12em] text-slate-500 dark:text-slate-400">Location: {{ $instrument->location ?: 'Main Lab' }}</p>
                </div>
                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('web.instrument', $instrument->id) }}" class="btn-pill btn-primary">Book Now</a>
                    <form method="POST" action="{{ route('web.favorites.remove', $instrument->id) }}">
                        @csrf
                        <button class="btn-pill btn-warn" type="submit">Remove</button>
                    </form>
                </div>
            </x-ui.card>
        @empty
            <div class="md:col-span-2 2xl:col-span-3">
                <x-ui.empty-state title="No favorites yet" description="Use the heart action on instrument cards to save items here.">
                    <a href="{{ route('web.home') }}" class="btn-pill btn-primary">Browse Instruments</a>
                </x-ui.empty-state>
            </div>
        @endforelse
    </section>

    @if($favorites->hasPages())
        <div class="mt-5 flex justify-center">
            {{ $favorites->links() }}
        </div>
    @endif
@endsection

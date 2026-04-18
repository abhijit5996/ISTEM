@extends('layouts.main')

@section('content')
    @php
        $heroInstrument = $instruments->first();
        $featuredInstruments = $instruments;

        $promoBanners = [
            [
                'title' => 'Microscopy Lab',
                'price' => '199.00 / day',
                'label' => 'Start From',
                'cta' => 'Book Now',
                'url' => route('web.home') . '#popular-product',
                'image' => asset('frontend/assets/hero-lab-DpylzpE1.jpg'),
                'alt' => 'Laboratory instrument setup',
                'variant' => 'mobile',
            ],
            [
                'title' => 'Spectroscopy Unit',
                'price' => '99.00 / day',
                'label' => 'Start From',
                'cta' => 'View Bag',
                'url' => route('web.bag'),
                'image' => asset('frontend/assets/hero-lab-DpylzpE1.jpg'),
                'alt' => 'Research lab equipment',
                'variant' => 'watch',
            ],
        ];
    @endphp

    @include('web.partials.home-hero', ['heroInstrument' => $heroInstrument])

    <section class="section-block pb-2">
        <div class="panel space-y-4">
            <form method="GET" action="{{ route('web.instruments') }}" class="grid grid-cols-1 gap-3 md:grid-cols-[1fr_0.6fr_0.5fr_auto]">
                <input type="search" name="search" class="input-ui" value="{{ $filters['search'] ?? '' }}" placeholder="Search instruments, categories, locations">
                <select name="location" class="select-ui">
                    <option value="">All Locations</option>
                    @foreach($locations as $location)
                        <option value="{{ $location }}" @selected(($filters['location'] ?? '') === $location)>{{ $location }}</option>
                    @endforeach
                </select>
                <select name="availability" class="select-ui">
                    <option value="all">Any Availability</option>
                    <option value="available" @selected(($filters['availability'] ?? '') === 'available')>Available</option>
                    <option value="booked" @selected(($filters['availability'] ?? '') === 'booked')>Booked</option>
                </select>
                <button type="submit" class="btn-pill btn-primary">Find Instruments</button>
            </form>

            <div class="overflow-x-auto pb-1">
                <div class="flex min-w-max items-center gap-2">
                    <a href="{{ route('web.instruments') }}" class="btn-pill btn-ghost">All Categories</a>
                    @foreach($categories as $category)
                        <a href="{{ route('web.category', $category) }}" class="btn-pill btn-ghost">{{ $category }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="section-block py-2 sm:py-4">
        <div class="collection-panel">
            <div class="section-title-wrap">
                <p class="section-kicker">Popular Product</p>
                <h2 class="section-title">Available Instruments</h2>
            </div>

            <div id="popular-product" class="product-grid">
                @forelse($featuredInstruments as $instrument)
                    @include('web.partials.product-card', ['instrument' => $instrument, 'featured' => $loop->first])
                @empty
                    <div class="xl:col-span-4 rounded-3xl border border-dashed border-slate-300 bg-white/80 p-8 text-center text-slate-600 dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-300">
                        No instruments available right now. Please check again later.
                    </div>
                @endforelse
            </div>

            @if($instruments->hasPages())
                <div class="mt-6 flex justify-center">
                    {{ $instruments->links() }}
                </div>
            @endif

            <div class="promo-grid">
                @foreach($promoBanners as $banner)
                    @include('web.partials.promo-banner', ['banner' => $banner])
                @endforeach
            </div>
        </div>
    </section>

    <section id="services-heading" class="services-heading">
        <div class="section-title-wrap text-left sm:text-center">
            <p class="section-kicker">Next Section</p>
            <h2 class="section-title">Our Services</h2>
        </div>
    </section>
@endsection

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

        $services = [
            [
                'icon' => 'calendar-check-2',
                'title' => 'Easy Instrument Booking',
                'description' => 'Book advanced laboratory instruments in just a few clicks with real-time availability and a smooth approval process.',
            ],
            [
                'icon' => 'clock-3',
                'title' => 'Live Availability Tracking',
                'description' => 'Check instrument availability instantly and plan your experiments without delays or scheduling conflicts.',
            ],
            [
                'icon' => 'users-round',
                'title' => 'Automated Queue Management',
                'description' => 'Join a smart queue when instruments are busy and get automatically allocated once they become available.',
            ],
            [
                'icon' => 'map-pin',
                'title' => 'Multi-Location Lab Access',
                'description' => 'Explore and book instruments across multiple labs and locations from a single unified platform.',
            ],
            [
                'icon' => 'book-open-check',
                'title' => 'Guidelines & Expert Support',
                'description' => 'Access instrument usage manuals and get support to ensure safe and efficient operation.',
            ],
            [
                'icon' => 'chart-column-increasing',
                'title' => 'Insights & Analytics',
                'description' => 'Track usage trends, booking history, and performance insights to optimize lab resource utilization.',
            ],
        ];
    @endphp

    @include('web.partials.home-hero', ['heroInstrument' => $heroInstrument])

    <section class="section-block py-4 sm:py-6">
        <div class="collection-panel">
            <div class="section-title-wrap">
                <p class="section-kicker">Popular Product</p>
                <h2 class="section-title">Available Instruments</h2>
            </div>

            <div id="popular-product" class="product-grid">
                @forelse($featuredInstruments as $instrument)
                    @include('web.partials.product-card', ['instrument' => $instrument, 'featured' => $loop->first])
                @empty
                    <div class="sm:col-span-2 md:col-span-3 lg:col-span-4 rounded-3xl border border-dashed border-slate-300 bg-white/80 p-8 text-center text-slate-600 dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-300">
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
        <div class="services-panel">
            <div class="section-title-wrap">
                <p class="section-kicker">Our Services</p>
                <h2 class="section-title">Our Services</h2>
                <p class="services-subtitle">
                    Empowering research and innovation with seamless access to advanced laboratory instruments and smart booking solutions.
                </p>
            </div>

            <div class="services-grid">
                @foreach($services as $service)
                    <article class="service-card">
                        <div class="service-icon-shell" aria-hidden="true">
                            <i data-lucide="{{ $service['icon'] }}" class="h-5 w-5"></i>
                        </div>
                        <h3 class="service-title">{{ $service['title'] }}</h3>
                        <p class="service-description">{{ $service['description'] }}</p>
                    </article>
                @endforeach
            </div>

            <div class="services-cta">
                <p class="services-cta-text">Ready to streamline your lab operations?</p>
                <a href="{{ route('web.home') }}#popular-product" class="btn-pill btn-primary">
                    Explore Instruments
                </a>
            </div>
        </div>
    </section>
@endsection

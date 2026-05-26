@extends('layouts.main')

@section('content')
    @php
        $heroInstrument = $instruments->first();
        $featuredInstruments = $instruments;
        $ecosystemStats = [
            ['label' => 'Instruments', 'value' => '150+'],
            ['label' => 'Labs Connected', 'value' => '12'],
            ['label' => 'Research Requests', 'value' => '24/7'],
        ];

        $innovationSteps = [
            ['step' => '01', 'title' => 'Discover', 'description' => 'Browse live instrument availability with an institutional-grade discovery flow.'],
            ['step' => '02', 'title' => 'Reserve', 'description' => 'Submit bookings with the existing workflow and keep your research schedule aligned.'],
            ['step' => '03', 'title' => 'Execute', 'description' => 'Track approvals, queue status, and usage handoffs through one seamless interface.'],
        ];

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

        $researchSignals = ['AI-enabled labs', 'Premium booking UX', 'Live queue visibility', 'Enterprise-grade workflow', 'Research-first design'];
    @endphp

    @include('web.partials.institutional-hero', ['heroInstrument' => $heroInstrument, 'ecosystemStats' => $ecosystemStats])

    <section class="section-block pt-0" data-reveal>
        <div class="insight-panel">
            <div class="insight-grid">
                @foreach($ecosystemStats as $stat)
                    <article class="metric-card metric-card-compact">
                        <p class="metric-label">{{ $stat['label'] }}</p>
                        <p class="metric-value">{{ $stat['value'] }}</p>
                    </article>
                @endforeach
            </div>

            <div class="logo-marquee" aria-label="Institutional highlights">
                <div class="logo-marquee-track">
                    @foreach($researchSignals as $signal)
                        <span class="logo-marquee-chip">{{ $signal }}</span>
                    @endforeach
                    @foreach($researchSignals as $signal)
                        <span class="logo-marquee-chip" aria-hidden="true">{{ $signal }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- About the Centre --}}
    <section class="section-block py-8 sm:py-10" data-reveal>
        <div class="collection-panel">
            <div class="section-title-wrap">
                <p class="section-kicker">About</p>
                <h2 class="section-title">Biomaterials Laboratory (BL)</h2>
                <p class="section-subtitle">Since its inception in 2024, the Biomaterials Laboratory (BL) has been creating cost-effective, translational technologies addressing diagnostics, regenerative therapies, therapeutic delivery, food safety, and agricultural challenges by exploring Indian biodiversity and sustainable materials.</p>
            </div>

            <div class="mt-6 grid gap-6 md:grid-cols-2">
                <div class="insight-panel">
                    <p class="text-sm leading-7 text-slate-600 dark:text-slate-400">Biomaterials Laboratory emphasizes excellence in research with translational impact, fosters innovation, continuous inquiry, and upholds societal and scientific accountability. We focus on sustainable technologies, regenerative therapies, material toxicity analysis at nano–micro scales, and food safety.</p>
                    <div class="mt-6 flex items-center gap-3">
                        <img src="{{ asset('frontend/assets/serb-logo.svg') }}" alt="SERB logo" class="h-10 w-auto">
                        <img src="{{ asset('frontend/assets/centre-logo.svg') }}" alt="Centre logo" class="h-10 w-auto">
                    </div>
                </div>

                <div class="insight-panel">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">Mission</p>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">Fostering an inclusive, ethical, and interdisciplinary research environment that prioritizes equity, diversity, and shared growth by preparing and inspiring students and intellectuals through exploration of nature and evolution in materials, sustainable technologies, regenerative therapies, material toxicity and food safety.</p>

                    <p class="mt-4 text-sm font-semibold text-slate-900 dark:text-white">Vision</p>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">Biomaterials Laboratory strives to be the hub of translational interdisciplinary excellence in thematic fields like smart & diagnostic, therapeutic safety, agriculture, and food technologies.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Publications & Book Chapters --}}
    <section class="section-block py-8 sm:py-10" data-reveal>
        <div class="innovation-panel">
            <div class="section-title-wrap">
                <p class="section-kicker">Research</p>
                <h2 class="section-title">Publications & Book Chapters</h2>
                <p class="section-subtitle">Selected peer-reviewed publications and authored book chapters from the centre.</p>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-2">
                <article class="insight-panel">
                    <p class="font-semibold">Chen J, Wang Y, Yang I, Li B, Kundu B, Kundu SC, Telas RL, Lin K, Li L.</p>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">Silkworm-gut-processed TCF 53 and BMP 2 from biomimetic calcium phosphate matrices. Journal of Nanobiotechnology (2024) 22:453</p>
                </article>

                <article class="insight-panel">
                    <p class="font-semibold">Kundu B, Branesto V, Telas RL</p>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">Hydroxyapatite alters bone metastatic tropism of cancer cells in engineered pre-metastatic niche. Under Review (2025)</p>
                </article>
            </div>

            <div class="mt-6">
                <p class="font-semibold">Book Chapters</p>
                <ul class="mt-3 list-inside list-disc text-sm text-slate-600 dark:text-slate-400">
                    <li>Importance of sustainability in packaging</li>
                    <li>Silk fibroin coaxialism and protein-based nanocarriers for therapeutic delivery</li>
                    <li>Exosomes in bone homeostasis, repair and regeneration</li>
                    <li>Public Perception of Artificial Intelligence: A Questionnaire Study</li>
                </ul>
            </div>
        </div>
    </section>

    {{-- Projects & Awards --}}
    <section class="section-block py-8 sm:py-10" data-reveal>
        <div class="collection-panel">
            <div class="section-title-wrap">
                <p class="section-kicker">Projects & Recognition</p>
                <h2 class="section-title">Selected Projects & Awards</h2>
                <p class="section-subtitle">Key projects, fundings and recognitions from the Biomaterials Laboratory.</p>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-2">
                <article class="insight-panel">
                    <p class="font-semibold">Project: BioSHIELD</p>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">Biocompatible Shield that Extends the Shelf-life of Fruits. Funding: Seed Fund – Adamas University. Reference: IAU/REG/NOT/2026/02/0011. Date: 11.02.2025</p>
                </article>

                <article class="insight-panel">
                    <p class="font-semibold">Awards & Recognition</p>
                    <ul class="mt-2 list-inside list-disc text-sm text-slate-600 dark:text-slate-400">
                        <li>Stanford University’s Top 2% Scientists (2024)</li>
                        <li>IIT Indore Invited Talk (April 2025)</li>
                        <li>Associate Editor — Frontiers in Bioengineering and Biotechnology</li>
                    </ul>
                </article>
            </div>
        </div>
    </section>

    {{-- Research Areas --}}
    <section class="section-block py-8 sm:py-10" data-reveal>
        <div class="innovation-panel">
            <div class="section-title-wrap">
                <p class="section-kicker">Focus Areas</p>
                <h2 class="section-title">Research Areas</h2>
                <p class="section-subtitle">Key thematic areas where BL conducts research and innovation.</p>
            </div>

            @php
                $researchAreas = ['Biomaterials','Regenerative Therapies','Therapeutic Delivery','Food Technology','Agricultural Innovation','Nano Toxicity','Sustainable Technologies','Translational Healthcare','Smart Diagnostics'];
            @endphp

            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($researchAreas as $area)
                    <article class="insight-panel">
                        <p class="font-semibold">{{ $area }}</p>
                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">Research, prototyping and translational activities in {{ $area }} to drive sustainable and clinically relevant innovations.</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section-block py-8 sm:py-10" data-reveal>
        <div class="collection-panel">
            <div class="section-title-wrap">
                <p class="section-kicker">Featured inventory</p>
                <h2 class="section-title">Available Instruments</h2>
                <p class="section-subtitle">A curated view of live lab assets with premium visual hierarchy and unchanged booking behavior.</p>
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

    <section class="section-block py-8 sm:py-10" data-reveal>
        <div class="innovation-panel">
            <div class="section-title-wrap text-left lg:max-w-xl">
                <p class="section-kicker">Research workflow</p>
                <h2 class="section-title">Designed for fast, confident lab execution</h2>
                <p class="section-subtitle">A modern workflow layer that keeps discovery, scheduling, approval, and queue handoffs visually clear without altering any backend logic.</p>
            </div>

            <div class="timeline-grid">
                @foreach($innovationSteps as $step)
                    <article class="timeline-card">
                        <span class="timeline-step">{{ $step['step'] }}</span>
                        <h3 class="timeline-title">{{ $step['title'] }}</h3>
                        <p class="timeline-description">{{ $step['description'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section id="services-heading" class="services-heading" data-reveal>
        <div class="services-panel">
            <div class="section-title-wrap">
                <p class="section-kicker">Platform capabilities</p>
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
                <a href="{{ route('web.home') }}#popular-product" class="btn-primary" aria-label="Explore Instruments">
                    Explore Instruments
                </a>
            </div>
        </div>
    </section>
@endsection

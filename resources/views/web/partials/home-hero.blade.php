@php
    $heroImage = $heroInstrument?->image_url ?: asset('frontend/assets/hero-lab-DpylzpE1.jpg');
    $heroAlt = $heroInstrument?->name ? $heroInstrument->name . ' instrument image' : 'Featured laboratory instrument';
    $heroStats = $ecosystemStats ?? [
        ['label' => 'Live inventory', 'value' => 'Always on'],
        ['label' => 'Research access', 'value' => '24/7'],
        ['label' => 'Queue visibility', 'value' => 'Real-time'],
    ];
@endphp

<section class="hero-section" data-reveal data-mouse-glow>
    <div class="hero-grid">
        <div class="hero-copy">
            <div class="hero-kicker-row">
                <span class="hero-kicker">Biomaterials Laboratory (BL)</span>
                <span class="hero-live-chip">Funded by SERB</span>
            </div>

            <h1 class="hero-title">Fostering inclusive, interdisciplinary biomaterials research and sustainable technologies.</h1>
            <p class="hero-description">
                Fostering an inclusive, ethical, and interdisciplinary research environment prioritizing equity, diversity and translational impact in diagnostics, regenerative therapies, therapeutic delivery, and food & agricultural technologies.
            </p>

            <div class="mt-3 flex items-center gap-3">
                <img src="{{ asset('frontend/assets/serb-logo.svg') }}" alt="SERB logo" class="h-10 w-auto opacity-90 hover:opacity-100 transition-opacity">
                <img src="{{ asset('frontend/assets/centre-logo.svg') }}" alt="Biomaterials Laboratory logo" class="h-10 w-auto opacity-90 hover:opacity-100 transition-opacity">
            </div>

            @if($heroInstrument)
                <p class="hero-featured">Featured: {{ $heroInstrument->name }}</p>
            @endif

            <div class="hero-actions">
                <a href="#popular-product" class="hero-primary-btn">Explore Instruments</a>
                <a href="{{ route('web.bag') }}" class="hero-secondary-btn">Open Bag</a>
            </div>

            <div class="hero-stat-grid">
                @foreach($heroStats as $stat)
                    <article class="hero-stat-card">
                        <span class="hero-stat-label">{{ $stat['label'] }}</span>
                        <span class="hero-stat-value">{{ $stat['value'] }}</span>
                    </article>
                @endforeach
            </div>
        </div>

        <div class="hero-visual">
            <div class="hero-visual-panel" data-parallax-card data-float>
                <div class="hero-visual-orb"></div>
                <div class="hero-visual-grid" aria-hidden="true"></div>
                <div class="hero-visual-meta">
                    <span class="hero-visual-meta-kicker">Featured instrument</span>
                    <span class="hero-visual-meta-title">Research-grade workflow</span>
                </div>
                <img
                    src="{{ $heroImage }}"
                    alt="{{ $heroAlt }}"
                    class="hero-visual-img"
                >
                <div class="hero-float-card hero-float-card-left">
                    <span class="hero-float-label">Queue status</span>
                    <strong>{{ $heroInstrument?->is_available ? 'Open' : 'Active' }}</strong>
                </div>
                <div class="hero-float-card hero-float-card-right">
                    <span class="hero-float-label">Location</span>
                    <strong>{{ $heroInstrument?->location ?: 'Main Lab' }}</strong>
                </div>
            </div>
        </div>
    </div>
</section>
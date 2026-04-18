@php
    $heroImage = $heroInstrument?->image_url ?: asset('frontend/assets/hero-lab-DpylzpE1.jpg');
    $heroAlt = $heroInstrument?->name ? $heroInstrument->name . ' instrument image' : 'Featured laboratory instrument';
@endphp

<section class="hero-section">
    <div class="hero-grid">
        <div class="hero-copy">
            <h1 class="hero-title">Latest Best Devices</h1>
            <p class="hero-description">
                Discover bookable lab instruments with live availability, smooth date selection, and a faster request approval flow.
            </p>

            @if($heroInstrument)
                <p class="text-sm font-medium text-teal-700">Featured: {{ $heroInstrument->name }}</p>
            @endif

            <div class="hero-actions">
                <a href="#popular-product" class="hero-primary-btn">Explore Instruments</a>
            </div>
        </div>

        <div class="hero-visual">
            <div class="hero-visual-panel">
                <div class="hero-visual-orb"></div>
                <img
                    src="{{ $heroImage }}"
                    alt="{{ $heroAlt }}"
                    class="hero-visual-img"
                >
            </div>
        </div>
    </div>
</section>
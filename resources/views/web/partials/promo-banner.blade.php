<article class="promo-card {{ $banner['variant'] === 'watch' ? 'promo-card-watch' : 'promo-card-mobile' }}">
    <div class="promo-card-copy">
        <p class="promo-card-label">{{ $banner['label'] }}</p>
        <h3 class="promo-card-title">{{ $banner['title'] }}</h3>
        <p class="promo-card-price">Start From ${{ $banner['price'] }}</p>
        <a href="{{ $banner['url'] ?? route('web.home') }}" class="promo-card-link">
            {{ $banner['cta'] }}
            <i data-lucide="arrow-right" class="h-4 w-4"></i>
        </a>
    </div>

    <div class="promo-card-visual" aria-hidden="true">
        <img src="{{ $banner['image'] }}" alt="{{ $banner['alt'] }}" class="promo-card-device">
    </div>
</article>
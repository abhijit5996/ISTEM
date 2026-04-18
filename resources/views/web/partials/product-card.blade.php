@php
    $isAvailable = $instrument->is_available;
    $price = number_format((float) ($instrument->usage_cost ?? 0), 2);
    $listPrice = number_format(((float) ($instrument->usage_cost ?? 0)) * 1.15, 2);
    $queueCount = $instrument->queues()->whereIn('status', ['pending', 'approved'])->count();
    $image = $instrument->image_url ?: asset('frontend/assets/hero-lab-DpylzpE1.jpg');
@endphp

<article class="product-card group">
    <div class="product-card-media">
        <span class="product-card-badge">{{ $isAvailable ? 'Open' : 'Booked' }}</span>
        @if(session('web_user_id'))
            <form method="POST" action="{{ route('web.favorites.add', $instrument->id) }}">
                @csrf
                <button type="submit" class="product-card-favorite" aria-label="Add {{ $instrument->name }} to favorites">
                    <i data-lucide="heart" class="h-4 w-4"></i>
                </button>
            </form>
        @else
            <a href="{{ route('web.login') }}" class="product-card-favorite" aria-label="Login to add {{ $instrument->name }} to favorites">
                <i data-lucide="heart" class="h-4 w-4"></i>
            </a>
        @endif

        <img src="{{ $image }}" alt="{{ $instrument->name }}" class="product-card-image">

        @if($featured)
            <a href="{{ route('web.instrument', $instrument->id) }}" class="product-card-quickview" aria-label="Quick view {{ $instrument->name }}">
                <i data-lucide="eye" class="h-4 w-4"></i>
                Quick view
            </a>
        @endif
    </div>

    <div class="product-card-body">
        <div>
            <h3 class="product-card-title">{{ $instrument->name }}</h3>
            <div class="product-card-prices">
                <span class="product-card-price-current">${{ $price }}</span>
                <span class="product-card-price-old">${{ $listPrice }}</span>
            </div>
        </div>

        <div class="product-card-rating">
            <span class="product-card-stars" aria-hidden="true">
                <i data-lucide="star" class="h-3.5 w-3.5"></i>
                <i data-lucide="star" class="h-3.5 w-3.5"></i>
                <i data-lucide="star" class="h-3.5 w-3.5"></i>
                <i data-lucide="star" class="h-3.5 w-3.5"></i>
                <i data-lucide="star" class="h-3.5 w-3.5"></i>
            </span>
            <span>{{ $isAvailable ? 'Available now' : 'Queue active' }}</span>
        </div>

        <div class="product-card-specs" aria-label="Product specifications">
            <span class="product-chip">{{ $instrument->category ?: 'General' }}</span>
            <span class="product-chip">{{ $instrument->location ?: 'Main Lab' }}</span>
            <span class="product-chip">Queue {{ $queueCount }}</span>
        </div>

        <div class="product-card-footer">
            <span class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $isAvailable ? 'Ready to book' : 'Join queue' }}</span>
            <a href="{{ route('web.instrument', $instrument->id) }}" class="product-cart-btn" aria-label="Open {{ $instrument->name }} details">
                <i data-lucide="shopping-cart" class="h-4 w-4"></i>
            </a>
        </div>
    </div>
</article>
@extends('layouts.main')

@section('breadcrumbs')
    <x-ui.breadcrumbs :items="[
        ['label' => 'Home', 'url' => route('web.home')],
        ['label' => $currentCategory ? ('Category: ' . $currentCategory) : 'Instruments']
    ]" />
@endsection

@section('content')
    <x-ui.page-header
        kicker="Instrument Marketplace"
        title="{{ $currentCategory ? ('Category: ' . $currentCategory) : 'Browse Instruments' }}"
        subtitle="Shop-style listing with filters, availability checks, and quick booking actions."
    />

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-[0.32fr_0.68fr]">
        <aside class="panel space-y-4 xl:sticky xl:top-24 xl:self-start">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-xl font-semibold">Filters</h2>
                <button
                    type="button"
                    class="btn-pill btn-ghost h-9 px-3 text-xs"
                    data-filter-toggle
                    data-filter-target="listing-filters-panel"
                    aria-expanded="false"
                    aria-controls="listing-filters-panel"
                >
                    <i data-lucide="sliders-horizontal" class="h-3.5 w-3.5"></i>
                    Filter
                </button>
            </div>

            <div id="listing-filters-panel" class="hidden space-y-4" data-filter-panel>
                <form method="GET" action="{{ $currentCategory ? route('web.category', $currentCategory) : route('web.instruments') }}" class="space-y-4" data-ajax-listing-form>
                <div>
                    <label class="label-ui" for="search">Search</label>
                    <input id="search" type="search" name="search" class="input-ui" value="{{ $filters['search'] ?? '' }}" placeholder="Name, category, location">
                </div>

                <div>
                    <label class="label-ui" for="location">Location</label>
                    <select id="location" name="location" class="select-ui">
                        <option value="">All Locations</option>
                        @foreach($locations as $location)
                            <option value="{{ $location }}" @selected(($filters['location'] ?? '') === $location)>{{ $location }}</option>
                        @endforeach
                    </select>
                </div>

                @if(!$currentCategory)
                    <div>
                        <p class="label-ui">Categories</p>
                        <div class="max-h-48 space-y-2 overflow-y-auto rounded-2xl border border-slate-200 p-3 dark:border-slate-700">
                            @foreach($categories as $category)
                                <label class="flex items-center gap-2 text-sm">
                                    <input
                                        type="checkbox"
                                        name="category[]"
                                        value="{{ $category }}"
                                        class="h-4 w-4 rounded border-slate-300"
                                        @checked(in_array($category, $filters['category'] ?? [], true))
                                    >
                                    <span>{{ $category }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div>
                    <label class="label-ui" for="availability">Availability</label>
                    <select id="availability" name="availability" class="select-ui">
                        <option value="all" @selected(($filters['availability'] ?? 'all') === 'all')>All</option>
                        <option value="available" @selected(($filters['availability'] ?? '') === 'available')>Available</option>
                        <option value="booked" @selected(($filters['availability'] ?? '') === 'booked')>Booked</option>
                    </select>
                </div>

                <div>
                    <label class="label-ui" for="status">Status</label>
                    <select id="status" name="status" class="select-ui">
                        <option value="all" @selected(($filters['status'] ?? 'all') === 'all')>All</option>
                        <option value="available" @selected(($filters['status'] ?? '') === 'available')>Available</option>
                        <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                        <option value="maintenance" @selected(($filters['status'] ?? '') === 'maintenance')>Maintenance</option>
                        <option value="booked" @selected(($filters['status'] ?? '') === 'booked')>Booked</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label class="label-ui" for="min_price">Min Price</label>
                        <input id="min_price" type="number" min="0" step="0.01" name="min_price" class="input-ui" value="{{ $filters['min_price'] ?? '' }}" placeholder="0">
                    </div>
                    <div>
                        <label class="label-ui" for="max_price">Max Price</label>
                        <input id="max_price" type="number" min="0" step="0.01" name="max_price" class="input-ui" value="{{ $filters['max_price'] ?? '' }}" placeholder="1000">
                    </div>
                </div>

                    <div class="flex flex-wrap gap-2">
                        <button type="submit" class="btn-pill btn-primary">Apply Filters</button>
                        <a href="{{ $currentCategory ? route('web.category', $currentCategory) : route('web.instruments') }}" class="btn-pill btn-ghost">Reset</a>
                    </div>
                </form>

                @if($categories->count() > 0)
                    <div>
                        <p class="text-sm font-semibold">Categories</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($categories as $category)
                                <a href="{{ route('web.category', $category) }}" class="btn-pill {{ ($currentCategory && $currentCategory === $category) || in_array($category, $filters['category'] ?? [], true) ? 'btn-primary' : 'btn-ghost' }}">{{ $category }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </aside>

        <div class="space-y-6">
            <div class="panel flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <p class="text-sm text-slate-600 dark:text-slate-300" data-listing-summary>Showing {{ $instruments->firstItem() ?? 0 }}-{{ $instruments->lastItem() ?? 0 }} of {{ $totalInstruments }} instrument(s)</p>
                <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
                    <select class="select-ui sm:min-w-52" data-ajax-listing-sort>
                        <option value="name_asc">Name A-Z</option>
                        <option value="newest">Newest</option>
                        <option value="price_asc">Price Low-High</option>
                        <option value="price_desc">Price High-Low</option>
                    </select>
                    <a href="{{ route('web.bag') }}" class="btn-pill btn-ghost">Open Bag</a>
                </div>
            </div>

            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4" data-ajax-listing-grid data-ajax-category-default="{{ $currentCategory ?: '' }}">
                @forelse($instruments as $instrument)
                    <x-ui.instrument-card :instrument="$instrument" />
                @empty
                    <div class="sm:col-span-2 md:col-span-3 lg:col-span-4">
                        <x-ui.empty-state title="No instruments matched" description="Try changing category, location, price, or availability filters." />
                    </div>
                @endforelse
            </section>

            @if($instruments->hasPages())
                <div class="flex justify-center" data-ajax-listing-pagination>
                    {{ $instruments->links() }}
                </div>
            @else
                <div class="flex justify-center" data-ajax-listing-pagination></div>
            @endif

            <div class="hidden items-center gap-2 text-sm text-slate-500" data-ajax-listing-loading>
                <span class="skeleton h-4 w-4 rounded-full"></span>
                Loading instruments...
            </div>
        </div>
    </section>
@endsection

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

    <section class="grid grid-cols-1 gap-4 xl:grid-cols-[0.32fr_0.68fr]">
        <aside class="panel space-y-4 xl:sticky xl:top-24 xl:self-start">
            <h2 class="text-xl font-semibold">Filters</h2>

            <form method="GET" action="{{ $currentCategory ? route('web.category', $currentCategory) : route('web.instruments') }}" class="space-y-4">
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

                <div class="grid grid-cols-2 gap-2">
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
        </aside>

        <div class="space-y-4">
            <div class="panel flex flex-wrap items-center justify-between gap-2">
                <p class="text-sm text-slate-600 dark:text-slate-300">Showing {{ $instruments->firstItem() ?? 0 }}-{{ $instruments->lastItem() ?? 0 }} of {{ $totalInstruments }} instrument(s)</p>
                <a href="{{ route('web.bag') }}" class="btn-pill btn-ghost">Open Bag</a>
            </div>

            <section class="grid grid-cols-1 gap-4 md:grid-cols-2 2xl:grid-cols-3">
                @forelse($instruments as $instrument)
                    <x-ui.instrument-card :instrument="$instrument" />
                @empty
                    <div class="md:col-span-2 2xl:col-span-3">
                        <x-ui.empty-state title="No instruments matched" description="Try changing category, location, price, or availability filters." />
                    </div>
                @endforelse
            </section>

            @if($instruments->hasPages())
                <div class="mt-4 flex justify-center">
                    {{ $instruments->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection

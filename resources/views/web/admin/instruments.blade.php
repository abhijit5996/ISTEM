@extends('layouts.main')

@section('content')
    <x-ui.page-header
        kicker="Administration"
        title="Instrument Management"
        subtitle="Manage inventory, upload assets, and maintain availability by location."
    >
        <div class="flex flex-wrap gap-2">
            <button type="button" class="btn-pill btn-primary" data-open-modal="add-instrument-modal">Add Instrument</button>
            <button type="button" class="btn-pill btn-ghost" data-open-modal="bulk-upload-modal">Bulk Upload</button>
        </div>
    </x-ui.page-header>

    <section class="panel">
        <form method="GET" action="{{ route('web.admin.instruments') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div>
                <label class="label-ui" for="location">Filter by location</label>
                <select id="location" name="location" class="select-ui">
                    <option value="">All Locations</option>
                    @foreach($locations as $location)
                        <option value="{{ $location }}" @selected(($filters['location'] ?? '') === $location)>{{ $location }}</option>
                    @endforeach
                </select>
            </div>
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
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label-ui" for="search">Search</label>
                <input id="search" type="search" name="search" class="input-ui" placeholder="Search by name/category/location" value="{{ $filters['search'] ?? '' }}">
            </div>
            <div class="sm:col-span-2 xl:col-span-4 flex flex-wrap gap-2">
                <button class="btn-pill btn-primary" type="submit">Apply Filters</button>
                <a class="btn-pill btn-ghost" href="{{ route('web.admin.instruments') }}">Reset</a>
            </div>
        </form>
    </section>

    <x-ui.table :headers="['Name', 'Status', 'Location', 'Availability', 'Actions']" class="mt-6">
        @foreach($instruments as $instrument)
            <tr>
                <td class="font-semibold">{{ $instrument->name }}</td>
                <td>{{ $instrument->status ?: 'available' }}</td>
                <td>{{ $instrument->location }}</td>
                <td>
                    <span class="status-chip {{ $instrument->is_available ? 'status-available' : 'status-booked' }}">{{ $instrument->is_available ? 'Available' : 'Booked' }}</span>
                </td>
                <td>
                    <div class="flex flex-wrap items-center gap-2">
                        <button class="btn-pill btn-ghost" type="button" data-open-modal="edit-instrument-{{ $instrument->id }}">Edit</button>
                        <form method="POST" action="{{ route('web.admin.instruments.delete', $instrument->id) }}">
                            @csrf
                            <button class="btn-pill btn-warn" type="submit">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-ui.table>

    @if($instruments->hasPages())
        <div class="mt-6 flex justify-center">
            {{ $instruments->links() }}
        </div>
    @endif

    @foreach($instruments as $instrument)
        <x-ui.modal id="edit-instrument-{{ $instrument->id }}" title="Edit Instrument">
            <form method="POST" action="{{ route('web.admin.instruments.update', $instrument->id) }}" enctype="multipart/form-data" class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                @csrf
                <x-ui.form-input class="sm:col-span-2" label="Name" name="name" :value="$instrument->name" required />
                <x-ui.form-input label="Category" name="category" :value="$instrument->category" required />
                <x-ui.form-input label="Location" name="location" :value="$instrument->location" required />
                <x-ui.form-input label="Usage Cost" name="usage_cost" :value="$instrument->usage_cost" />
                <div class="sm:col-span-2">
                    <label class="label-ui">Status</label>
                    <select class="select-ui" name="status">
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" @selected(($instrument->status ?: 'available') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <x-ui.form-input class="sm:col-span-2" label="Description" name="description" :value="$instrument->description" />
                <x-ui.file-upload class="sm:col-span-2" label="Replace Image" name="image" accept="image/*" />
                <div class="sm:col-span-2 flex justify-end gap-2">
                    <button type="button" class="btn-pill btn-ghost" data-close-modal>Cancel</button>
                    <button class="btn-pill btn-primary" type="submit">Save Changes</button>
                </div>
            </form>
        </x-ui.modal>
    @endforeach

    <x-ui.modal id="add-instrument-modal" title="Add Instrument">
        <form method="POST" action="{{ route('web.admin.instruments.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            @csrf
            <x-ui.form-input class="sm:col-span-2" label="Name" name="name" required />
            <x-ui.form-input label="Category" name="category" required />
            <x-ui.form-input label="Location" name="location" required />
            <x-ui.form-input label="Usage Cost" name="usage_cost" />
            <x-ui.form-input class="sm:col-span-2" label="Description" name="description" />
            <x-ui.file-upload class="sm:col-span-2" label="Image Upload" name="image" accept="image/*" />
            <div class="sm:col-span-2 flex justify-end gap-2">
                <button type="button" class="btn-pill btn-ghost" data-close-modal>Cancel</button>
                <button class="btn-pill btn-primary" type="submit">Create</button>
            </div>
        </form>
    </x-ui.modal>

    <x-ui.modal id="bulk-upload-modal" title="Bulk Upload">
        <form method="POST" action="{{ route('web.admin.instruments.bulk-upload') }}" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <x-ui.file-upload label="Upload CSV" name="bulk_upload" accept=".csv,text/csv" />
            <p class="text-sm text-slate-500 dark:text-slate-400">CSV headers: <span class="font-semibold">name, category, location</span>. Optional: description, usage_cost, status.</p>
            <div class="flex justify-end gap-2">
                <button type="button" class="btn-pill btn-ghost" data-close-modal>Cancel</button>
                <button type="submit" class="btn-pill btn-primary">Upload</button>
            </div>
        </form>
    </x-ui.modal>
@endsection

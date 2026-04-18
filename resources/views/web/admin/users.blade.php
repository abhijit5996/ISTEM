@extends('layouts.main')

@section('content')
    <x-ui.page-header
        kicker="Administration"
        title="User Management"
        subtitle="View user details and account status controls."
    >
        <a href="{{ route('web.admin.users.export') }}" class="btn-pill btn-ghost">Export CSV</a>
    </x-ui.page-header>

    <section class="panel mb-4">
        <form method="GET" action="{{ route('web.admin.users') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-[1fr_auto_auto]">
            <input type="search" name="search" class="input-ui" value="{{ $filters['search'] ?? '' }}" placeholder="Search name, email, phone">
            <select name="sort" class="select-ui">
                <option value="newest" @selected(($filters['sort'] ?? 'newest') === 'newest')>Newest</option>
                <option value="oldest" @selected(($filters['sort'] ?? '') === 'oldest')>Oldest</option>
                <option value="name" @selected(($filters['sort'] ?? '') === 'name')>Name A-Z</option>
            </select>
            <div class="flex gap-2">
                <button type="submit" class="btn-pill btn-primary">Apply</button>
                <a href="{{ route('web.admin.users') }}" class="btn-pill btn-ghost">Reset</a>
            </div>
        </form>
    </section>

    <x-ui.table :headers="['Name', 'Email', 'Phone', 'Email Verified', 'Actions']">
        @forelse($users as $user)
            <tr>
                <td>
                    <p class="font-semibold">{{ $user->name }}</p>
                </td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->phone ?: 'N/A' }}</td>
                <td>{{ $user->email_verified ? 'Yes' : 'No' }}</td>
                <td>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" class="btn-pill btn-ghost">View Details</button>
                        <button type="button" class="btn-pill btn-warn">Activate / Deactivate</button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5">
                    <x-ui.empty-state title="No users found" description="User records will appear here when signups occur." />
                </td>
            </tr>
        @endforelse
    </x-ui.table>

    @if($users->hasPages())
        <div class="mt-4 flex justify-center">
            {{ $users->links() }}
        </div>
    @endif
@endsection

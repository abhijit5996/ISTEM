@extends('layouts.main')

@section('content')
    <section class="mx-auto max-w-xl">
        <x-ui.card title="Admin Login" subtitle="Control center access for bookings, inventory, queue, and analytics.">
            <form method="POST" action="{{ route('web.admin.login.submit') }}" class="space-y-3">
                @csrf
                <x-ui.form-input label="Username" name="username" required />
                <x-ui.form-input type="password" label="Password" name="password" required />
                <div class="flex flex-wrap gap-2">
                    <button class="btn-pill btn-primary" type="submit">Login as Admin</button>
                    <a class="btn-pill btn-ghost" href="{{ route('web.admin.signup') }}">Create Admin</a>
                </div>
            </form>
        </x-ui.card>
    </section>
@endsection

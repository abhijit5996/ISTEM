@extends('layouts.main')

@section('content')
    <section class="mx-auto max-w-xl">
        <x-ui.card title="Admin Setup" subtitle="Create administrator credentials for platform management.">
            @if($adminExists)
                <x-ui.empty-state title="Admin already exists" description="An administrator account is configured for this platform.">
                    <a class="btn-pill btn-primary" href="{{ route('web.admin.login') }}">Go to Admin Login</a>
                </x-ui.empty-state>
            @else
                <form method="POST" action="{{ route('web.admin.signup.submit') }}" class="space-y-3">
                    @csrf
                    <x-ui.form-input label="Username" name="username" required />
                    <x-ui.form-input type="password" label="Password" name="password" required />
                    <x-ui.form-input type="password" label="Confirm Password" name="password_confirmation" required />
                    <div class="flex flex-wrap gap-2">
                        <button class="btn-pill btn-primary" type="submit">Create Admin</button>
                        <a class="btn-pill btn-ghost" href="{{ route('web.admin.login') }}">Back to Login</a>
                    </div>
                </form>
            @endif
        </x-ui.card>
    </section>
@endsection

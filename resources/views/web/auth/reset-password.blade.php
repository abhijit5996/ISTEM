@extends('layouts.main')

@section('content')
    <section class="mx-auto max-w-xl">
        <x-ui.card title="Reset Password" subtitle="Create a new secure password for your account.">
            <form method="POST" action="{{ route('web.reset-password.submit') }}" class="space-y-3">
                @csrf
                <x-ui.form-input type="email" label="Email" name="email" :value="$email" required />
                <x-ui.form-input type="password" label="New Password" name="password" required />
                <x-ui.form-input type="password" label="Confirm Password" name="password_confirmation" required />
                <div class="flex flex-wrap gap-2">
                    <button class="btn-pill btn-primary" type="submit">Reset Password</button>
                    <a class="btn-pill btn-ghost" href="{{ route('web.login') }}">Back to Login</a>
                </div>
            </form>
        </x-ui.card>
    </section>
@endsection

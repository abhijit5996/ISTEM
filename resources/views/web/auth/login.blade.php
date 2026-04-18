@extends('layouts.main')

@section('content')
    <section class="mx-auto grid max-w-5xl grid-cols-1 gap-4 lg:grid-cols-2">
        <x-ui.card title="Welcome Back" subtitle="Sign in to continue with bookings, queue updates, and your profile dashboard.">
            <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-300">
                <li class="rounded-2xl border border-slate-200 p-3 dark:border-slate-700">Manage active bookings in one place</li>
                <li class="rounded-2xl border border-slate-200 p-3 dark:border-slate-700">Track queue positions in realtime-friendly cards</li>
                <li class="rounded-2xl border border-slate-200 p-3 dark:border-slate-700">View status notifications with toast feedback</li>
            </ul>
        </x-ui.card>

        <x-ui.card title="Login" subtitle="Secure sign-in for your booking workspace.">
            <form method="POST" action="{{ route('web.login.submit') }}" class="space-y-3">
                @csrf
                <x-ui.form-input type="email" label="Email" name="email" required />
                <x-ui.form-input type="password" label="Password" name="password" required />

                <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300">
                    Remember me
                </label>

                <div class="flex flex-wrap gap-2">
                    <button class="btn-pill btn-primary" type="submit">Login</button>
                    <a class="btn-pill btn-ghost" href="{{ route('web.signup') }}">Create Account</a>
                    <a class="btn-pill btn-ghost" href="{{ route('web.forgot-password.form') }}">Forgot Password?</a>
                </div>
            </form>
        </x-ui.card>
    </section>
@endsection

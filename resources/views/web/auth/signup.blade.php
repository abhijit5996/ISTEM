@extends('layouts.main')

@section('content')
    <section class="mx-auto max-w-3xl">
        <x-ui.card title="Create Account" subtitle="Sign up to reserve instruments and receive booking updates.">
            <form method="POST" action="{{ route('web.signup.submit') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2" data-ajax-auth="signup">
                @csrf
                <x-ui.form-input class="sm:col-span-2" label="Name" name="name" required />
                <x-ui.form-input label="Phone" name="phone" />
                <x-ui.form-input class="sm:col-span-2" type="email" label="Email" name="email" required />
                <x-ui.form-input type="password" label="Password" name="password" required />
                <x-ui.form-input type="password" label="Confirm Password" name="password_confirmation" required />

                <div class="sm:col-span-2 flex flex-wrap gap-2">
                    <button class="btn-pill btn-primary" type="submit">Create Account</button>
                    <a class="btn-pill btn-ghost" href="{{ route('web.login') }}">Already have an account?</a>
                </div>
            </form>
        </x-ui.card>
    </section>
@endsection

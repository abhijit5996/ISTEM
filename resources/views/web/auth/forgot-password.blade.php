@extends('layouts.main')

@section('content')
    <section class="mx-auto max-w-xl">
        <x-ui.card title="Forgot Password" subtitle="Submit your registered email to receive reset OTP.">
            <form method="POST" action="{{ route('web.forgot-password.submit') }}" class="space-y-3">
                @csrf
                <x-ui.form-input type="email" label="Email" name="email" required />
                <div class="flex flex-wrap gap-2">
                    <button class="btn-pill btn-primary" type="submit">Send OTP</button>
                    <a class="btn-pill btn-ghost" href="{{ route('web.login') }}">Back to Login</a>
                </div>
            </form>
        </x-ui.card>
    </section>
@endsection

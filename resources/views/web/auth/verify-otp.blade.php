@extends('layouts.main')

@section('content')
    <section class="mx-auto max-w-xl">
        <x-ui.card title="Verify OTP" subtitle="Enter the 6-digit code sent to your email.">
            <form method="POST" action="{{ route('web.verify-otp.submit') }}" class="space-y-4">
                @csrf
                <x-ui.form-input type="email" label="Email" name="email" :value="$email" required />
                <x-ui.form-input label="OTP" name="otp" required maxlength="6" />
                <div class="flex flex-wrap gap-2">
                    <button class="btn-pill btn-primary" type="submit">Verify</button>
                    <a class="btn-pill btn-ghost" href="{{ route('web.login') }}">Back to Login</a>
                </div>
            </form>
        </x-ui.card>
    </section>
@endsection

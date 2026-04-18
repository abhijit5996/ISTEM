@extends('layouts.main')

@section('content')
    <x-ui.page-header
        kicker="Booking Workflow"
        title="Booking Form"
        subtitle="Step 1: Date review, Step 2: Details, Step 3: Review and submit."
    />

    <section class="panel" data-stepper>
        <div class="grid grid-cols-1 gap-2 text-center text-sm sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 px-3 py-2 font-semibold dark:border-slate-700" data-step-indicator="1">1. Date Range</div>
            <div class="rounded-2xl border border-slate-200 px-3 py-2 font-semibold dark:border-slate-700" data-step-indicator="2">2. Details</div>
            <div class="rounded-2xl border border-slate-200 px-3 py-2 font-semibold dark:border-slate-700" data-step-indicator="3">3. Review</div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-[0.9fr_1.1fr]">
        <x-ui.card title="Selected Instruments">
            <div class="space-y-3">
                @foreach($bag as $item)
                    <div class="rounded-2xl border border-slate-200 p-3 dark:border-slate-700">
                        <p class="font-semibold">{{ $item['instrument_name'] }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $item['start_date'] }} to {{ $item['end_date'] }}</p>
                    </div>
                @endforeach
            </div>
        </x-ui.card>

        <x-ui.card title="Request Details">
            <form method="POST" action="{{ route('web.booking.submit') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2" data-stepper-form data-ajax-booking-submit>
                @csrf
                <div class="sm:col-span-2 space-y-4" data-step-panel="1">
                    <p class="text-sm text-slate-500 dark:text-slate-400">Review selected instrument slots below before entering personal details.</p>
                    <div class="rounded-2xl border border-slate-200 p-4 dark:border-slate-700">
                        <p class="text-sm font-semibold">Selected Date Ranges</p>
                        <ul class="mt-2 space-y-2 text-sm text-slate-600 dark:text-slate-300">
                            @foreach($bag as $item)
                                <li>{{ $item['instrument_name'] }}: {{ $item['start_date'] }} to {{ $item['end_date'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button" class="btn-pill btn-primary w-full sm:w-auto" data-step-next>Continue to Details</button>
                </div>

                <div class="sm:col-span-2 grid grid-cols-1 gap-4 sm:grid-cols-2" data-step-panel="2">
                    <x-ui.form-input class="sm:col-span-1" label="Employee Name" name="name" :value="$user?->name" required />
                    <x-ui.form-input class="sm:col-span-1" type="email" label="Email" name="email" :value="$user?->email" required />

                    <div>
                        <label class="label-ui">User Type</label>
                        <select class="select-ui" name="user_type" required>
                            <option value="student" @selected(old('user_type') === 'student')>Student</option>
                            <option value="employee" @selected(old('user_type') === 'employee')>Employee</option>
                        </select>
                    </div>

                    <x-ui.form-input label="Employee ID" name="identifier" required />
                    <x-ui.form-input label="Phone" name="phone" :value="$user?->phone" />
                    <x-ui.form-input label="Department" name="department" required />
                    <x-ui.form-input label="Program / School" name="program_or_school" required />
                    <x-ui.form-input class="sm:col-span-2" label="Reason for Booking" name="project_title" placeholder="Project title or reason" />

                    <label class="sm:col-span-2 flex items-center gap-2 rounded-2xl border border-slate-300 p-3 text-sm dark:border-slate-700">
                        <input type="checkbox" class="h-4 w-4 rounded border-slate-300" id="confidential_project" name="confidential_project" value="1" @checked(old('confidential_project'))>
                        Confidential project
                    </label>

                    <div class="sm:col-span-2 flex flex-wrap gap-2">
                        <button type="button" class="btn-pill btn-ghost" data-step-prev>Back</button>
                        <button type="button" class="btn-pill btn-primary" data-step-next>Review Submission</button>
                    </div>
                </div>

                <div class="sm:col-span-2 space-y-4" data-step-panel="3">
                    <div class="rounded-2xl border border-slate-200 p-4 dark:border-slate-700">
                        <p class="text-sm font-semibold">Final Review</p>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Verify details and submit booking request. Conflict validation and queue processing continue to use existing backend logic.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" class="btn-pill btn-ghost" data-step-prev>Back</button>
                        <button class="btn-pill btn-primary" type="submit">Submit Booking Request</button>
                        <a class="btn-pill btn-ghost" href="{{ route('web.bag') }}">Back to Bag</a>
                    </div>
                </div>
            </form>
        </x-ui.card>
    </section>
@endsection

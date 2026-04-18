@extends('layouts.main')

@section('breadcrumbs')
    <x-ui.breadcrumbs :items="[
        ['label' => 'Home', 'url' => route('web.home')],
        ['label' => 'Profile', 'url' => route('web.profile')],
        ['label' => 'Queue Status']
    ]" />
@endsection

@section('content')
    <x-ui.page-header
        kicker="Queue System"
        title="Queue Status"
        subtitle="Realtime-friendly queue view with progress and estimated wait indicators."
    />

    <section class="grid grid-cols-1 gap-6 lg:grid-cols-2" data-queue-live>
        @forelse($queueItems as $item)
            <x-ui.card>
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <p class="text-sm font-semibold">{{ $item->instrument?->name ?: $item->instrument_id }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $item->date ?: '-' }} {{ $item->time_slot ?: '' }}</p>
                    </div>
                    <span class="status-chip status-{{ strtolower($item->status) }}">{{ ucfirst($item->status) }}</span>
                </div>

                <div class="mt-3 space-y-2 text-sm text-slate-600 dark:text-slate-300">
                    <p>Queue ID: {{ $item->id }}</p>
                    <p>Position: <span class="font-semibold">{{ $item->queue_position }}</span></p>
                    <p>Estimated wait: <span class="font-semibold">{{ max(1, (int) $item->queue_position * 5) }} minutes</span></p>
                </div>

                <div class="mt-3">
                    <x-ui.progress-bar :value="max(5, 100 - ((int) $item->queue_position * 9))" label="Queue Progress" />
                </div>
            </x-ui.card>
        @empty
            <div class="lg:col-span-2">
                <x-ui.empty-state title="No queue entries found" description="Join queue from instrument details to track waiting position." />
            </div>
        @endforelse
    </section>

    @if($queueItems->hasPages())
        <div class="mt-6 flex justify-center">
            {{ $queueItems->links() }}
        </div>
    @endif

    @push('scripts')
        <script>
            (function () {
                const queueLiveRoot = document.querySelector('[data-queue-live]');
                if (!queueLiveRoot) {
                    return;
                }

                window.setInterval(() => {
                    if (document.hidden) {
                        return;
                    }

                    window.location.reload();
                }, 30000);
            })();
        </script>
    @endpush
@endsection

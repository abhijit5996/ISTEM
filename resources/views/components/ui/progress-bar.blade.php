@props([
    'value' => 0,
    'label' => 'Progress',
])

@php
    $safeValue = max(0, min((int) $value, 100));
@endphp

<div class="space-y-1" data-queue-progress>
    <div class="flex items-center justify-between text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">
        <span>{{ $label }}</span>
        <span>{{ $safeValue }}%</span>
    </div>
    <div class="h-2 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800">
        <div class="h-full rounded-full bg-gradient-to-r from-cyan-500 to-teal-500 transition-all duration-700" style="width: {{ $safeValue }}%"></div>
    </div>
</div>

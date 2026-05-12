@props([
    'type' => 'info',
    'title' => null,
])

@php
    $typeClasses = [
        'info' => 'alert-ui alert-ui-info',
        'success' => 'alert-ui alert-ui-success',
        'warning' => 'alert-ui alert-ui-warning',
        'danger' => 'alert-ui alert-ui-danger',
    ];

    $classes = $typeClasses[$type] ?? $typeClasses['info'];
@endphp

<div role="alert" {{ $attributes->merge(['class' => $classes]) }}>
    <div class="flex items-start gap-3">
        <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/70 dark:bg-white/10">
            <i data-lucide="info" class="h-4 w-4"></i>
        </div>

        <div class="min-w-0 flex-1">
            @if($title)
                <p class="text-sm font-semibold">{{ $title }}</p>
            @endif

            @if(trim((string) $slot) !== '')
                <div class="mt-1 text-sm leading-6 opacity-90">
                    {{ $slot }}
                </div>
            @endif
        </div>
    </div>
</div>
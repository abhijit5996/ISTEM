@props([
    'id',
    'title' => 'Modal',
])

<template id="{{ $id }}">
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-semibold text-slate-900 dark:text-slate-100">{{ $title }}</h3>
            <button type="button" class="icon-btn" data-close-modal>
                <i data-lucide="x" class="h-4 w-4"></i>
            </button>
        </div>
        {{ $slot }}
    </div>
</template>

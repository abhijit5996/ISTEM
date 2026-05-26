@props([
    'id',
    'title' => 'Modal',
])

<template id="{{ $id }}">
    <div class="space-y-5 rounded-3xl border border-slate-200/70 bg-white p-5 shadow-2xl dark:border-white/10 dark:bg-slate-950/95">
        <div class="flex items-center justify-between gap-4">
            <h3 class="text-xl font-semibold text-slate-900 dark:text-slate-100">{{ $title }}</h3>
            <button type="button" class="icon-btn" data-close-modal aria-label="Close modal">
                <i data-lucide="x" class="h-4 w-4"></i>
                <span class="sr-only">Close modal</span>
            </button>
        </div>
        {{ $slot }}
    </div>
</template>

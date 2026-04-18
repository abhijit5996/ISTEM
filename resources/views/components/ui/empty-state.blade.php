@props([
    'title' => 'No data found',
    'description' => null,
])

<div class="panel text-center">
    <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-300">
        <i data-lucide="inbox" class="h-6 w-6"></i>
    </div>
    <h3 class="text-lg font-semibold">{{ $title }}</h3>
    @if($description)
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $description }}</p>
    @endif
    @if(trim((string) $slot) !== '')
        <div class="mt-4">{{ $slot }}</div>
    @endif
</div>

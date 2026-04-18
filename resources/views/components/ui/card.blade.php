@props([
    'title' => null,
    'subtitle' => null,
    'class' => '',
])

<article {{ $attributes->merge(['class' => trim('panel ' . $class)]) }}>
    @if($title)
        <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">{{ $title }}</h3>
    @endif
    @if($subtitle)
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $subtitle }}</p>
    @endif

    @if(trim((string) $slot) !== '')
        <div class="{{ $title || $subtitle ? 'mt-4 space-y-4' : 'space-y-4' }}">
            {{ $slot }}
        </div>
    @endif
</article>

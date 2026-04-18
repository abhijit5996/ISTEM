@props([
    'kicker' => null,
    'title' => null,
    'subtitle' => null,
    'class' => '',
])

<section {{ $attributes->merge(['class' => trim($class)]) }}>
    @if($kicker)
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700 dark:text-cyan-300">{{ $kicker }}</p>
    @endif

    @if($title)
        <h2 class="mt-1 text-xl font-semibold sm:text-2xl">{{ $title }}</h2>
    @endif

    @if($subtitle)
        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $subtitle }}</p>
    @endif
</section>
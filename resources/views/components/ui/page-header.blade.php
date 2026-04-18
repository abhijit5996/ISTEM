@props([
    'kicker' => null,
    'title' => null,
    'subtitle' => null,
    'class' => '',
])

<section {{ $attributes->merge(['class' => trim('panel ' . $class)]) }}>
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            @if($kicker)
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700 dark:text-cyan-300">{{ $kicker }}</p>
            @endif

            @if($title)
                <h1 class="mt-1 text-2xl font-semibold sm:text-3xl">{{ $title }}</h1>
            @endif

            @if($subtitle)
                <p class="mt-2 max-w-3xl text-sm text-slate-500 dark:text-slate-400">{{ $subtitle }}</p>
            @endif
        </div>

        @if(trim((string) $slot) !== '')
            <div>{{ $slot }}</div>
        @endif
    </div>
</section>
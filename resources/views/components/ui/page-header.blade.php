@props([
    'kicker' => null,
    'title' => null,
    'subtitle' => null,
    'class' => '',
])

<section {{ $attributes->merge(['class' => trim('panel ' . $class)]) }}>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between sm:gap-6">
        <div>
            @if($kicker)
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700 dark:text-cyan-300">{{ $kicker }}</p>
            @endif

            @if($title)
                <h1 class="mt-1 text-2xl font-semibold leading-tight sm:text-3xl">{{ $title }}</h1>
            @endif

            @if($subtitle)
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $subtitle }}</p>
            @endif
        </div>

        @if(trim((string) $slot) !== '')
            <div class="flex w-full flex-wrap gap-2 sm:w-auto sm:justify-end">{{ $slot }}</div>
        @endif
    </div>
</section>
@props([
    'items' => [],
])

<nav aria-label="Breadcrumb" class="panel py-3">
    <ol class="flex flex-wrap items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        @foreach($items as $item)
            @php
                $isLast = $loop->last;
                $label = $item['label'] ?? '';
                $url = $item['url'] ?? null;
            @endphp

            <li class="inline-flex items-center gap-2">
                @if($url && !$isLast)
                    <a href="{{ $url }}" class="font-medium text-slate-600 transition hover:text-cyan-700 dark:text-slate-300 dark:hover:text-cyan-300">{{ $label }}</a>
                @else
                    <span class="{{ $isLast ? 'font-semibold text-slate-800 dark:text-slate-100' : '' }}">{{ $label }}</span>
                @endif

                @if(!$isLast)
                    <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i>
                @endif
            </li>
        @endforeach
    </ol>
</nav>

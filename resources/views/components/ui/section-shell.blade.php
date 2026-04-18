@props([
    'class' => '',
])

<section {{ $attributes->merge(['class' => trim('section-shell ' . $class)]) }}>
    {{ $slot }}
</section>
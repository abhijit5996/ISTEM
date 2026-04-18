@props([
    'class' => 'h-4 w-full',
])

<div {{ $attributes->merge(['class' => trim('skeleton ' . $class)]) }}></div>

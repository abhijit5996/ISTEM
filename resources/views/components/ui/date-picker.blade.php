@props([
    'label',
    'name',
    'value' => '',
    'required' => false,
    'minDate' => now()->toDateString(),
])

<div>
    <label for="{{ $name }}" class="label-ui">{{ $label }}</label>
    <input id="{{ $name }}" type="date" name="{{ $name }}" value="{{ old($name, $value) }}" min="{{ $minDate }}" {{ $required ? 'required' : '' }} {{ $attributes->merge(['class' => 'input-ui']) }}>
</div>

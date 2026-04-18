@props([
    'label' => 'Upload file',
    'name' => 'file',
    'accept' => '*/*',
])

<div>
    <label class="label-ui">{{ $label }}</label>
    <label class="flex cursor-pointer items-center justify-between rounded-2xl border border-dashed border-slate-300 bg-white px-4 py-3 text-sm text-slate-500 transition hover:border-cyan-400 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
        <span>Choose file</span>
        <i data-lucide="upload" class="h-4 w-4"></i>
        <input type="file" name="{{ $name }}" accept="{{ $accept }}" class="hidden" {{ $attributes }}>
    </label>
</div>

@props([
    'headers' => [],
])

<div {{ $attributes->merge(['class' => 'table-shell']) }}>
    <div class="overflow-x-auto">
        <table class="table-ui">
            @if(count($headers))
                <thead>
                <tr>
                    @foreach($headers as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
                </thead>
            @endif
            <tbody>
            {{ $slot }}
            </tbody>
        </table>
    </div>
</div>

@props([
    'headers' => [],
    'bodyId' => null,
])

<div {{ $attributes->merge(['class' => 'table-container']) }}>
    <table class="task-table">
        <thead>
            <tr>
                @foreach ($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody @if ($bodyId) id="{{ $bodyId }}" @endif>
            {{ $slot }}
        </tbody>
    </table>
</div>

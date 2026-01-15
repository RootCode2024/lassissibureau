@props(['startName' => 'date_from', 'endName' => 'date_to', 'startValue' => '', 'endValue' => ''])

<div {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row gap-4']) }}>
    <div class="flex-1">
        <x-input-label for="{{ $startName }}" value="Date début" />
        <x-text-input
            type="date"
            name="{{ $startName }}"
            id="{{ $startName }}"
            value="{{ $startValue }}"
            class="mt-1 block w-full"
        />
    </div>

    <div class="flex-1">
        <x-input-label for="{{ $endName }}" value="Date fin" />
        <x-text-input
            type="date"
            name="{{ $endName }}"
            id="{{ $endName }}"
            value="{{ $endValue }}"
            class="mt-1 block w-full"
        />
    </div>
</div>

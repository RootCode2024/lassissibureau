@props([
    'title',
    'value',
    'subtitle' => null,
    'icon',
    'iconColor' => 'indigo', // indigo, emerald, purple, amber, rose
])

@php
$iconColors = [
    'indigo' => 'bg-indigo-100 text-indigo-600',
    'emerald' => 'bg-emerald-100 text-emerald-600',
    'purple' => 'bg-purple-100 text-purple-600',
    'amber' => 'bg-amber-100 text-amber-600',
    'rose' => 'bg-rose-100 text-rose-600',
];
@endphp

<x-card>
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-600">{{ $title }}</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $value }}</p>
            @if($subtitle)
                <p class="text-sm text-gray-500 mt-2">{{ $subtitle }}</p>
            @endif
        </div>
        <div class="w-12 h-12 {{ $iconColors[$iconColor] }} rounded-lg flex items-center justify-center flex-shrink-0">
            <i data-lucide="{{ $icon }}" class="w-6 h-6"></i>
        </div>
    </div>
</x-card>

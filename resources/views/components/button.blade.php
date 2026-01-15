@props([
    'variant' => 'primary', // primary, secondary, danger, ghost
    'size' => 'md', // sm, md, lg
    'type' => 'button',
    'href' => null,
])

@php
$variants = [
    'primary' => 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm',
    'secondary' => 'bg-white hover:bg-gray-50 text-gray-900 border border-gray-300 shadow-sm',
    'danger' => 'bg-rose-600 hover:bg-rose-700 text-white shadow-sm',
    'ghost' => 'hover:bg-gray-100 text-gray-700',
];

$sizes = [
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-6 py-3 text-base',
];

$classes = $variants[$variant] . ' ' . $sizes[$size];
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "inline-flex items-center justify-center gap-2 rounded-lg font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {$classes}"]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => "inline-flex items-center justify-center gap-2 rounded-lg font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed {$classes}"]) }}>
        {{ $slot }}
    </button>
@endif

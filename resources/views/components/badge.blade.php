@props([
    'variant' => 'default', // default, success, warning, danger, info, purple
    'size' => 'md', // sm, md, lg
])

@php
$variants = [
    'default' => 'bg-gray-100 text-gray-800',
    'success' => 'bg-emerald-100 text-emerald-800',
    'warning' => 'bg-amber-100 text-amber-800',
    'danger' => 'bg-rose-100 text-rose-800',
    'info' => 'bg-blue-100 text-blue-800',
    'purple' => 'bg-purple-100 text-purple-800',
    'indigo' => 'bg-indigo-100 text-indigo-800',
];

$sizes = [
    'sm' => 'px-2 py-0.5 text-xs',
    'md' => 'px-2.5 py-1 text-xs',
    'lg' => 'px-3 py-1.5 text-sm',
];

$classes = $variants[$variant] . ' ' . $sizes[$size];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 rounded-full font-medium {$classes}"]) }}>
    {{ $slot }}
</span>

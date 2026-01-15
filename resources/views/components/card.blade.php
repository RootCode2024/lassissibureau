@props([
    'padding' => 'p-6',
    'shadow' => true,
])

<div {{ $attributes->merge(['class' => "bg-white rounded-xl border border-gray-200 {$padding} " . ($shadow ? 'shadow-sm' : '')]) }}>
    {{ $slot }}
</div>

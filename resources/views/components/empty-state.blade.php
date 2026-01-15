@props([
    'icon' => 'inbox',
    'title',
    'description' => null,
    'action' => null,
])

<div {{ $attributes->merge(['class' => 'text-center py-12']) }}>
    <i data-lucide="{{ $icon }}" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $title }}</h3>
    @if($description)
        <p class="text-sm text-gray-600 mb-6 max-w-sm mx-auto">{{ $description }}</p>
    @endif
    @if($action)
        <div>{{ $action }}</div>
    @endif
</div>

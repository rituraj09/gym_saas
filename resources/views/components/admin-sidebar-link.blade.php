@props(['href', 'icon', 'label', 'active' => false])
@php
    $baseClasses =
        'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 min-h-[44px]';
    $activeClasses = 'bg-indigo-50 text-indigo-700 shadow-sm';
    $inactiveClasses = 'text-gray-600 hover:bg-gray-100 hover:text-gray-900';
@endphp
<a href="{{ $href }}" class="{{ $baseClasses }} {{ $active ? $activeClasses : $inactiveClasses }}">
    <x-icon :name="$icon" class="w-5 h-5 flex-shrink-0" />
    <span class="truncate">{{ $label }}</span>
</a>

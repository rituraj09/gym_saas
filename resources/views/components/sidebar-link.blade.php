@props(['href', 'icon', 'label', 'active' => false])

<a href="{{ $href }}" @if ($active) aria-current="page" @endif
    class="group relative flex items-center gap-3 px-3.5 py-3 rounded-xl
          text-sm font-medium transition-all duration-200
          {{ $active ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">

    @if ($active)
        <span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-indigo-600"></span>
    @endif

    <x-icon name="{{ $icon }}"
        class="w-5 h-5 shrink-0 transition-colors
                   {{ $active ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-700' }}" />

    <span class="truncate">{{ $label }}</span>

    @if ($active)
        <x-icon name="chevron-right" class="w-4 h-4 ml-auto text-indigo-400" />
    @endif

</a>

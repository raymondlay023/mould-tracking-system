@props(['active'])

@php
$classes = ($active ?? false)
            ? 'group flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-blue-600 bg-blue-50 rounded-xl transition-all duration-200 border-l-4 border-blue-600'
            : 'group flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-xl transition-all duration-200 border-l-4 border-transparent';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @isset($icon)
        <span class="w-5 h-5 flex-shrink-0 transition-colors duration-200 {{ $active ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600' }}">
            {{ $icon }}
        </span>
    @endisset
    <span class="truncate">{{ $slot }}</span>
</a>

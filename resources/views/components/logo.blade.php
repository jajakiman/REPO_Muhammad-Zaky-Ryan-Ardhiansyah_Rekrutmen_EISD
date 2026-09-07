@props([
    'variant' => 'full',
    'size' => 'md',
    'textColor' => 'white',
])

@php
    $dimensions = match($size) {
        'sm' => ['mark' => 'w-7 h-7', 'title' => 'text-base', 'sub' => 'text-[10px]'],
        'lg' => ['mark' => 'w-12 h-12', 'title' => 'text-2xl', 'sub' => 'text-xs'],
        default => ['mark' => 'w-9 h-9', 'title' => 'text-lg', 'sub' => 'text-[11px]'],
    };
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }}>
    <svg class="{{ $dimensions['mark'] }} shrink-0 shadow-sm rounded-xl" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" aria-hidden="true">
        <rect width="48" height="48" rx="12" fill="#1E3A8A" />
        <path d="M24 7C17.37 7 12 12.37 12 19c0 8.8 10.2 19.4 12 20.9 1.8-1.5 12-12.1 12-20.9 0-6.63-5.37-12-12-12z" fill="#172554" opacity="0.65" />
        <circle cx="24" cy="14.5" r="3.2" fill="#EA580C" />
        <path d="M24 19.5v6.5l4.5 4" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
        <path d="M19 24a6 6 0 1 0 6 6" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round" />
        <circle cx="32" cy="18" r="1.75" fill="#EA580C" />
    </svg>

    @if($variant === 'full')
        <div class="flex flex-col leading-tight">
            <span class="font-extrabold tracking-tight {{ $dimensions['title'] }} {{ $textColor === 'white' ? 'text-white' : 'text-slate-900' }}">
                Akses<span class="text-orange-500">Loka</span>
            </span>
            <span class="font-semibold tracking-wider uppercase {{ $dimensions['sub'] }} {{ $textColor === 'white' ? 'text-slate-300' : 'text-slate-500' }}">
                Aksesibilitas Kampus
            </span>
        </div>
    @endif
</div>

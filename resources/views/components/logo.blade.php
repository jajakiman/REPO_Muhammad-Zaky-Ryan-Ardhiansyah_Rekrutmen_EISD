@props([
    'variant' => 'full',
    'size' => 'md',
    'textColor' => 'white',
])

@php
    $dimensions = match($size) {
        'sm' => ['mark' => 'w-7 h-7', 'horiz' => 'h-7 w-auto', 'badge' => 'w-16 h-16', 'title' => 'text-base', 'sub' => 'text-[10px]'],
        'lg' => ['mark' => 'w-12 h-12', 'horiz' => 'h-12 w-auto', 'badge' => 'w-24 h-24', 'title' => 'text-2xl', 'sub' => 'text-xs'],
        default => ['mark' => 'w-9 h-9', 'horiz' => 'h-9 w-auto', 'badge' => 'w-20 h-20', 'title' => 'text-lg', 'sub' => 'text-[11px]'],
    };
@endphp

@if($variant === 'badge')
    <picture class="inline-block shrink-0">
        <source srcset="{{ asset('images/logo-badge.webp') }}" type="image/webp">
        <img src="{{ asset('images/logo-badge.png') }}" alt="AksesLoka Badge" class="{{ $dimensions['badge'] }} object-contain drop-shadow-md" width="512" height="512" loading="eager">
    </picture>
@elseif($variant === 'horizontal')
    <picture class="inline-block shrink-0">
        <source srcset="{{ asset('images/logo-horizontal.webp') }}" type="image/webp">
        <img src="{{ asset('images/logo-horizontal.png') }}" alt="AksesLoka - Pelaporan Fasilitas Kampus" class="{{ $dimensions['horiz'] }} object-contain" width="720" height="180" loading="eager">
    </picture>
@elseif($variant === 'mark')
    <picture class="inline-block shrink-0">
        <source srcset="{{ asset('images/logo-mark.webp') }}" type="image/webp">
        <img src="{{ asset('images/logo-mark.png') }}" alt="AksesLoka Logo" class="{{ $dimensions['mark'] }} object-contain rounded-xl shadow-sm" width="512" height="512" loading="eager">
    </picture>
@else
    <div {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }}>
        <picture class="inline-block shrink-0">
            <source srcset="{{ asset('images/logo-mark.webp') }}" type="image/webp">
            <img src="{{ asset('images/logo-mark.png') }}" alt="AksesLoka Logo" class="{{ $dimensions['mark'] }} object-contain rounded-xl shadow-sm" width="512" height="512" loading="eager">
        </picture>

        <div class="flex flex-col leading-tight">
            <span class="font-extrabold tracking-tight {{ $dimensions['title'] }} {{ $textColor === 'white' ? 'text-white' : 'text-slate-900' }}">
                Akses<span class="text-orange-500">Loka</span>
            </span>
            <span class="font-semibold tracking-wider uppercase {{ $dimensions['sub'] }} {{ $textColor === 'white' ? 'text-slate-300' : 'text-slate-500' }}">
                Pelaporan Fasilitas Kampus
            </span>
        </div>
    </div>
@endif

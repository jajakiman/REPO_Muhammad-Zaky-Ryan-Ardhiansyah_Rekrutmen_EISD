@props(['href', 'variant' => 'light'])

<a
    href="{{ $href }}"
    data-back-link
    {{ $attributes->class([
        'inline-flex min-h-11 items-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-semibold no-underline transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-600',
        'border-slate-300 bg-white text-navy-900 hover:border-navy-900 hover:bg-slate-50' => $variant === 'light',
        'border-white/20 bg-white/10 text-slate-200 hover:bg-white/20 hover:text-white' => $variant === 'dark',
    ]) }}
>
    <svg class="h-4 w-4 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" />
    </svg>
    <span>{{ $slot }}</span>
</a>

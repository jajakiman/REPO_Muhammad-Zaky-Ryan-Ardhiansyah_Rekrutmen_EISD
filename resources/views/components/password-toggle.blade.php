@props(['target'])

<button
    type="button"
    data-password-toggle="{{ $target }}"
    aria-label="Tampilkan password"
    aria-pressed="false"
    class="absolute inset-y-0 right-0 inline-flex min-h-11 min-w-11 items-center justify-center rounded-r-lg text-slate-500 transition-colors hover:text-navy-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-orange-600"
>
    <svg data-eye-open class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.04 12.32a1 1 0 0 1 0-.64C3.42 7.51 7.35 5 12 5s8.58 2.51 9.96 6.68a1 1 0 0 1 0 .64C20.58 16.49 16.65 19 12 19s-8.58-2.51-9.96-6.68Z" />
        <circle cx="12" cy="12" r="3" />
    </svg>
    <svg data-eye-closed class="hidden h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18M10.58 10.59a2 2 0 0 0 2.83 2.83M9.88 4.24A10.9 10.9 0 0 1 12 4c4.65 0 8.58 2.51 9.96 6.68a1 1 0 0 1 0 .64 10.7 10.7 0 0 1-2.16 3.67M6.61 6.61A10.5 10.5 0 0 0 2.04 11.68a1 1 0 0 0 0 .64C3.42 16.49 7.35 19 12 19a10.9 10.9 0 0 0 5.39-1.39" />
    </svg>
</button>

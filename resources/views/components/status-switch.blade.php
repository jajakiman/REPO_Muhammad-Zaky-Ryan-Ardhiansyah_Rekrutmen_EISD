@props(['action', 'checked', 'label', 'disabled' => false, 'disabledReason' => null])

<form method="post" action="{{ $action }}" class="inline-flex" data-status-switch>
    @csrf
    @method('patch')
    <input type="hidden" name="is_active" value="0">
    <label class="inline-flex min-h-11 items-center gap-2 {{ $disabled ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }}" @if($disabledReason) title="{{ $disabledReason }}" @endif>
        <input
            type="checkbox"
            name="is_active"
            value="1"
            class="peer sr-only"
            @checked($checked)
            @disabled($disabled)
            aria-label="Status aktif {{ $label }}"
            onchange="this.form.requestSubmit()"
        >
        <span class="relative h-6 w-11 shrink-0 rounded-full bg-slate-400 transition-colors after:absolute after:start-1 after:top-1 after:h-4 after:w-4 after:rounded-full after:bg-white after:transition-transform after:content-[''] peer-checked:bg-emerald-700 peer-checked:after:translate-x-5 peer-focus-visible:ring-4 peer-focus-visible:ring-emerald-200"></span>
        <span class="text-sm font-semibold text-slate-700">{{ $checked ? 'Aktif' : 'Nonaktif' }}</span>
    </label>
</form>

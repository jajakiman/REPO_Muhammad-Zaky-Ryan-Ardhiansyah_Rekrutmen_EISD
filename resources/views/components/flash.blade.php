@foreach (['success' => 'positive', 'error' => 'critical'] as $key => $tone)
    @if (session()->has($key))
        @if ($key === 'success' && session('success_modal'))
        <dialog data-success-dialog @if (session('success_modal_auto_close')) data-auto-close="3000" @endif aria-labelledby="success-dialog-title" aria-describedby="success-dialog-message" class="fixed inset-0 m-auto max-h-[calc(100dvh-2rem)] w-[min(calc(100%-2rem),28rem)] overflow-y-auto rounded-2xl border-0 bg-white p-0 shadow-2xl backdrop:bg-slate-950/70">
            <div class="p-6 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-emerald-800" aria-hidden="true">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                </div>
                <h2 id="success-dialog-title" class="mt-4 text-xl font-bold text-slate-950">Berhasil</h2>
                <p id="success-dialog-message" class="mt-2 text-sm leading-6 text-slate-600">{{ session($key) }}</p>
                <form method="dialog" class="mt-6">
                    <button class="button button-primary min-w-24" type="submit" autofocus>OK</button>
                </form>
            </div>
        </dialog>
        <script>
            (function () {
                var dialog = document.currentScript.previousElementSibling;
                dialog.showModal();
                var delay = Number(dialog.dataset.autoClose);
                if (delay) window.setTimeout(function () { if (dialog.open) dialog.close(); }, delay);
            })();
        </script>
        @else
        <div class="mb-6 flex items-start justify-between gap-3.5 p-4 rounded-2xl border shadow-sm transition-all duration-200 flash flash-{{ $tone }}" role="{{ $key === 'error' ? 'alert' : 'status' }}">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 mt-0.5 {{ $key === 'success' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                    @if ($key === 'success')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    @endif
                </div>
                <div class="flex-1 text-sm leading-relaxed">
                    <p class="font-bold {{ $key === 'success' ? 'text-emerald-950' : 'text-red-950' }}">
                        {{ $key === 'success' ? 'Berhasil' : 'Terjadi Kesalahan' }}
                    </p>
                    <p class="mt-0.5 {{ $key === 'success' ? 'text-emerald-900' : 'text-red-900' }}">
                        {{ session($key) }}
                    </p>
                </div>
            </div>
            <button type="button" onclick="this.closest('.flash').remove()" class="p-1 rounded-lg text-slate-400 hover:text-slate-700 transition-colors" aria-label="Tutup notifikasi">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        @endif
    @endif
@endforeach

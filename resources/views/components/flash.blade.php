@foreach (['success' => 'positive', 'error' => 'critical'] as $key => $tone)
    @if (session()->has($key))
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
@endforeach

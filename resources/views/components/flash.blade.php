@foreach (['success' => 'positive', 'error' => 'critical'] as $key => $tone)
    @if (session()->has($key))
        <div class="flash flash-{{ $tone }}" role="status">
            <strong>{{ $key === 'success' ? 'Berhasil:' : 'Terjadi masalah:' }}</strong>
            {{ session($key) }}
        </div>
    @endif
@endforeach

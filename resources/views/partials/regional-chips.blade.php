<div class="chips" role="tablist" aria-label="Pilih regional">
    @foreach(config('hrd.regionals') as $r)
        <a href="{{ request()->fullUrlWithQuery(['regional' => $r]) }}"
           class="chip {{ $r === $regional ? 'active' : '' }}">{{ $r }}</a>
    @endforeach
</div>

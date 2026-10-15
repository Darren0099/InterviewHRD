@php
    $meta   = config("hrd.divisi.{$c->divisi}", ['icon' => 'user', 'color' => '#ede9fe', 'kuota' => 0]);
    $kuota  = (int) ($meta['kuota'] ?? 0);
    $isTop  = $rank <= $kuota;
    $max    = (int) config('hrd.max_score', 25);
    $payload = [
        'id' => $c->id, 'regional' => $c->regional, 'divisi' => $c->divisi,
        'nama_kandidat' => $c->nama_kandidat, 'nama_hrd' => $c->nama_hrd,
        'aspek_teknis' => $c->aspek_teknis, 'aspek_komunikasi' => $c->aspek_komunikasi,
        'aspek_sikap' => $c->aspek_sikap, 'aspek_motivasi' => $c->aspek_motivasi,
        'catatan' => $c->catatan,
    ];
@endphp
<article class="cand {{ $isTop ? 'is-top' : '' }}"
         data-divisi="{{ $c->divisi }}"
         data-search="{{ mb_strtolower($c->nama_kandidat.' '.$c->nama_hrd.' '.$c->divisi) }}">

    <div class="cand-head">
        <div class="avatar" style="background: {{ $meta['color'] }}">{{ $c->initials }}</div>
        <div class="cand-id">
            <h3>@if($rank <= 3)@include('partials.medal', ['rank' => $rank]) @endif{{ $c->nama_kandidat }}</h3>
            <p>{{ $c->divisi }} · Peringkat #{{ $rank }}</p>
        </div>
        <div class="ring" style="--p: {{ min(100, $c->total) }}" title="Total {{ $c->total }}/100">
            <span>{{ $c->total }}</span>
        </div>
    </div>

    <div class="aspects">
        @foreach(['aspek_teknis' => 'Teknis', 'aspek_komunikasi' => 'Komunikasi', 'aspek_sikap' => 'Sikap', 'aspek_motivasi' => 'Motivasi'] as $f => $l)
            <div class="asp">
                <span>{{ $l }}</span>
                <div class="bar"><i style="width: {{ $max ? min(100, $c->$f / $max * 100) : 0 }}%"></i></div>
                <b>{{ $c->$f }}</b>
            </div>
        @endforeach
    </div>

    <p class="cand-meta">
        <span>@include('partials.icon', ['n' => 'user']) HRD: <b>{{ $c->nama_hrd }}</b></span>
        <span>@include('partials.icon', ['n' => 'clock']) {{ $c->created_at ? $c->created_at->translatedFormat('d M Y, H:i') : '-' }}</span>
    </p>

    @if($c->catatan)
        <p class="note">{{ $c->catatan }}</p>
    @endif

    <div class="cand-foot">
        <span class="pill {{ $isTop ? 'pill-accent' : '' }}">@if($isTop)@include('partials.icon', ['n' => 'star']) TOP {{ $kuota }}@else Tier {{ $c->tier }}@endif</span>
        <div class="cand-actions">
            <button type="button" class="btn btn-ghost btn-sm" data-action="edit" data-edit="{{ json_encode($payload) }}">
                @include('partials.icon', ['n' => 'edit']) Edit
            </button>
            <button type="button" class="btn btn-danger-soft btn-sm" data-action="delete" data-id="{{ $c->id }}" data-nama="{{ $c->nama_kandidat }}">
                @include('partials.icon', ['n' => 'trash']) Hapus
            </button>
        </div>
    </div>
</article>

<div class="top-pop">
    <div class="top-pop-head">
        <span class="top-pop-ico">@include('partials.icon', ['n' => config("hrd.divisi.{$divisi}.icon", 'users')])</span>
        <div>
            <h3>TOP {{ $kuota }} · {{ $divisi }}</h3>
            <p>Regional {{ $regional }}</p>
        </div>
    </div>

    @forelse($list as $i => $d)
        <div class="top-row">
            <div class="top-rank">@include('partials.medal', ['rank' => $i + 1])</div>
            <div class="top-body">
                <b>{{ $d->nama_kandidat }}</b>
                <small>Tek {{ $d->aspek_teknis }} · Kom {{ $d->aspek_komunikasi }} · Sik {{ $d->aspek_sikap }} · Mot {{ $d->aspek_motivasi }}</small>
                <small>HRD: {{ $d->nama_hrd }}</small>
                @if($d->catatan)<em>{{ $d->catatan }}</em>@endif
            </div>
            <div class="top-score">{{ $d->total }}</div>
        </div>
    @empty
        <p class="top-empty">Belum ada kandidat di divisi ini.</p>
    @endforelse
</div>

@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<header class="page-head">
    <div>
        <p class="eyebrow">Selamat datang kembali</p>
        <h1>Dashboard Interview HRD</h1>
    </div>
</header>

@include('partials.regional-chips')

{{-- ===== Hero: level & misi ===== --}}
<section class="hero">
    <div class="hero-main">
        <span class="hero-tag">LEVEL {{ $level['lv'] }}</span>
        <h2>{{ $level['title'] }}</h2>
        <p>Misi rekrutmen <b>{{ $regional }}</b>: isi semua slot TOP untuk menyelesaikan misi.</p>

        <div class="xp-line">
            <span>XP {{ $level['xp'] }}/100</span>
            <div class="xp xp-dark"><i style="width: {{ $level['xp'] }}%"></i></div>
        </div>
        <div class="xp-line">
            <span>Misi {{ $stat['top'] }}/{{ $stat['slot'] }} slot TOP</span>
            <div class="xp xp-dark"><i style="width: {{ $stat['misi'] }}%"></i></div>
        </div>

        <button type="button" class="btn btn-accent" data-action="open-form">
            @include('partials.icon', ['n' => 'plus']) Mulai Interview
        </button>
    </div>
    <div class="hero-trophy" aria-hidden="true">@include('partials.icon', ['n' => $stat['misi'] >= 100 ? 'trophy' : 'target'])</div>
</section>

{{-- ===== Statistik ===== --}}
<section class="stats">
    <div class="tile">
        <span class="tile-ico">@include('partials.icon', ['n' => 'users'])</span>
        <strong data-count="{{ $stat['total'] }}">{{ $stat['total'] }}</strong>
        <small>Total Pendaftar</small>
    </div>
    <div class="tile">
        <span class="tile-ico">@include('partials.icon', ['n' => 'star'])</span>
        <strong data-count="{{ $stat['top'] }}">{{ $stat['top'] }}</strong>
        <small>Kandidat TOP</small>
    </div>
    <div class="tile">
        <span class="tile-ico">@include('partials.icon', ['n' => 'trending'])</span>
        <strong>{{ $stat['avg'] }}</strong>
        <small>Rata-rata Skor</small>
    </div>
    <div class="tile">
        <span class="tile-ico">@include('partials.icon', ['n' => 'award'])</span>
        <strong>{{ $stat['penuh'] }}<em>/{{ $stat['divisi'] }}</em></strong>
        <small>Divisi Kuota Penuh</small>
    </div>
</section>

@if($best)
    <section class="card best">
        <div class="best-medal">@include('partials.icon', ['n' => 'award'])</div>
        <div>
            <small>Skor tertinggi di {{ $regional }}</small>
            <h3>{{ $best->nama_kandidat }}</h3>
            <p>{{ $best->divisi }}</p>
        </div>
        <div class="ring" style="--p: {{ min(100, $best->total) }}"><span>{{ $best->total }}</span></div>
    </section>
@endif

{{-- ===== Divisi ===== --}}
<h2 class="section-title">Progress Divisi</h2>
<section class="div-grid">
    @foreach($divisi as $d)
        <article class="div-card">
            <div class="div-top">
                <span class="div-ico" style="background: {{ $d['color'] }}">@include('partials.icon', ['n' => $d['icon']])</span>
                <div>
                    <h3>{{ $d['nama'] }}</h3>
                    <small>Kuota TOP {{ $d['kuota'] }}</small>
                </div>
            </div>

            <div class="div-num"><strong>{{ $d['jumlah'] }}</strong><span>pendaftar</span></div>

            <div class="xp"><i style="width: {{ $d['progress'] }}%"></i></div>
            <p class="div-status {{ $d['penuh'] ? 'ok' : '' }}">
                @if($d['penuh'])@include('partials.icon', ['n' => 'check']) Kuota terpenuhi @else Butuh {{ $d['kurang'] }} lagi @endif
            </p>

            @if($d['best'])
                <p class="div-best">Terbaik: {{ $d['best']->nama_kandidat }} <b>{{ $d['best']->total }}</b></p>
            @else
                <p class="div-best muted">Belum ada kandidat</p>
            @endif

            <button type="button" class="btn btn-soft btn-block" data-action="top"
                    data-divisi="{{ $d['nama'] }}" data-regional="{{ $regional }}">
                Lihat TOP {{ $d['kuota'] }}
            </button>
        </article>
    @endforeach
</section>

{{-- ===== Grafik + Leaderboard ===== --}}
<section class="two-col">
    <div class="card">
        <h2 class="card-title">Pendaftar vs Kuota</h2>
        <div class="chart-wrap"><canvas id="chartDivisi" aria-label="Grafik pendaftar per divisi"></canvas></div>
    </div>

    <div class="card">
        <h2 class="card-title">Leaderboard HRD</h2>
        @forelse($hrBoard as $i => $h)
            <div class="lb-row">
                <span class="lb-rank">@include('partials.medal', ['rank' => $i + 1])</span>
                <div class="lb-body">
                    <b>{{ $h['nama'] }}</b>
                    <small>Rata-rata skor diberikan {{ $h['avg'] }}</small>
                </div>
                <span class="pill pill-accent">{{ $h['jumlah'] }} interview</span>
            </div>
        @empty
            <p class="muted">Belum ada interview. Jadilah yang pertama!</p>
        @endforelse
    </div>
</section>

{{-- ===== Badge ===== --}}
<h2 class="section-title">Pencapaian</h2>
<section class="badges">
    @foreach($badges as $b)
        <div class="badge-card {{ $b['on'] ? 'on' : 'off' }}">
            <span class="badge-ico">@include('partials.icon', ['n' => $b['icon']])</span>
            <b>{{ $b['nama'] }}</b>
            <small>{{ $b['desc'] }}</small>
        </div>
    @endforeach
</section>

{{-- ===== Zona berbahaya ===== --}}
<section class="danger-zone">
    <div>
        <b>Reset data regional</b>
        <small>Menghapus seluruh penilaian di {{ $regional }}. Tidak bisa dibatalkan.</small>
    </div>
    <button type="button" class="btn btn-danger-soft" data-action="delete-regional" data-regional="{{ $regional }}">
        @include('partials.icon', ['n' => 'trash']) Hapus Semua
    </button>
</section>
@endsection

@push('vendor')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
@endpush

@push('scripts')
<script>
    (function () {
        if (typeof Chart === 'undefined') return;
        var el = document.getElementById('chartDivisi');
        var mobile = window.innerWidth < 700;
        new Chart(el, {
            type: 'bar',
            data: {
                labels: @json($divisi->pluck('nama')),
                datasets: [
                    { label: 'Pendaftar', data: @json($divisi->pluck('jumlah')), backgroundColor: '#7c3aed', borderRadius: 8 },
                    { label: 'Kuota TOP', data: @json($divisi->pluck('kuota')), backgroundColor: '#c4b5fd', borderRadius: 8 }
                ]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { labels: { font: { size: mobile ? 14 : 12, family: 'Plus Jakarta Sans' } } } },
                scales: {
                    x: { beginAtZero: true, ticks: { precision: 0, font: { size: mobile ? 13 : 12 } } },
                    y: { ticks: { font: { size: mobile ? 13 : 12 } } }
                }
            }
        });
    })();
</script>
@endpush

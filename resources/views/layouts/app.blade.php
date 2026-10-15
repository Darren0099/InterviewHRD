<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1b1432">
    <title>@yield('title', 'Dashboard') · Interview HRD</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v=2">
</head>
<body>
@php $regional = $regional ?? session('regional', config('hrd.regionals')[0]); @endphp

<div class="app">

    {{-- ===== Sidebar (desktop) ===== --}}
    <aside class="sidebar">
        <div class="brand">
            <span class="brand-logo">HR</span>
            <span>Interview<b>HRD</b></span>
        </div>

        <nav class="side-nav">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">@include('partials.icon', ['n' => 'grid']) Dashboard</a>
            <a href="{{ route('kandidat') }}" class="{{ request()->routeIs('kandidat') ? 'active' : '' }}">@include('partials.icon', ['n' => 'users']) Kandidat</a>
            <a href="{{ route('unduh') }}" class="{{ request()->routeIs('unduh') ? 'active' : '' }}">@include('partials.icon', ['n' => 'download']) Unduh Data</a>
        </nav>

        <button type="button" class="btn btn-accent btn-block" data-action="open-form">
            @include('partials.icon', ['n' => 'plus']) Nilai Kandidat
        </button>

        @if(session('hrd_auth'))
            <form method="post" action="{{ route('logout') }}" class="side-logout">
                @csrf
                <button type="submit" class="btn btn-ghost-dark btn-block">@include('partials.icon', ['n' => 'logout']) Keluar</button>
            </form>
        @endif

        <div class="side-foot">
            <span>Youth Ranger Indonesia</span>
            <small>Regional aktif: {{ $regional }}</small>
        </div>
    </aside>

    {{-- ===== Konten ===== --}}
    <main class="main">
        @if(session('hrd_auth'))
            <form method="post" action="{{ route('logout') }}" class="logout-mini">
                @csrf
                <button type="submit" aria-label="Keluar">@include('partials.icon', ['n' => 'logout'])</button>
            </form>
        @endif
        @yield('content')
    </main>
</div>

{{-- ===== Bottom nav (HP) ===== --}}
<nav class="bottom-nav" aria-label="Menu utama">
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
        @include('partials.icon', ['n' => 'grid'])<span>Dashboard</span>
    </a>
    <a href="{{ route('kandidat') }}" class="{{ request()->routeIs('kandidat') ? 'active' : '' }}">
        @include('partials.icon', ['n' => 'users'])<span>Kandidat</span>
    </a>
    <button type="button" class="bn-fab" data-action="open-form" aria-label="Nilai kandidat baru">
        @include('partials.icon', ['n' => 'plus'])
    </button>
    <a href="{{ route('unduh') }}" class="{{ request()->routeIs('unduh') ? 'active' : '' }}">
        @include('partials.icon', ['n' => 'download'])<span>Unduh</span>
    </a>
</nav>

{{-- ===== Modal form penilaian ===== --}}
<div class="modal" id="formModal" aria-hidden="true">
    <div class="sheet" role="dialog" aria-modal="true" aria-labelledby="formTitle">
        <div class="sheet-head">
            <h2 id="formTitle">Nilai Kandidat</h2>
            <button type="button" class="icon-btn" data-action="close-form" aria-label="Tutup">@include('partials.icon', ['n' => 'x'])</button>
        </div>

        <form id="penilaianForm" autocomplete="off" novalidate>
            <div class="grid-2">
                <label class="field">
                    <span>Regional</span>
                    <select name="regional" class="input" required>
                        @foreach(config('hrd.regionals') as $r)
                            <option value="{{ $r }}">{{ $r }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="field">
                    <span>Divisi</span>
                    <select name="divisi" class="input" required>
                        @foreach(config('hrd.divisi') as $nama => $m)
                            <option value="{{ $nama }}">{{ $nama }} (TOP {{ $m['kuota'] }})</option>
                        @endforeach
                    </select>
                </label>
            </div>

            <label class="field">
                <span>Nama Kandidat</span>
                <input type="text" name="nama_kandidat" class="input" placeholder="Nama lengkap kandidat" maxlength="150" required>
            </label>

            <label class="field">
                <span>Nama HRD (pewawancara)</span>
                <input type="text" name="nama_hrd" class="input" placeholder="Nama kamu" maxlength="150" required>
            </label>

            <div class="aspects-form">
                @foreach([
                    'aspek_teknis'     => ['tool', 'Teknis'],
                    'aspek_komunikasi' => ['message', 'Komunikasi'],
                    'aspek_sikap'      => ['heart', 'Sikap'],
                    'aspek_motivasi'   => ['zap', 'Motivasi'],
                ] as $name => [$emo, $label])
                    <div class="aspect-row">
                        <div class="aspect-top">
                            <span>@include('partials.icon', ['n' => $emo]) {{ $label }}</span>
                            <b><output data-out="{{ $name }}">0</output><small>/{{ config('hrd.max_score') }}</small></b>
                        </div>
                        <input type="range" name="{{ $name }}" min="0" max="{{ config('hrd.max_score') }}" step="1" value="0">
                    </div>
                @endforeach
            </div>

            <div class="total-box">
                <div class="total-row">
                    <span>Total Skor</span>
                    <div><strong id="totalNum">0</strong><small>/100</small> <span class="tier" id="tierBadge">Tier D</span></div>
                </div>
                <div class="xp"><i id="totalBar"></i></div>
                <p id="tierMsg">Geser slider untuk memberi nilai.</p>
            </div>

            <label class="field">
                <span>Catatan</span>
                <textarea name="catatan" class="input" rows="3" maxlength="2000" placeholder="Kesan, kelebihan, hal yang perlu diperhatikan…"></textarea>
            </label>

            <button type="submit" id="saveBtn" class="btn btn-dark btn-block btn-lg">Simpan Penilaian</button>
        </form>
    </div>
</div>

<script>
    window.HRD = {
        base: @json(rtrim(url('/'), '/')),
        regional: @json($regional),
        regionals: @json(config('hrd.regionals')),
        divisi: @json(config('hrd.divisi')),
    };
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stack('vendor')
<script src="{{ asset('js/app.js') }}?v=1"></script>
@stack('scripts')
</body>
</html>

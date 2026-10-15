@extends('layouts.app')
@section('title', 'Unduh Data')

@section('content')
<header class="page-head">
    <div>
        <p class="eyebrow">Laporan lengkap + waktu unduh</p>
        <h1>Unduh Data</h1>
    </div>
</header>

<section class="card export-card">
    <form method="get" action="{{ route('export.excel') }}">
        <label class="field">
            <span>Pilih regional</span>
            <select name="regional" class="input" id="exportRegional">
                <option value="semua">Semua Regional ({{ $counts->sum() }} data)</option>
                @foreach(config('hrd.regionals') as $r)
                    <option value="{{ $r }}" {{ $r === $regional ? 'selected' : '' }}>{{ $r }} ({{ $counts[$r] ?? 0 }} data)</option>
                @endforeach
            </select>
        </label>

        <div class="export-grid">
            <button type="submit" class="export-btn excel" formaction="{{ route('export.excel') }}">
                <span class="export-ico">@include('partials.icon', ['n' => 'sheet'])</span>
                <b>Excel (.xlsx)</b>
                <small>Tabel lengkap, bisa difilter</small>
            </button>
            <button type="submit" class="export-btn pdf" formaction="{{ route('export.pdf') }}">
                <span class="export-ico">@include('partials.icon', ['n' => 'file'])</span>
                <b>PDF</b>
                <small>Laporan siap cetak per divisi</small>
            </button>
        </div>
    </form>

    <ul class="export-notes">
        <li>@include('partials.icon', ['n' => 'clock']) <span>Setiap file memuat <b>tanggal &amp; jam unduh</b> saat ini serta <b>waktu input</b> tiap kandidat.</span></li>
        <li>@include('partials.icon', ['n' => 'star']) <span>Kandidat TOP sesuai kuota divisi ditandai di dalam file.</span></li>
        <li>@include('partials.icon', ['n' => 'smartphone']) <span>Di HP, file akan masuk ke folder Unduhan.</span></li>
    </ul>
</section>
@endsection

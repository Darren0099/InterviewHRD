@extends('layouts.app')
@section('title', 'Kandidat')

@section('content')
<header class="page-head">
    <div>
        <p class="eyebrow">{{ $rows->count() }} kandidat terdaftar</p>
        <h1>Data Kandidat</h1>
    </div>
    <a href="{{ route('unduh') }}" class="btn btn-ghost hide-mobile">@include('partials.icon', ['n' => 'download']) Unduh</a>
</header>

@include('partials.regional-chips')

<div class="search">
    @include('partials.icon', ['n' => 'search'])
    <input type="search" id="q" class="input" placeholder="Cari nama kandidat, HRD, atau divisi…" autocomplete="off">
</div>

<div class="chips chips-filter" id="divisiFilter">
    <button type="button" class="chip active" data-filter="">Semua <span>{{ $rows->count() }}</span></button>
    @foreach(config('hrd.divisi') as $nama => $m)
        <button type="button" class="chip" data-filter="{{ $nama }}">@include('partials.icon', ['n' => $m['icon']]) {{ $nama }} <span>{{ $counts[$nama] ?? 0 }}</span></button>
    @endforeach
</div>

@if($rows->isEmpty())
    <div class="empty">
        <div class="empty-ico">@include('partials.icon', ['n' => 'folder'])</div>
        <h3>Belum ada kandidat di {{ $regional }}</h3>
        <p>Tekan tombol + untuk menilai kandidat pertama.</p>
        <button type="button" class="btn btn-dark" data-action="open-form">Nilai Kandidat</button>
    </div>
@else
    <section class="cand-grid" id="candGrid">
        @foreach($rows as $c)
            @include('partials.candidate-card', ['c' => $c, 'rank' => $ranks[$c->id] ?? 999])
        @endforeach
    </section>
    <div class="empty" id="noResult" hidden>
        <div class="empty-ico">@include('partials.icon', ['n' => 'search'])</div>
        <h3>Tidak ada hasil</h3>
        <p>Coba kata kunci atau divisi yang lain.</p>
    </div>
@endif
@endsection

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Penilaian Interview HRD</title>
<style>
    @page { margin: 95px 28px 55px 28px; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 9.5px; color: #1b1b25; }
    header { position: fixed; top: -78px; left: 0; right: 0; }
    footer { position: fixed; bottom: -38px; left: 0; right: 0; font-size: 8.5px; color: #777; border-top: 1px solid #ddd; padding-top: 5px; }
    h1 { font-size: 18px; margin: 0 0 3px 0; }
    .sub { font-size: 10.5px; margin: 0; color: #333; }
    .stamp { display: inline-block; margin-top: 6px; background: #1b1432; color: #c4b5fd; padding: 4px 10px; border-radius: 10px; font-size: 9.5px; }
    h2 { font-size: 13px; margin: 14px 0 2px 0; color: #1b1432; }
    h3 { font-size: 11px; margin: 10px 0 4px 0; }
    h3 small { font-weight: normal; color: #666; font-size: 9px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
    th { background: #1b1432; color: #fff; padding: 5px 4px; font-size: 8.5px; text-align: center; }
    td { border: 1px solid #ddddd5; padding: 4px; vertical-align: top; }
    .c { text-align: center; }
    .top td { background: #f1ebff; }
    .badge { background: #1b1432; color: #c4b5fd; padding: 1px 5px; border-radius: 6px; font-size: 8px; }
    .total { font-weight: bold; font-size: 10.5px; }
    .empty { text-align: center; padding: 40px; color: #888; }
</style>
</head>
<body>

<header>
    <h1>LAPORAN PENILAIAN INTERVIEW HRD</h1>
    <p class="sub">Youth Ranger Indonesia &nbsp;|&nbsp; <b>{{ $label }}</b> &nbsp;|&nbsp; Total {{ $rows->count() }} kandidat</p>
    <span class="stamp">Diunduh pada: {{ $stamp }}</span>
</header>

<footer>
    Interview HRD Dashboard &bull; Dokumen dibuat otomatis pada {{ $stamp }} &bull; Baris berwarna hijau = kandidat TOP sesuai kuota divisi
</footer>

@if($rows->isEmpty())
    <p class="empty">Belum ada data penilaian.</p>
@endif

@foreach($rows->groupBy('regional') as $reg => $regRows)
    <h2>Regional {{ $reg }}</h2>

    @foreach($regRows->groupBy('divisi') as $div => $list)
        @php $kuota = (int) config("hrd.divisi.{$div}.kuota", 0); @endphp
        <h3>{{ $div }} <small>&nbsp;Kuota TOP: {{ $kuota }} &middot; Pendaftar: {{ $list->count() }}</small></h3>
        <table>
            <thead>
            <tr>
                <th style="width:3%">#</th>
                <th style="width:10%">Waktu Input</th>
                <th style="width:15%">Nama Kandidat</th>
                <th style="width:11%">HRD</th>
                <th style="width:5%">Teknis</th>
                <th style="width:7%">Komunikasi</th>
                <th style="width:5%">Sikap</th>
                <th style="width:6%">Motivasi</th>
                <th style="width:5%">Total</th>
                <th style="width:5%">Status</th>
                <th>Catatan</th>
            </tr>
            </thead>
            <tbody>
            @foreach($list as $i => $c)
                @php $rank = $ranks[$c->id] ?? ($i + 1); @endphp
                <tr class="{{ $rank <= $kuota ? 'top' : '' }}">
                    <td class="c">{{ $rank }}</td>
                    <td class="c">{{ $c->created_at ? $c->created_at->format('d/m/Y H:i') : '-' }}</td>
                    <td><b>{{ $c->nama_kandidat }}</b></td>
                    <td>{{ $c->nama_hrd }}</td>
                    <td class="c">{{ $c->aspek_teknis }}</td>
                    <td class="c">{{ $c->aspek_komunikasi }}</td>
                    <td class="c">{{ $c->aspek_sikap }}</td>
                    <td class="c">{{ $c->aspek_motivasi }}</td>
                    <td class="c total">{{ $c->total }}</td>
                    <td class="c">@if($rank <= $kuota)<span class="badge">TOP</span>@else - @endif</td>
                    <td>{!! nl2br(e($c->catatan)) !!}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endforeach
@endforeach

</body>
</html>

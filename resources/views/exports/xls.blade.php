<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style>
    td, th { font-family: Calibri, Arial, sans-serif; font-size: 11pt; border: .5pt solid #ddd; vertical-align: top; }
    th { background: #1b1432; color: #ffffff; }
    .top td { background: #ede9fe; }
    .c { text-align: center; }
</style>
</head>
<body>
<table>
    <tr><td colspan="14" style="border:none;font-size:16pt;font-weight:bold">LAPORAN PENILAIAN INTERVIEW HRD</td></tr>
    <tr><td colspan="14" style="border:none;font-weight:bold">Youth Ranger Indonesia | {{ $label }}</td></tr>
    <tr><td colspan="14" style="border:none;font-style:italic">Diunduh pada: {{ $stamp }} | Total data: {{ $rows->count() }} kandidat</td></tr>
    <tr><td colspan="14" style="border:none"></td></tr>
    <tr>
        <th>No</th><th>Waktu Input</th><th>Regional</th><th>Divisi</th><th>Peringkat</th><th>Nama Kandidat</th><th>HRD</th>
        <th>Teknis</th><th>Komunikasi</th><th>Sikap</th><th>Motivasi</th><th>Total</th><th>Status</th><th>Catatan</th>
    </tr>
    @foreach($rows as $i => $r)
        @php
            $rank  = $ranks[$r->id] ?? 0;
            $isTop = $rank > 0 && $rank <= \App\Models\Penilaian::kuota($r->divisi);
        @endphp
        <tr class="{{ $isTop ? 'top' : '' }}">
            <td class="c">{{ $i + 1 }}</td>
            <td>{{ $r->created_at ? $r->created_at->format('d/m/Y H:i:s') : '-' }}</td>
            <td>{{ $r->regional }}</td>
            <td>{{ $r->divisi }}</td>
            <td class="c">{{ $rank }}</td>
            <td>{{ $r->nama_kandidat }}</td>
            <td>{{ $r->nama_hrd }}</td>
            <td class="c">{{ $r->aspek_teknis }}</td>
            <td class="c">{{ $r->aspek_komunikasi }}</td>
            <td class="c">{{ $r->aspek_sikap }}</td>
            <td class="c">{{ $r->aspek_motivasi }}</td>
            <td class="c"><b>{{ $r->total }}</b></td>
            <td class="c">{{ $isTop ? 'TOP' : '-' }}</td>
            <td>{{ $r->catatan }}</td>
        </tr>
    @endforeach
</table>
</body>
</html>

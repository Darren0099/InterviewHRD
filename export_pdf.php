Berikut adalah perbaikan dan perapian untuk kode `export_pdf.php` menggunakan Dompdf.

Struktur `HTML` string telah dipisahkan agar lebih rapi, dan sistem pencatatan status kelulusan otomatis berdasarkan kuota divisi telah disematkan agar hasilnya sinkron dengan halaman utama dan export Excel sebelumnya. Kode ini bersih dari seluruh baris komentar:

```php
<?php
require 'vendor/autoload.php';
include 'koneksi.php';

use Dompdf\Dompdf;

date_default_timezone_set("Asia/Jakarta");

$regional = $_GET['regional'] ?? 'SUMSEL';

$divisiList = [
    'Graphic Design' => 4,
    'Content Creator' => 3,
    'Finance' => 4,
    'Project Management' => 7,
    'Human Resource' => 7,
    'Public Relation' => 7,
    'Secretary' => 2,
    'Vice Leader' => 5,
    'Leader' => 5,
    'Social Media Management' => 1
];

$dompdf = new Dompdf();

$html = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        h2 { text-align: center; margin-bottom: 5px; }
        .meta-info { margin-bottom: 15px; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #212529; color: #ffffff; padding: 8px; text-align: center; font-weight: bold; }
        td { padding: 6px; border: 1px solid #dee2e6; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .status-lolos { background-color: #d4edda; color: #155724; font-weight: bold; text-align: center; }
        .status-cadangan { background-color: #fff3cd; color: #856404; font-weight: bold; text-align: center; }
        .status-gagal { background-color: #f8d7da; color: #721c24; text-align: center; }
    </style>
</head>
<body>

    <h2>Data Interview Kandidat Youth Ranger Indonesia</h2>
    <div class='meta-info'>
        Nomor Regional: <b>" . htmlspecialchars($regional) . "</b><br>
        Waktu Cetak: " . date('d-m-Y H:i') . " WIB
    </div>

    <table>
        <thead>
            <tr>
                <th style='width: 5%;'>No</th>
                <th style='width: 25%;'>Nama Kandidat</th>
                <th style='width: 20%;'>Divisi</th>
                <th style='width: 10%;'>Total Nilai</th>
                <th style='width: 15%;'>Status Seleksi</th>
                <th style='width: 25%;'>HRD Interviewer</th>
            </tr>
        </thead>
        <tbody>
";

$no = 1;
$q = mysqli_query($conn, "
    SELECT *
    FROM penilaian
    WHERE regional='$regional'
    ORDER BY divisi ASC, total DESC
");

$counterDivisi = [];

while ($d = mysqli_fetch_assoc($q)) {
    $div = $d['divisi'];

    if (!isset($counterDivisi[$div])) {
        $counterDivisi[$div] = 1;
    } else {
        $counterDivisi[$div]++;
    }

    $rankDivisi = $counterDivisi[$div];
    $kuotaDivisi = $divisiList[$div] ?? 0;

    if ($rankDivisi <= $kuotaDivisi) {
        $statusText = "Lolos Kuota";
        $classStatus = "status-lolos";
    } elseif ($rankDivisi <= ($kuotaDivisi + 2)) {
        $statusText = "Cadangan";
        $classStatus = "status-cadangan";
    } else {
        $statusText = "Tidak Lolos";
        $classStatus = "status-gagal";
    }

    $html .= "
        <tr>
            <td class='text-center'>" . $no++ . "</td>
            <td>" . htmlspecialchars($d['nama_kandidat']) . "</td>
            <td>" . htmlspecialchars($div) . "</td>
            <td class='text-center fw-bold'>" . $d['total'] . "</td>
            <td class='" . $classStatus . "'>" . $statusText . "</td>
            <td>" . htmlspecialchars($d['nama_hrd']) . "</td>
        </tr>
    ";
}

$html .= "
        </tbody>
    </table>
</body>
</html>
";

$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "landscape");
$dompdf->render();

$dompdf->stream("Interview_" . $regional . "_" . date('Ymd') . ".pdf", [
    "Attachment" => true
]);
?>

```
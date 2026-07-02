<?php
include 'koneksi.php';

date_default_timezone_set("Asia/Jakarta");

$regional = $_GET['regional'] ?? 'SUMSEL';

$hari = [
    "Sunday"    => "Minggu",
    "Monday"    => "Senin",
    "Tuesday"   => "Selasa",
    "Wednesday" => "Rabu",
    "Thursday"  => "Kamis",
    "Friday"    => "Jumat",
    "Saturday"  => "Sabtu"
];

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

$namaHari = $hari[date("l")];
$tanggal = date("d-m-Y");

$namaFile = "Data Interview " . $regional . " " . $namaHari . " " . $tanggal . ".xls";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$namaFile\"");
header("Pragma: no-cache");
header("Expires: 0");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; }
        table { border-collapse: collapse; width: 100%; }
        th { background-color: #212529; color: #ffffff; text-align: center; padding: 8px; }
        td { padding: 6px; text-align: left; }
        .text-center { text-align: center; }
        .status-lolos { background-color: #d4edda; color: #155724; font-weight: bold; }
        .status-cadangan { background-color: #fff3cd; color: #856404; font-weight: bold; }
        .status-gagal { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

    <h2>Data Interview Kandidat Youth Ranger Indonesia</h2>

    <table>
        <tr>
            <td style="width: 120px;"><b>Regional</b></td>
            <td>: <?= htmlspecialchars($regional) ?></td>
        </tr>
        <tr>
            <td><b>Tanggal Export</b></td>
            <td>: <?= $namaHari . ", " . date("d F Y H:i") ?> WIB</td>
        </tr>
    </table>
    
    <br>

    <table border="1">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Kandidat</th>
                <th>Divisi</th>
                <th>Teknis</th>
                <th>Komunikasi</th>
                <th>Sikap</th>
                <th>Motivasi</th>
                <th>Total Nilai</th>
                <th>Status Seleksi</th>
                <th>HRD</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $no = 1;
        $q = mysqli_query($conn, "
            SELECT *
            FROM penilaian
            WHERE regional='$regional'
            ORDER BY divisi ASC, total DESC
        ");

        $counterDivisi = [];

        while ($d = mysqli_fetch_assoc($q)):
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
        ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td><?= htmlspecialchars($d['nama_kandidat']) ?></td>
                <td><?= htmlspecialchars($div) ?></td>
                <td class="text-center"><?= $d['aspek_teknis'] ?></td>
                <td class="text-center"><?= $d['aspek_komunikasi'] ?></td>
                <td class="text-center"><?= $d['aspek_sikap'] ?></td>
                <td class="text-center"><?= $d['aspek_motivasi'] ?></td>
                <td class="text-center" style="font-weight: bold;"><?= $d['total'] ?></td>
                <td class="text-center <?= $classStatus ?>"><?= $statusText ?></td>
                <td><?= htmlspecialchars($d['nama_hrd']) ?></td>
                <td><?= htmlspecialchars($d['catatan']) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>

```
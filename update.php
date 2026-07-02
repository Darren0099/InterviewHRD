<?php
include 'koneksi.php';

$divisi   = mysqli_real_escape_string($conn, $_GET['divisi']);
$regional = mysqli_real_escape_string($conn, $_GET['regional']);

$kuota = [
    "Graphic Design" => 4,
    "Content Creator" => 3,
    "Finance" => 4,
    "Project Management" => 7,
    "Human Resource" => 7,
    "Public Relation" => 7,
    "Secretary" => 2,
    "Vice Leader" => 5,
    "Leader" => 5,
    "Social Media Management" => 1
];

$limit = $kuota[$divisi] ?? 0;

$q = mysqli_query($conn, "
    SELECT *
    FROM penilaian
    WHERE regional='$regional'
    AND divisi='$divisi'
    ORDER BY total DESC
    LIMIT $limit
");
?>

<div style="max-height: 500px; overflow-y: auto;">
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th style="width: 5%;">#</th>
                <th>Nama</th>
                <th>Teknis</th>
                <th>Komunikasi</th>
                <th>Sikap</th>
                <th>Motivasi</th>
                <th>Total</th>
                <th>HRD</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $no = 1;

        if (mysqli_num_rows($q) == 0) {
            echo "<tr><td colspan='9' align='center'>Belum ada kandidat.</td></tr>";
        }

        while ($d = mysqli_fetch_assoc($q)):
            switch ($no) {
                case 1: $medal = "🥇 "; break;
                case 2: $medal = "🥈 "; break;
                case 3: $medal = "🥉 "; break;
                default: $medal = "";
            }
        ?>
            <tr>
                <td class="text-center fw-bold"><?= $no++ ?></td>
                <td><?= $medal . htmlspecialchars($d['nama_kandidat']) ?></td>
                <td class="text-center"><?= $d['aspek_teknis'] ?></td>
                <td class="text-center"><?= $d['aspek_komunikasi'] ?></td>
                <td class="text-center"><?= $d['aspek_sikap'] ?></td>
                <td class="text-center"><?= $d['aspek_motivasi'] ?></td>
                <td class="text-center">
                    <?php
                    if ($d['total'] >= 85) {
                        echo "<span class='badge bg-success px-2 py-1'>" . $d['total'] . "</span>";
                    } elseif ($d['total'] >= 70) {
                        echo "<span class='badge bg-warning text-dark px-2 py-1'>" . $d['total'] . "</span>";
                    } else {
                        echo "<span class='badge bg-danger px-2 py-1'>" . $d['total'] . "</span>";
                    }
                    ?>
                </td>
                <td><?= htmlspecialchars($d['nama_hrd']) ?></td>
                <td><?= nl2br(htmlspecialchars($d['catatan'])) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

```
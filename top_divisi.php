<?php
include 'koneksi.php';

$divisi   = mysqli_real_escape_string($conn, $_GET['divisi']);
$regional = mysqli_real_escape_string($conn, $_GET['regional']);

$kuota = [

"Graphic Design"=>4,

"Content Creator"=>3,

"Finance"=>4,

"Project Management"=>7,

"Human Resource"=>7,

"Public Relation"=>7,

"Secretary"=>5,

"Vice Leader"=>5,

"Leader"=>5,

"Social Media Management"=>4

];

$limit = $kuota[$divisi];

// Ambil kandidat terbaik sesuai kuota
$q = mysqli_query($conn,"
SELECT *
FROM penilaian
WHERE regional='$regional'
AND divisi='$divisi'
ORDER BY total DESC
LIMIT $limit
");

?>

<div style="max-height:500px;overflow:auto;">

<table class="table table-bordered table-striped">

<thead class="table-dark">

<tr>

<th>#</th>
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

$no=1;

if(mysqli_num_rows($q)==0){

echo "<tr><td colspan='9' align='center'>Belum ada kandidat.</td></tr>";

}

while($d=mysqli_fetch_assoc($q)):

?>

<tr>

<td><?= $no++ ?></td>

<td>

<?php

if($no==2){

echo "🥇 ";

}elseif($no==3){

echo "🥈 ";

}elseif($no==4){

echo "🥉 ";

}

?>

<?= htmlspecialchars($d['nama_kandidat']) ?>

</td>

<td><?= $d['aspek_teknis'] ?></td>

<td><?= $d['aspek_komunikasi'] ?></td>

<td><?= $d['aspek_sikap'] ?></td>

<td><?= $d['aspek_motivasi'] ?></td>

<td>

<span class="badge bg-success">

<?= $d['total'] ?>

</span>

</td>

<td>

<?= htmlspecialchars($d['nama_hrd']) ?>

</td>

<td>

<?= nl2br(htmlspecialchars($d['catatan'])) ?>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>
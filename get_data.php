<?php

include 'koneksi.php';

$id = (int)$_GET['id'];

$q = mysqli_query($conn,"
SELECT *
FROM penilaian
WHERE id='$id'
");

echo json_encode(mysqli_fetch_assoc($q));

?>
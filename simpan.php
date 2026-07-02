<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $regional = mysqli_real_escape_string($conn, $_POST['regional']);
    $divisi = mysqli_real_escape_string($conn, $_POST['divisi']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_kandidat']);
    $hrd = mysqli_real_escape_string($conn, $_POST['nama_hrd']);

    $teknis = (int)$_POST['aspek_teknis'];
    $komunikasi = (int)$_POST['aspek_komunikasi'];
    $sikap = (int)$_POST['aspek_sikap'];
    $motivasi = (int)$_POST['aspek_motivasi'];

    $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);

    $total = $teknis + $komunikasi + $sikap + $motivasi;

    $sql = "INSERT INTO penilaian
    (
        regional,
        divisi,
        nama_kandidat,
        nama_hrd,
        aspek_teknis,
        aspek_komunikasi,
        aspek_sikap,
        aspek_motivasi,
        total,
        catatan
    )
    VALUES
    (
        '$regional',
        '$divisi',
        '$nama',
        '$hrd',
        '$teknis',
        '$komunikasi',
        '$sikap',
        '$motivasi',
        '$total',
        '$catatan'
    )";

    if(mysqli_query($conn,$sql)){
        echo "ok";
    }else{
        echo mysqli_error($conn);
    }

}
?>
<?php

include 'koneksi.php';

if(!isset($_GET['regional'])){

header("Location:index.php");

exit;

}

$regional=mysqli_real_escape_string($conn,$_GET['regional']);

mysqli_query($conn,"

DELETE FROM penilaian

WHERE regional='$regional'

");

header("Location:index.php?regional=".$regional);

exit;

?>
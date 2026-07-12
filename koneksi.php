<?php
// Pengaturan Database
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "inventaris_laboratorium";

// Membuat Koneksi secara Procedural
$koneksi = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Memeriksa Koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>

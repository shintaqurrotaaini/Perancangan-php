<?php
$host = "localhost:3307";
$user = "root";
$pass = "";
$db   = "toko_my_official";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
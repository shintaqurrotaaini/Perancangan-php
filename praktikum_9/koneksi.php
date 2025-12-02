<?php
session_start();
$servername = "localhost";
$port = 3307;
$username = "root";     
$password = "";         
$dbname = "db_praktikum"; 

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Memeriksa koneksi
if ($conn->connect_error) {
    // Jika koneksi GAGAL, tampilkan error dan hentikan skrip.
    die("Koneksi gagal: " . $conn->connect_error);
} 
// $conn TIDAK DITUTUP di sini, agar bisa digunakan di index.php dan pagination.php
?>
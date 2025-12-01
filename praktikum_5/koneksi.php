<?php
session_start();
$servername = "localhost:3307";
$username = "root";     // Ganti sesuai Anda
$password = "";         // Ganti sesuai Anda
$dbname = "db_praktikum"; // Ganti dengan nama database Anda

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    // Menggunakan die() untuk menghentikan eksekusi dan menampilkan pesan error
    die("Koneksi gagal: " . $conn->connect_error);
}
// Koneksi berhasil, tidak perlu echo apapun di sini
?>
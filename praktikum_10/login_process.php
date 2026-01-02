<?php
session_start();
// Memuat definisi fungsi pengiriman email
include 'config_mail.php'; //

// Ambil data login
$username = trim($_POST['username']); //
$password = trim($_POST['password']); //

// === Autentikasi (SIMULASI - Ganti dengan database yang aman) ===
// Untuk tujuan contoh, kita tetap menggunakan hardcode
if ($username === 'admin' && $password === '123') {

    // Set session
    $_SESSION['loggedin'] = TRUE;
    $_SESSION['username'] = $username; //

    // Jenis Autentikasi
    $authType = "LOGIN-PASSWORD"; //

    // Kirim email notifikasi
    sendLoginAlert($username, $authType); //

    header("Location: dashboard.php");
    exit;
    
} else {
    //
    header("Location: login.php?error=1");
    exit;
}
// Kurung kurawal berlebih di akhir sudah dihapus.
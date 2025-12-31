<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../config/db.php";
include "../config/log.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

// 🔴 FIX DI SINI
$sql = "SELECT * FROM login WHERE username='$username'";
$query = mysqli_query($conn, $sql);

if (!$query) {
    die("QUERY ERROR: " . mysqli_error($conn));
}

$user = mysqli_fetch_assoc($query);

if ($user && password_verify($password, $user['password'])) {

    $_SESSION['login']   = true;
    $_SESSION['id_user'] = $user['id_user'];
    $_SESSION['nama']    = $user['nama'];
    $_SESSION['role']    = $user['role'];

 // ===== LOG AKTIVITAS LOGIN =====
tambah_log($conn, $_SESSION['id_user'], 'Login ke sistem');


    if ($user['role'] === 'admin') {
        header("Location: ../dashboard/admin.php");
    } elseif ($user['role'] === 'pemilik') {
        header("Location: ../dashboard/pemilik.php");
    } elseif ($user['role'] === 'gudang') {
        header("Location: ../dashboard/gudang.php");
    } else {
        die("Role tidak dikenali");
    }
    exit;

} else {
    header("Location: login.php?error=login");
    exit;
}

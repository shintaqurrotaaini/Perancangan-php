<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../config/db.php";
include "../config/mail.php"; // PHPMailer

// ================= VALIDASI METHOD =================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register.php");
    exit;
}

// ================= AMBIL DATA =================
$nama     = trim($_POST['nama'] ?? '');
$email    = trim($_POST['email'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// 🔒 ROLE DIKUNCI (HAKIKAT SISTEM)
$role = 'gudang';

// ================= VALIDASI KOSONG =================
if ($nama === '' || $email === '' || $username === '' || $password === '') {
    die("Data tidak lengkap");
}

// ================= CEK DUPLIKAT =================
$cek = mysqli_query($conn, "
    SELECT id_user FROM login 
    WHERE username='$username' OR email='$email'
");

if (!$cek) {
    die("QUERY ERROR: " . mysqli_error($conn));
}

if (mysqli_num_rows($cek) > 0) {
    die("Username atau email sudah terdaftar");
}

// ================= INSERT USER =================
$id_user = "USR" . rand(1000,9999);
$password_hash = password_hash($password, PASSWORD_DEFAULT);

$sql = "
    INSERT INTO login (id_user, nama, email, username, password, role)
    VALUES ('$id_user','$nama','$email','$username','$password_hash','$role')
";

$query = mysqli_query($conn, $sql);

if (!$query) {
    die("INSERT ERROR: " . mysqli_error($conn));
}

// ================= KIRIM EMAIL =================
if (function_exists('kirimEmail')) {
    kirimEmail($email, $nama, $role);
}

// ================= REDIRECT =================
header("Location: login.php?register=success");
exit;

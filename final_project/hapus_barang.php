<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";

/* ================= AMBIL ID ================= */
$id = $_GET['id'] ?? '';

if ($id == '') {
    header("Location: data_barang.php");
    exit;
}

/* ================= AMBIL DATA BARANG ================= */
$q = mysqli_query($conn, "SELECT gambar FROM barang WHERE id_barang='$id'");
$barang = mysqli_fetch_assoc($q);

if (!$barang) {
    header("Location: data_barang.php");
    exit;
}

/* ================= HAPUS GAMBAR ================= */
if (!empty($barang['gambar'])) {
    $path = "../assets/barang/" . $barang['gambar'];

    if (file_exists($path) && $barang['gambar'] !== 'BRG1008.jpeg') {
        unlink($path);
    }
}

/* ================= HAPUS DATA ================= */
mysqli_query($conn, "DELETE FROM barang WHERE id_barang='$id'");

/* ================= REDIRECT ================= */
header("Location: data_barang.php?hapus=success");
exit;

<?php
session_start();
if(!isset($_SESSION['login']) || $_SESSION['role']!='gudang'){
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";

$id = $_GET['id'] ?? 0;

/* ambil data restock */
$q = mysqli_query($conn,"
    SELECT id_barang, jumlah
    FROM restock
    WHERE id_restock='$id' AND status='disetujui'
");
$data = mysqli_fetch_assoc($q);

if(!$data){
    die("Data tidak valid");
}

$id_barang = $data['id_barang'];
$jumlah    = $data['jumlah'];

/* tambah stok barang */
mysqli_query($conn,"
    UPDATE barang
    SET stok = stok + $jumlah
    WHERE id_barang='$id_barang'
");

/* update status restock */
mysqli_query($conn,"
    UPDATE restock
    SET status='selesai'
    WHERE id_restock='$id'
");

/* redirect balik */
header("Location: pemenuhan_stok.php");
exit;

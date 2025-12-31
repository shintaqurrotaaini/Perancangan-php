<?php
session_start();
if(!isset($_SESSION['login'])){
    header("Location: ../auth/login.php");
    exit;
}

$allowed_roles = ['admin','pemilik','gudang'];
if (!in_array($_SESSION['role'], $allowed_roles)) {
    exit('Akses ditolak');
}

include "../config/db.php";

$id   = $_GET['id'] ?? '';
$tipe = $_GET['tipe'] ?? 'masuk';

if($id == ''){
    echo "ID laporan tidak valid";
    exit;
}

// Query sesuai tipe laporan
if($tipe == 'masuk'){
    $sql = "SELECT bm.tanggal, bm.jumlah, bm.input_by, b.nama_barang
            FROM barang_masuk bm
            JOIN barang b ON bm.id_barang = b.id_barang
            WHERE bm.id_masuk = '$id'";
    $judul = "Detail Laporan Barang Masuk";
}else{
    $sql = "SELECT bk.tanggal, bk.jumlah, bk.input_by, b.nama_barang
            FROM barang_keluar bk
            JOIN barang b ON bk.id_barang = b.id_barang
            WHERE bk.id_keluar = '$id'";
    $judul = "Detail Laporan Barang Keluar";
}

$q = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($q);

if(!$data){
    echo "Data laporan tidak ditemukan";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title><?= $judul ?></title>
<style>
body{
    font-family:'Segoe UI';
    background:#eef5ff;
    margin:0;
    padding:40px;
}
.box{
    background:#fff;
    max-width:520px;
    margin:auto;
    padding:30px;
    border-radius:14px;
    box-shadow:0 4px 14px rgba(0,0,0,.12);
}
h2{
    text-align:center;
    margin-bottom:20px;
}
.detail{
    margin-bottom:12px;
    font-size:15px;
}
.detail b{
    display:inline-block;
    width:140px;
}
.back{
    display:inline-block;
    margin-top:20px;
    padding:10px 18px;
    background:#4da3ff;
    color:#fff;
    text-decoration:none;
    border-radius:8px;
}
.back:hover{
    background:#1e88e5;
}
</style>
</head>
<body>

<div class="box">
    <h2>📄 <?= $judul ?></h2>

    <div class="detail">
        <b>Tanggal</b> : <?= date('d-m-Y', strtotime($data['tanggal'])) ?>
    </div>
    <div class="detail">
        <b>Nama Barang</b> : <?= htmlspecialchars($data['nama_barang']) ?>
    </div>
    <div class="detail">
        <b>Jumlah</b> : <?= $data['jumlah'] ?>
    </div>
    <div class="detail">
        <b>Input Oleh</b> : <?= $data['input_by'] ?>
    </div>

    <a href="index.php?tipe=<?= $tipe ?>" class="back">← Kembali ke Laporan</a>
</div>

</body>
</html>

<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ID BARANG VARCHAR
    $id_barang = "BRG" . rand(1000,9999);

    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $harga    = (int) $_POST['harga'];
    $stok     = (int) $_POST['stok'];
    $satuan   = mysqli_real_escape_string($conn, $_POST['satuan']);

    /* ===== UPLOAD GAMBAR ===== */
    $gambar = '';
    if (!empty($_FILES['gambar']['name'])) {
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $nama_file = $id_barang . '.' . $ext;

        move_uploaded_file(
            $_FILES['gambar']['tmp_name'],
            "../assets/barang/" . $nama_file
        );

        $gambar = $nama_file;
    }

    mysqli_query($conn, "
        INSERT INTO barang
        (id_barang, nama_barang, kategori, harga, stok, satuan, gambar)
        VALUES
        ('$id_barang','$nama','$kategori','$harga','$stok','$satuan','$gambar')
    ");

    header("Location: data_barang.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tambah Barang | Toko MY_Official</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background:#eef5ff;
}

/* ===== SIDEBAR ===== */
.sidebar{
    width:240px;
    height:100vh;
    background:#4da3ff;
    position:fixed;
    color:#fff;
    padding:20px;
}
.sidebar small{
    display:block;
    margin-bottom:4px;   /* JARAK DIKECILKAN */
}
.sidebar h2{
    margin:0 0 10px;     /* HILANGKAN JARAK ATAS */
}
.sidebar a{
    display:block;
    padding:10px;
    color:#fff;
    text-decoration:none;
    border-radius:6px;
    margin-bottom:6px;
}
.sidebar a:hover,
.sidebar a.active{
    background:rgba(255,255,255,.25);
}

/* ===== BRAND / LOGO ===== */
.brand{
    display:flex;
    align-items:center;
    gap:10px;
    margin:20px 0;
}
.brand img{
    width:45px;
    height:45px;
    object-fit:cover;
    border-radius:50%;
}
.brand-text{
    display:flex;
    flex-direction:column;
    line-height:1.2;
}
.brand-text small{
    font-size:12px;
    opacity:.9;
}

/* ===== MAIN ===== */
.main{
    margin-left:260px;
    padding:30px;
}

/* ===== CARD (PUTIH LEBIH BESAR) ===== */
.card{
    background:#fff;
    max-width:650px;
    padding:35px;
    border-radius:14px;
    margin:40px auto;
}

/* ===== FORM ===== */
label{
    font-weight:600;
}
input{
    width:100%;
    padding:12px;
    margin:8px 0 16px;
    border:1px solid #ccc;
    border-radius:8px;
}
button{
    width:100%;
    padding:14px;
    background:#41b883;
    color:#fff;
    border:none;
    border-radius:10px;
    font-size:16px;
    cursor:pointer;
}
button:hover{opacity:.9}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <small>DATA TOKO</small>
    <h2>TOKO MY_Official</h2>

    <div class="brand">
        <img src="../assets/logo.jpeg" alt="Logo">
        <div class="brand-text">
            <b><?= $_SESSION['nama']; ?></b>
            <small>ADMIN</small>
        </div>
    </div>

    <a href="../dashboard/admin.php">Dashboard</a>
    <a href="data_barang.php" class="active">Data Barang</a>
    <a href="../barang/rekap_barang.php">Rekap Barang</a>
    <a href="../restok/index.php">Permintaan Restock</a>
    <a href="../laporan/index.php">Laporan Barang</a>
    <a href="../auth/logout.php">Logout</a>
</div>

<!-- MAIN -->
<div class="main">

<div class="card">
<h2 style="text-align:center;margin-bottom:25px;">Tambah Barang</h2>

<form method="post" enctype="multipart/form-data">

<label>Nama Barang</label>
<input type="text" name="nama" required>

<label>Kategori</label>
<input type="text" name="kategori" required>

<label>Harga</label>
<input type="number" name="harga" required>

<label>Stok Awal</label>
<input type="number" name="stok" required>

<label>Satuan</label>
<input type="text" name="satuan" required>

<label>Gambar Barang</label>
<input type="file" name="gambar" accept="image/*">

<button type="submit">Simpan Barang</button>

</form>
</div>

</div>
</body>
</html>

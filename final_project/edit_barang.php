<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";

/* ================= AMBIL DATA BARANG ================= */
$id = $_GET['id'] ?? '';
$q = mysqli_query($conn, "SELECT * FROM barang WHERE id_barang='$id'");
$barang = mysqli_fetch_assoc($q);

if (!$barang) {
    die("Data barang tidak ditemukan");
}

/* ================= PROSES UPDATE ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $harga    = (int) $_POST['harga'];
    $stok     = (int) $_POST['stok'];
    $satuan   = mysqli_real_escape_string($conn, $_POST['satuan']);

    $gambar_lama = $barang['gambar'];
    $gambar_baru = $gambar_lama;

    if (!empty($_FILES['gambar']['name'])) {
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $nama_file = $id . '.' . $ext;

        move_uploaded_file(
            $_FILES['gambar']['tmp_name'],
            "../assets/barang/" . $nama_file
        );

        if ($gambar_lama && file_exists("../assets/barang/".$gambar_lama)) {
            unlink("../assets/barang/".$gambar_lama);
        }

        $gambar_baru = $nama_file;
    }

    mysqli_query($conn, "
        UPDATE barang SET
            nama_barang='$nama',
            kategori='$kategori',
            harga='$harga',
            stok='$stok',
            satuan='$satuan',
            gambar='$gambar_baru'
        WHERE id_barang='$id'
    ");

    header("Location: data_barang.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Barang | Toko MY_Official</title>

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

/* ===== BRAND ===== */
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

/* ===== CARD ===== */
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
img.preview{
    width:140px;
    height:140px;
    object-fit:cover;
    border-radius:12px;
    margin-bottom:12px;
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
<h2 style="text-align:center;margin-bottom:25px;">Edit Barang</h2>

<form method="post" enctype="multipart/form-data">

<label>Nama Barang</label>
<input type="text" name="nama" value="<?= $barang['nama_barang']; ?>" required>

<label>Kategori</label>
<input type="text" name="kategori" value="<?= $barang['kategori']; ?>" required>

<label>Harga</label>
<input type="number" name="harga" value="<?= $barang['harga']; ?>" required>

<label>Stok</label>
<input type="number" name="stok" value="<?= $barang['stok']; ?>" required>

<label>Satuan</label>
<input type="text" name="satuan" value="<?= $barang['satuan']; ?>" required>

<label>Gambar Saat Ini</label><br>
<img src="../assets/barang/<?= $barang['gambar'] ?: 'default.jpeg'; ?>" class="preview">

<label>Ganti Gambar (opsional)</label>
<input type="file" name="gambar" accept="image/*">

<button type="submit">Simpan Perubahan</button>

</form>
</div>

</div>
</body>
</html>

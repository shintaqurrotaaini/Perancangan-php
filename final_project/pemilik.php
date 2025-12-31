<?php
session_start();
if(!isset($_SESSION['login']) || $_SESSION['role']!='pemilik'){
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";

/* ===============================
   1. STOK KRITIS (dari permintaan admin)
================================ */
$q_kritis = mysqli_query($conn,"
    SELECT COUNT(DISTINCT id_barang) AS total
    FROM restock
    WHERE status='diminta'
");
$stok_kritis = mysqli_fetch_assoc($q_kritis)['total'] ?? 0;

/* ===============================
   2. TOTAL PERMINTAAN RESTOCK
================================ */
$q_req = mysqli_query($conn,"
    SELECT COUNT(*) AS total
    FROM restock
    WHERE status='diminta'
");
$permintaan = mysqli_fetch_assoc($q_req)['total'] ?? 0;

/* ===============================
   3. BARANG STOK MENIPIS
================================ */
$q_barang = mysqli_query($conn,"
    SELECT nama_barang, stok
    FROM barang
    ORDER BY stok ASC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Pemilik</title>

<style>
body{margin:0;font-family:'Segoe UI';background:#f4f7fb;}
/* ===== SIDEBAR ===== */
.sidebar{
    width:240px;height:100vh;
    position:fixed;
    background:#4da3ff;
    color:#fff;padding:20px;
}
.sidebar h2{margin:5px 0 15px;}
.sidebar a{
    display:block;
    padding:10px;
    color:#fff;
    text-decoration:none;
    border-radius:6px;
    margin-bottom:6px;
}
.sidebar a:hover,.sidebar a.active{
    background:rgba(255,255,255,.25);
}
.profile{
    display:flex;
    align-items:center;
    gap:10px;
    margin:20px 0;
}
.profile img{
    width:45px;height:45px;
    border-radius:50%;
    object-fit:cover;
}

/* ===== MAIN ===== */
.main{margin-left:260px;padding:30px;}
.cards{display:flex;gap:20px;margin:25px 0;}
.card{
    background:#fff;padding:20px;
    border-radius:12px;
    width:260px;
    box-shadow:0 4px 10px rgba(0,0,0,.1);
}
.card h3{margin:0;font-size:14px;color:#666;}
.card b{font-size:28px;}
.grafik{
    background:#fff;padding:20px;
    border-radius:12px;
    box-shadow:0 4px 10px rgba(0,0,0,.1);
}
.bar{margin:12px 0;}
.bar span{display:inline-block;width:160px;}
.bar div{
    display:inline-block;
    height:18px;
    background:#4da3ff;
    border-radius:5px;
}
</style>
</head>
<body>

<!-- SIDEBAR PEMILIK -->
<div class="sidebar">
    <small>DATA TOKO</small>
    <h2>TOKO MY_Official</h2>

    <div class="profile">
        <img src="../assets/logo.jpeg">
        <div>
            <b><?= $_SESSION['nama'] ?? 'Pemilik'; ?></b><br>
            <small>PEMILIK</small>
        </div>
    </div>

    <a href="pemilik.php" class="active">Dashboard</a>
    <a href="../restok/persetujuan.php">Persetujuan Restock</a>
    <a href="../laporan/index.php">Laporan Barang</a>
    <a href="../auth/logout.php">Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="main">
    <h2>Dashboard</h2>
    <p>Selamat datang, pemilik toko. Anda login sebagai pemilik toko</p>

    <div class="cards">
        <div class="card">
            <h3>Stok Kritis</h3>
            <b><?= $stok_kritis ?> STOK</b>
            <p>Perlu segera ditindak</p>
        </div>

        <div class="card">
            <h3>Permintaan Restock</h3>
            <b><?= $permintaan ?></b>
            <p>Menunggu persetujuan</p>
        </div>
    </div>

    <div class="grafik">
        <h3>Grafik Barang Stok Menipis</h3>

        <?php if(mysqli_num_rows($q_barang)>0): ?>
            <?php while($b=mysqli_fetch_assoc($q_barang)): ?>
                <div class="bar">
                    <span><?= $b['nama_barang'] ?></span>
                    <div style="width:<?= max(40,220-$b['stok']) ?>px"></div>
                    <?= $b['stok'] ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Data barang belum tersedia</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>

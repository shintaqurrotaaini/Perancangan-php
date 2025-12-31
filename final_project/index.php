<?php
session_start();
if(!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";

/* ================== QUERY DATA ================== */
$sql = "
    SELECT r.*, b.nama_barang
    FROM restock r
    JOIN barang b ON r.id_barang = b.id_barang
    ORDER BY r.tanggal DESC
";

$q = mysqli_query($conn, $sql);
if(!$q){
    die("Query error: ".mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Permintaan Restock | Toko MY_Official</title>
<style>
body{margin:0;font-family:'Segoe UI';background:#eef5ff;}
.sidebar{width:240px;height:100vh;position:fixed;background:#4da3ff;color:#fff;padding:20px;}
.sidebar h2{margin:5px 0 10px;}
.sidebar a{display:block;padding:10px;color:#fff;text-decoration:none;border-radius:6px;margin-bottom:6px;}
.sidebar a:hover,.sidebar a.active{background:rgba(255,255,255,.25);}
.profile{display:flex;align-items:center;gap:10px;margin:20px 0;}
.profile img{width:45px;height:45px;border-radius:50%;object-fit:cover;}
.main{margin-left:260px;padding:30px;}
.header h2{background:#fff;border:3px solid #000;border-radius:50px;padding:10px 30px;display:inline-block;}
.table-box{background:#fff;padding:20px;border-radius:12px;}
table{width:100%;border-collapse:collapse;}
th,td{border:1px solid #ccc;padding:10px;text-align:center;}
th{background:#f1f1f1;}
.status-diminta{background:#ff9800;color:#fff;padding:5px 10px;border-radius:12px;}
.status-disetujui{background:#4caf50;color:#fff;padding:5px 10px;border-radius:12px;}
.status-dipenuhi{background:#2196f3;color:#fff;padding:5px 10px;border-radius:12px;}
.btn{background:#4da3ff;color:#fff;padding:8px 15px;border-radius:6px;text-decoration:none;}
</style>
</head>
<body>

<div class="sidebar">
    <small>DATA TOKO</small>
    <h2>TOKO MY_Official</h2>
    <div class="profile">
        <img src="../assets/logo.jpeg">
        <div>
            <b><?= $_SESSION['nama']; ?></b><br>
            <small><?= strtoupper($_SESSION['role']); ?></small>
        </div>
    </div>
    <a href="../dashboard/admin.php">Dashboard</a>
    <a href="../barang/data_barang.php">Data Barang</a>
    <a href="../barang/rekap_barang.php">Rekap Barang</a>
    <a href="../restok/index.php" class="active">Permintaan Restock</a>
    <a href="../laporan/index.php">Laporan Barang</a>
    <a href="../auth/logout.php">Logout</a>
</div>

<div class="main">
    <div class="header">
        <h2>📦 Permintaan Restock Produk</h2>
    </div>

    <a href="tambah.php" class="btn">+ Buat Permintaan Restock</a>

    <div class="table-box">
        <table>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Status</th>
                <th>Tanggal</th>
            </tr>
            <?php $no=1; while($r = mysqli_fetch_assoc($q)): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $r['nama_barang'] ?></td>
                <td><?= $r['jumlah'] ?></td>
                <td>
                    <span class="status-<?= $r['status'] ?>">
                        <?= ucfirst($r['status']) ?>
                    </span>
                </td>
                <td><?= date('d-m-Y', strtotime($r['tanggal'])) ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>

</body>
</html>

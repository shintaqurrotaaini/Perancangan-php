<?php
session_start();
if(!isset($_SESSION['login']) || $_SESSION['role'] !== 'gudang'){
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";

/* ================= QUERY RESTOCK ================= */
$sql = "
    SELECT r.id_restock, b.nama_barang, r.jumlah, r.status
    FROM restock r
    JOIN barang b ON r.id_barang = b.id_barang
    WHERE r.status IN ('disetujui','dipenuhi')
    ORDER BY r.tanggal DESC
";
$q = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pemenuhan Stok | Gudang</title>

<style>
body{margin:0;font-family:'Segoe UI';background:#eef5ff}

/* SIDEBAR (SAMA SEPERTI SEBELUMNYA) */
.sidebar{
    width:240px;height:100vh;position:fixed;
    background:#4da3ff;color:#fff;padding:20px
}
.sidebar h2{margin:5px 0 10px}
.sidebar a{
    display:block;padding:10px;color:#fff;
    text-decoration:none;border-radius:6px;margin-bottom:6px
}
.sidebar a.active,.sidebar a:hover{
    background:rgba(255,255,255,.25)
}
.profile{
    display:flex;align-items:center;gap:10px;margin:20px 0
}
.profile img{
    width:45px;height:45px;border-radius:50%;object-fit:cover
}

/* MAIN */
.main{margin-left:260px;padding:30px}
.title{
    background:#fff;padding:15px 20px;
    border-radius:12px;font-weight:bold
}
.card{
    background:#fff;padding:20px;border-radius:12px;
    margin-top:20px
}
table{width:100%;border-collapse:collapse}
th,td{
    padding:10px;border-bottom:1px solid #eee;
    text-align:center;font-size:14px
}
th{background:#f5f5f5}
.badge{
    padding:5px 10px;border-radius:20px;
    font-size:12px;color:#fff
}
.menunggu{background:#ff9800}
.dipenuhi{background:#4caf50}
.btn{
    padding:6px 12px;border-radius:6px;
    text-decoration:none;color:#fff;font-size:13px
}
.btn-proses{background:#1e88e5}
.btn-disable{background:#9e9e9e}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <small>DATA TOKO</small>
    <h2>TOKO MY_Official</h2>

    <div class="profile">
        <img src="../assets/logo.jpeg">
        <div>
            <b><?= $_SESSION['nama'] ?></b><br>
            <small>GUDANG</small>
        </div>
    </div>

    <a href="../dashboard/gudang.php">Dashboard</a>
    <a href="../barang/rekap_barang.php">Rekap Barang</a>
    <a href="../restok/pemenuhan_stok.php" class="active">Pemenuhan Stok</a>
    <a href="../laporan/index.php">Laporan Barang</a>
    <a href="../auth/logout.php">Logout</a>
</div>

<!-- MAIN -->
<div class="main">

<div class="title">Pemenuhan Stok (Gudang)</div>

<div class="card">
<h3>Riwayat Pemenuhan Restock</h3>

<table>
<tr>
    <th>Nama Barang</th>
    <th>Jumlah</th>
    <th>Status</th>
    <th>Aksi</th>
</tr>

<?php if($q && mysqli_num_rows($q)>0): ?>
<?php while($r=mysqli_fetch_assoc($q)): ?>
<tr>
    <td><?= $r['nama_barang'] ?></td>
    <td><?= $r['jumlah'] ?></td>
    <td>
        <?php if($r['status']=='disetujui'): ?>
            <span class="badge menunggu">Menunggu</span>
        <?php else: ?>
            <span class="badge dipenuhi">Selesai</span>
        <?php endif; ?>
    </td>
    <td>
        <?php if($r['status']=='disetujui'): ?>
            <a href="proses_pemenuhan.php?id=<?= $r['id_restock'] ?>"
               class="btn btn-proses">Proses</a>
        <?php else: ?>
            <span class="btn btn-disable">✓</span>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
    <td colspan="4">Tidak ada data pemenuhan</td>
</tr>
<?php endif; ?>

</table>
</div>

</div>
</body>
</html>

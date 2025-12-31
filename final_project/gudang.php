<?php
session_start();
if(!isset($_SESSION['login']) || $_SESSION['role']!=='gudang'){
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";

/* ================= SUMMARY ================= */
$barang_masuk_hari = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT SUM(jumlah) total FROM barang_masuk WHERE DATE(tanggal)=CURDATE()")
)['total'] ?? 0;

$barang_keluar_hari = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT SUM(jumlah) total FROM barang_keluar WHERE DATE(tanggal)=CURDATE()")
)['total'] ?? 0;

$menunggu = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM restock WHERE status='diminta'")
)['total'] ?? 0;

$total_stok = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT SUM(stok) total FROM barang")
)['total'] ?? 0;

/* ================= GRAFIK 7 HARI ================= */
$q = mysqli_query($conn,"
    SELECT tanggal,
    SUM(masuk) masuk,
    SUM(keluar) keluar
    FROM (
        SELECT DATE(tanggal) tanggal, SUM(jumlah) masuk, 0 keluar FROM barang_masuk GROUP BY DATE(tanggal)
        UNION ALL
        SELECT DATE(tanggal), 0, SUM(jumlah) FROM barang_keluar GROUP BY DATE(tanggal)
    ) x
    WHERE tanggal >= CURDATE() - INTERVAL 6 DAY
    GROUP BY tanggal
    ORDER BY tanggal
");

$tgl=[]; $masuk=[]; $keluar=[];
while($r=mysqli_fetch_assoc($q)){
    $tgl[] = date('d M',strtotime($r['tanggal']));
    $masuk[] = (int)$r['masuk'];
    $keluar[] = (int)$r['keluar'];
}

/* ================= RESTOCK ================= */
$q_restock = mysqli_query($conn,"
    SELECT r.id_restock,b.nama_barang,r.jumlah
    FROM restock r
    JOIN barang b ON r.id_barang=b.id_barang
    WHERE r.status='diminta'
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Gudang</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body{margin:0;font-family:'Segoe UI';background:#eef5ff}
.sidebar{
    width:240px;height:100vh;position:fixed;background:#4da3ff;color:#fff;padding:20px
}
.sidebar h2{margin:5px 0 10px}
.profile{display:flex;align-items:center;gap:10px;margin:20px 0}
.profile img{width:45px;height:45px;border-radius:50%}
.sidebar a{display:block;padding:10px;color:#fff;text-decoration:none;border-radius:6px;margin-bottom:6px}
.sidebar a.active,.sidebar a:hover{background:rgba(255,255,255,.25)}

.main{margin-left:260px;padding:30px}
.cards{display:grid;grid-template-columns:repeat(4,1fr);gap:15px;margin:20px 0}
.card{background:#4da3ff;color:#fff;padding:20px;border-radius:12px}
.card b{font-size:22px}
.box{background:#fff;padding:20px;border-radius:12px;margin-top:20px}
table{width:100%;border-collapse:collapse}
th,td{padding:10px;border-bottom:1px solid #eee;text-align:center}
.btn{background:#4da3ff;color:#fff;padding:6px 12px;border-radius:6px;text-decoration:none}
</style>
</head>
<body>

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

    <a href="#" class="active">Dashboard</a>
    <a href="../barang/rekap_barang.php">Rekap Barang</a>
    <a href="../restok/pemenuhan_stok.php">Pemenuhan Stok</a>
    <a href="../laporan/index.php">Laporan Barang</a>
    <a href="../auth/logout.php">Logout</a>
</div>

<div class="main">
    <h2>Dashboard</h2>
    <p>Selamat datang, <b><?= $_SESSION['nama'] ?></b>, anda login sebagai gudang</p>

    <div class="cards">
        <div class="card">Barang Masuk Hari Ini<br><b><?= $barang_masuk_hari ?></b></div>
        <div class="card">Barang Keluar Hari Ini<br><b><?= $barang_keluar_hari ?></b></div>
        <div class="card">Menunggu Dipenuhi<br><b><?= $menunggu ?></b></div>
        <div class="card">Total Stok<br><b><?= $total_stok ?></b></div>
    </div>

    <div class="box">
        <h3>Barang Masuk & Keluar (7 Hari Terakhir)</h3>
        <canvas id="grafik"></canvas>
    </div>

    <div class="box">
        <h3>Permintaan Restock yang Perlu Dipenuhi</h3>
        <table>
            <tr><th>Nama Barang</th><th>Diminta</th><th>Aksi</th></tr>
            <?php while($r=mysqli_fetch_assoc($q_restock)): ?>
            <tr>
                <td><?= $r['nama_barang'] ?></td>
                <td><?= $r['jumlah'] ?></td>
                <td>
                    <a href="penuhi.php?id=<?= $r['id_restock'] ?>" class="btn">Penuhi</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>

<script>
new Chart(document.getElementById('grafik'),{
    type:'line',
    data:{
        labels:<?= json_encode($tgl) ?>,
        datasets:[
            {label:'Masuk',data:<?= json_encode($masuk) ?>,borderWidth:2},
            {label:'Keluar',data:<?= json_encode($keluar) ?>,borderWidth:2}
        ]
    }
});
</script>

</body>
</html>

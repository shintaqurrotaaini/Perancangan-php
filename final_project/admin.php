<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";

/* ===== SUMMARY ===== */
$total_barang = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) total FROM barang"))['total'] ?? 0;
$total_habis  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) total FROM barang WHERE stok<=5"))['total'] ?? 0;
$total_masuk_bulan  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(jumlah) total FROM barang_masuk WHERE MONTH(tanggal)=MONTH(CURDATE())"))['total'] ?? 0;
$total_keluar_bulan = mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(jumlah) total FROM barang_keluar WHERE MONTH(tanggal)=MONTH(CURDATE())"))['total'] ?? 0;

/* ===== GRAFIK ===== */
$q_masuk = mysqli_query($conn,"SELECT tanggal,SUM(jumlah) total FROM barang_masuk GROUP BY tanggal");
$tgl=[]; $masuk=[];
while($r=mysqli_fetch_assoc($q_masuk)){
    $tgl[]=$r['tanggal'];
    $masuk[]=(int)$r['total'];
}

$q_keluar = mysqli_query($conn,"SELECT tanggal,SUM(jumlah) total FROM barang_keluar GROUP BY tanggal");
$keluar=[];
while($r=mysqli_fetch_assoc($q_keluar)){
    $keluar[]=(int)$r['total'];
}

/* ===== TABEL ===== */
$q_habis = mysqli_query($conn,"SELECT id_barang,nama_barang,stok FROM barang WHERE stok<=5 LIMIT 5");
$q_aktivitas = mysqli_query($conn,"
    SELECT 'Barang Masuk' aktivitas, tanggal FROM barang_masuk
    UNION ALL
    SELECT 'Barang Keluar', tanggal FROM barang_keluar
    ORDER BY tanggal DESC LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Admin</title>
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
.card{background:#4da3ff;color:#000;padding:20px;border-radius:12px}
.card b{font-size:22px}
.box{background:#fff;padding:20px;border-radius:12px;margin-top:20px}
table{width:100%;border-collapse:collapse}
th,td{padding:10px;border-bottom:1px solid #050404ff;text-align:center}
.btn{background:#4da3ff;color:#fff;padding:6px 12px;border-radius:6px;text-decoration:none}


.main{margin-left:260px;padding:30px}
.card{
    background:#fff;padding:20px;
    border-radius:12px;
    box-shadow:0 4px 12px rgba(0,0,0,.1)
}
.stats{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:20px;margin:20px 0
}
.stats h2{color:#4da3ff;margin:5px 0}
.grid-2{
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:20px;margin-top:20px
}
table{width:100%;border-collapse:collapse}
th,td{
    padding:10px;
    border-bottom:1px solid #eee;
    text-align:center;
    color:#000; /* pastikan teks hitam */
}
th{background:#f1f1f1}

</head>
</style>
</head>
<body>

<!-- ===== SIDEBAR ===== -->
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

    <a class="active">Dashboard</a>
    <a href="../barang/data_barang.php">Data Barang</a>
    <a href="../barang/rekap_barang.php">Rekap Barang</a>
    <a href="../restok/index.php">Permintaan Restock</a>
    <a href="../laporan/index.php">Laporan Barang</a>
    <a href="../auth/logout.php">Logout</a>
</div>

<!-- ===== MAIN ===== -->
<div class="main">
    <h2>Dashboard</h2>
    <p>Selamat datang, <b><?= $_SESSION['nama']; ?></b>, anda login sebagai admin</p>

    <div class="stats">
        <div class="card"><small>Total Barang</small><h2><?= $total_barang ?></h2></div>
        <div class="card"><small>Hampir Habis</small><h2><?= $total_habis ?></h2></div>
        <div class="card"><small>Masuk Bulan Ini</small><h2><?= $total_masuk_bulan ?: 0 ?></h2></div>
        <div class="card"><small>Keluar Bulan Ini</small><h2><?= $total_keluar_bulan ?: 0 ?></h2></div>
    </div>

    <div class="card">
        <canvas id="grafik"></canvas>
    </div>

    <div class="grid-2">
        <div class="card">
            <h3>Barang Hampir Habis</h3>
            <table>
                <tr><th>ID</th><th>Nama</th><th>Stok</th></tr>
                <?php while($b=mysqli_fetch_assoc($q_habis)): ?>
                <tr>
                    <td><?= $b['id_barang'] ?></td>
                    <td><?= $b['nama_barang'] ?></td>
                    <td><?= $b['stok'] ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <div class="card">
            <h3>Aktivitas Terbaru</h3>
            <table>
                <tr><th>Aktivitas</th><th>Tanggal</th></tr>
                <?php while($a=mysqli_fetch_assoc($q_aktivitas)): ?>
                <tr>
                    <td><?= $a['aktivitas'] ?></td>
                    <td><?= $a['tanggal'] ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</div>

<script>
new Chart(document.getElementById('grafik'),{
    type:'line',
    data:{
        labels:<?= json_encode($tgl) ?>,
        datasets:[
            {label:'Masuk',data:<?= json_encode($masuk) ?>,borderColor:'#4da3ff',fill:false},
            {label:'Keluar',data:<?= json_encode($keluar) ?>,borderColor:'#e53935',fill:false}
        ]
    }
});
</script>

</body>
</html>

<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";

/* ======================
   PROSES SETUJUI / TOLAK
====================== */
if (isset($_GET['aksi'], $_GET['id'])) {
    $id = (int)$_GET['id'];

    if ($_GET['aksi'] == 'setujui') {
        mysqli_query($conn,"UPDATE restock SET status='disetujui' WHERE id_restock=$id");
    } elseif ($_GET['aksi'] == 'tolak') {
        mysqli_query($conn,"UPDATE restock SET status='ditolak' WHERE id_restock=$id");
    }

    header("Location: persetujuan.php");
    exit;
}

/* ======================
   DATA RESTOCK DIMINTA
====================== */
$q = mysqli_query($conn,"
    SELECT r.id_restock, r.tanggal, r.jumlah,
           b.nama_barang
    FROM restock r
    JOIN barang b ON r.id_barang = b.id_barang
    WHERE r.status = 'diminta'
    ORDER BY r.tanggal DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Persetujuan Restock</title>

<style>
body{margin:0;font-family:'Segoe UI';background:#eef5ff}

/* ===== SIDEBAR (SAMA DENGAN ADMIN) ===== */
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
    width:1200px;
    box-shadow:0 4px 10px rgba(0,0,0,.1);
}
.card h3{margin:0;font-size:14px;color:#666;}
.card b{font-size:28px;}
.bar{margin:12px 0;}
.bar span{display:inline-block;width:160px;}
.bar div{
    display:inline-block;
    height:18px;
    background:#4da3ff;
    border-radius:5px;
}
.ok{background:#4caf50}
.no{background:#e53935}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:15px
}
th,td{
    padding:15px;
    border-bottom:1px solid #ddd;
    text-align:center;
    color:#333
}
th{background:#f2f2f2}

.btn{
    padding:6px 12px;
    border-radius:6px;
    text-decoration:none;
    font-size:13px;
    margin:0 3px;
    display:inline-block
}




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
            <small>PEMILIK</small>
        </div>
    </div>

    <a href="../dashboard/pemilik.php">Dashboard</a>
    <a class="active">Persetujuan Restock</a>
    <a href="../laporan/index.php">Laporan Barang</a>
    <a href="../auth/logout.php">Logout</a>
</div>

<!-- ===== MAIN ===== -->
<div class="main">
    <h2>Persetujuan Restock</h2>
    <p>Daftar permintaan restock dari admin</p>

    <div class="card">
        <table>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Aksi</th>
            </tr>

            <?php if ($q && mysqli_num_rows($q) > 0): $no=1; ?>
                <?php while($r=mysqli_fetch_assoc($q)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= date('d-m-Y',strtotime($r['tanggal'])) ?></td>
                    <td><?= $r['nama_barang'] ?></td>
                    <td><?= $r['jumlah'] ?></td>
                    <td>
                        <a class="btn ok"
                           href="?aksi=setujui&id=<?= $r['id_restock'] ?>">
                           Setujui
                        </a>
                        <a class="btn no"
                           href="?aksi=tolak&id=<?= $r['id_restock'] ?>"
                           onclick="return confirm('Tolak permintaan ini?')">
                           Tolak
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Tidak ada permintaan restock</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</div>

</body>
</html>

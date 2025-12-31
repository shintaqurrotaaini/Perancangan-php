<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";

$limit = 2; // jumlah data per halaman
$page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page  = max($page, 1);
$offset = ($page - 1) * $limit;


$q_barang = mysqli_query($conn, "
    SELECT * FROM barang
    ORDER BY nama_barang ASC
    LIMIT $limit OFFSET $offset
");
$q_total = mysqli_query($conn, "SELECT COUNT(*) AS total FROM barang");
$total_data = mysqli_fetch_assoc($q_total)['total'];
$total_page = ceil($total_data / $limit);

$no = 1;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Barang | Toko MY_Official</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background:#eef5ff;
}
.sidebar{
    width:240px;
    height:100vh;
    background:#4da3ff;
    position:fixed;
    color:#fff;
    padding:20px;
}
.sidebar h2{margin:5px 0 10px}
.profile{
    display:flex;
    align-items:center;
    gap:10px;
    margin:20px 0;
}
.logo{
    width:45px;
    height:45px;
    border-radius:50%;
    object-fit:cover;
}
.avatar{
    width:40px;
    height:40px;
    border-radius:50%;
    background:#fff;
    color:#4da3ff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:bold;
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
.main{
    margin-left:260px;
    padding:30px;
}
.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.header h2{
    border:3px solid #000;
    padding:10px 30px;
    border-radius:50px;
    background:#fff;
}
.btn{
    background:#41b883;
    color:#fff;
    padding:10px 18px;
    border-radius:8px;
    text-decoration:none;
    font-weight:bold;
}
.table-box{
    margin-top:20px;
    background:#fff;
    padding:20px;
    border-radius:12px;
}
table{
    width:100%;
    border-collapse:collapse;
}
th,td{
    border:2px solid #000;
    padding:10px;
    text-align:center;
    font-size:14px;
}
th{background:#f1f1f1}
img.barang{
    width:60px;
    height:60px;
    object-fit:cover;
    border-radius:8px;
}
.btn-edit{
    background:#ffb74d;
    color:#fff;
    padding:6px 10px;
    border-radius:6px;
}
.btn-hapus{
    background:#e53935;
    color:#fff;
    padding:6px 10px;
    border-radius:6px;
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <small>DATA TOKO</small>
    <h2>TOKO MY_Official</h2>

    <div class="profile">
        <img src="../assets/logo.jpeg" class="logo" alt="logo">
        <div>
            <b><?= $_SESSION['nama']; ?></b><br>
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

<div class="header">
    <h2>👤 Data Barang</h2>
    <a href="tambah_barang.php" class="btn">+ Tambah Barang</a>
</div>

<div class="table-box">
<h3>Tabel Data Barang</h3>

<table>
<tr>
    <th>No</th>
    <th>Gambar</th>
    <th>Nama Barang</th>
    <th>Kategori</th>
    <th>Harga</th>
    <th>Stok</th>
    <th>Satuan</th>
    <th>Aksi</th>
</tr>

<?php while($b = mysqli_fetch_assoc($q_barang)): ?>
<tr>
    <td><?= $no++; ?></td>
    <td>
        <img 
    src="../assets/barang/<?= $b['gambar'] ?: 'BRG1008.jpeg'; ?>" 
    class="barang">   
    </td>
    <td><?= $b['nama_barang']; ?></td>
    <td><?= $b['kategori']; ?></td>
    <td>Rp <?= number_format($b['harga']); ?></td>
    <td><?= $b['stok']; ?></td>
    <td><?= $b['satuan']; ?></td>
    <td>
        <a href="edit_barang.php?id=<?= $b['id_barang']; ?>" class="btn-edit">Edit</a>
        <a href="hapus_barang.php?id=<?= $b['id_barang']; ?>"
           class="btn-hapus"
           onclick="return confirm('Hapus barang ini?')">Hapus</a>
    </td>
</tr>
<?php endwhile; ?>

</table>

<div style="margin-top:15px; text-align:center">

<?php if($page > 1): ?>
    <a href="?page=<?= $page-1 ?>" style="margin-right:6px">« Prev</a>
<?php endif; ?>

<?php for($i=1; $i<=$total_page; $i++): ?>
    <a href="?page=<?= $i ?>"
       style="
       padding:6px 10px;
       margin:0 3px;
       text-decoration:none;
       border-radius:5px;
       <?= $i==$page ? 'background:#4da3ff;color:#fff' : 'background:#eee;color:#333' ?>
       ">
       <?= $i ?>
    </a>
<?php endfor; ?>

<?php if($page < $total_page): ?>
    <a href="?page=<?= $page+1 ?>" style="margin-left:6px">Next »</a>
<?php endif; ?>

</div>

</div>

</div>
</body>
</html>  biar gini loh logo sama tulisannya
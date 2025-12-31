<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";

/* ================= PROSES SIMPAN ================= */
if (isset($_POST['simpan'])) {
    $id_restock = "RS" . date("YmdHis"); // contoh: RS20251217194530
    $id_barang  = $_POST['id_barang'];
    $jumlah     = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];

    $sql = "INSERT INTO restock 
            (id_restock, id_barang, jumlah, status, keterangan)
            VALUES 
            ('$id_restock', '$id_barang', '$jumlah', 'diminta', '$keterangan')";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=success");
        exit;
    } else {
        $error = "Gagal menyimpan data: " . mysqli_error($conn);
    }
}

/* ================= DATA BARANG ================= */
$q_barang = mysqli_query($conn, "SELECT * FROM barang ORDER BY nama_barang ASC");
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
.box{background:#fff;padding:25px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,.1);width:450px;}
input,select,textarea{width:100%;padding:10px;margin-top:6px;margin-bottom:15px;border-radius:6px;border:1px solid #ccc;}
button{padding:10px 20px;border:none;border-radius:6px;background:#4da3ff;color:#fff;font-size:14px;cursor:pointer;}
button:hover{opacity:.9;}
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
    <a href="../laporan/index.php">Laporan</a>
    <a href="../auth/logout.php">Logout</a>
</div>

<div class="main">
    <h2>➕ Buat Permintaan Restock</h2>

    <div class="box">
        <?php if(isset($error)): ?>
            <p style="color:red"><?= $error ?></p>
        <?php endif; ?>

        <form method="post">
            <label>Nama Barang</label>
            <select name="id_barang" required>
                <option value="">-- Pilih Barang --</option>
                <?php while($b = mysqli_fetch_assoc($q_barang)): ?>
                    <option value="<?= $b['id_barang'] ?>">
                        <?= $b['nama_barang'] ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Jumlah Permintaan</label>
            <input type="number" name="jumlah" required min="1">

            <label>Keterangan</label>
            <textarea name="keterangan" placeholder="Alasan permintaan restock..."></textarea>

            <button type="submit" name="simpan">Simpan Permintaan</button>
        </form>
    </div>
</div>

</body>
</html>

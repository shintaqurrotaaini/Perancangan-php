<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ======================
       SIAPKAN DATA
    ====================== */
    $id_barang = "BRG" . rand(1000,9999);

    $data = [
        "id_barang"   => $id_barang,
        "nama_barang" => $_POST['nama'],
        "kategori"    => $_POST['kategori'],
        "harga"       => (int) $_POST['harga'],
        "stok"        => (int) $_POST['stok'],
        "satuan"      => $_POST['satuan'],
        "gambar"      => ""
    ];

    /* ======================
       UPLOAD GAMBAR
    ====================== */
    if (!empty($_FILES['gambar']['name'])) {
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $nama_file = $id_barang . '.' . $ext;

        move_uploaded_file(
            $_FILES['gambar']['tmp_name'],
            "../assets/barang/" . $nama_file
        );

        $data['gambar'] = $nama_file;
    }

    /* ======================
       KIRIM KE API (STORE)
    ====================== */
    $ch = curl_init("http://localhost/project-toko/api/barang/store.php");

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
        CURLOPT_POSTFIELDS => json_encode($data)
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    if ($result['status'] === true) {
        header("Location: data_barang.php?success=1");
        exit;
    } else {
        $error = $result['message'] ?? "Gagal menambahkan barang";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tambah Barang | Toko MY_Official</title>

<style>
body{margin:0;font-family:'Segoe UI',sans-serif;background:#eef5ff}
.sidebar{width:240px;height:100vh;background:#4da3ff;position:fixed;color:#fff;padding:20px}
.sidebar h2{margin:0 0 10px}
.sidebar a{display:block;padding:10px;color:#fff;text-decoration:none;border-radius:6px;margin-bottom:6px}
.sidebar a:hover,.sidebar a.active{background:rgba(255,255,255,.25)}
.brand{display:flex;align-items:center;gap:10px;margin:20px 0}
.brand img{width:45px;height:45px;border-radius:50%}
.main{margin-left:260px;padding:30px}
.card{background:#fff;max-width:650px;padding:35px;border-radius:14px;margin:40px auto}
input{width:100%;padding:12px;margin:8px 0 16px;border:1px solid #ccc;border-radius:8px}
button{width:100%;padding:14px;background:#41b883;color:#fff;border:none;border-radius:10px;font-size:16px}
.error{background:#f8d7da;color:#842029;padding:10px;border-radius:6px;margin-bottom:15px}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <small>DATA TOKO</small>
    <h2>TOKO MY_Official</h2>

    <div class="brand">
        <img src="../assets/logo.jpeg">
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
<div class="card">

<h2 style="text-align:center">Tambah Barang</h2>

<?php if (!empty($error)): ?>
    <div class="error"><?= $error ?></div>
<?php endif; ?>

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
    <input type="file" name="gambar">

    <button type="submit">Simpan Barang</button>
</form>

</div>
</div>

</body>
</html>

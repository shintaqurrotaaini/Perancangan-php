<?php // Pastikan session ada $role = $_SESSION['role'] ?? ''; $username = $_SESSION['username'] ?? 'User'; // Koneksi DB include "db_config.php";


// ======================= QUERY GLOBAL =======================

// Total stok
$q_total_barang = mysqli_query($conn, "SELECT SUM(stok) AS total FROM barang");
$total_barang = mysqli_fetch_assoc($q_total_barang)['total'] ?? 0;

// Barang hampir habis
$q_hampir_habis = mysqli_query($conn, "SELECT * FROM barang WHERE stok < 10");
$barang_hampir_habis = mysqli_num_rows($q_hampir_habis);

// Barang masuk bulan ini
$q_in_month = mysqli_query($conn, "
    SELECT SUM(jumlah) AS total 
    FROM barang_masuk 
    WHERE MONTH(tanggal)=MONTH(CURRENT_DATE())
");
$barang_masuk_bulan = mysqli_fetch_assoc($q_in_month)['total'] ?? 0;

// Barang keluar bulan ini
$q_out_month = mysqli_query($conn, "
    SELECT SUM(jumlah) AS total 
    FROM barang_keluar 
    WHERE MONTH(tanggal)=MONTH(CURRENT_DATE())
");
$barang_keluar_bulan = mysqli_fetch_assoc($q_out_month)['total'] ?? 0;

// Log aktivitas
$q_activity = mysqli_query($conn, "SELECT * FROM log_aktivitas ORDER BY waktu DESC LIMIT 5");

// Restock pending
$q_restock_pending = mysqli_query($conn, "SELECT * FROM restock WHERE status='pending'");

?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body class="p-4">

<div class="card p-4">
    
    <h4>Dashboard</h4>
    <p>Selamat datang, <strong><?= htmlspecialchars($username) ?></strong>  
        (Role: <strong><?= ucfirst(htmlspecialchars($role)) ?></strong>)</p>
    <hr>

    <!-- ======================== ADMIN ======================== -->
    <?php if ($role === 'admin'): ?>

        <h5>Ringkasan Data</h5>
        <div class="row">

            <div class="col-md-3">
                <div class="p-3 bg-primary text-white rounded shadow-sm">
                    <h6>Total Stok</h6>
                    <h3><?= $total_barang ?></h3>
                </div>
            </div>

            <div class="col-md-3">
                <div class="p-3 bg-danger text-white rounded shadow-sm">
                    <h6>Stok Hampir Habis</h6>
                    <h3><?= $barang_hampir_habis ?></h3>
                </div>
            </div>

            <div class="col-md-3">
                <div class="p-3 bg-success text-white rounded shadow-sm">
                    <h6>Barang Masuk (Bulan Ini)</h6>
                    <h3><?= $barang_masuk_bulan ?></h3>
                </div>
            </div>

            <div class="col-md-3">
                <div class="p-3 bg-warning text-dark rounded shadow-sm">
                    <h6>Barang Keluar (Bulan Ini)</h6>
                    <h3><?= $barang_keluar_bulan ?></h3>
                </div>
            </div>

        </div>

        <!-- Barang hampir habis -->
        <h5 class="mt-4">Barang Hampir Habis</h5>
        <table class="table table-striped">
            <tr><th>Nama Barang</th><th>Stok</th></tr>
            <?php while ($b = mysqli_fetch_assoc($q_hampir_habis)): ?>
            <tr>
                <td><?= $b['nama_barang'] ?></td>
                <td><?= $b['stok'] ?></td>
            </tr>
            <?php endwhile; ?>
        </table>

        <!-- Log aktivitas -->
        <h5 class="mt-4">Aktivitas Terbaru</h5>
        <table class="table table-hover">
            <tr><th>User</th><th>Aksi</th><th>Waktu</th></tr>
            <?php while ($log = mysqli_fetch_assoc($q_activity)): ?>
            <tr>
                <td><?= $log['user'] ?></td>
                <td><?= $log['aksi'] ?></td>
                <td><?= $log['waktu'] ?></td>
            </tr>
            <?php endwhile; ?>
        </table>



    <!-- ======================== GUDANG ======================== -->
    <?php elseif ($role === 'gudang'): ?>

        <h5>Ringkasan Gudang</h5>

        <div class="row">

            <div class="col-md-3">
                <div class="p-3 bg-info text-white rounded shadow-sm">
                    <h6>Barang Masuk Hari Ini</h6>
                    <h3>
                        <?php
                        $today_in = mysqli_query($conn, "
                            SELECT SUM(jumlah) AS total 
                            FROM barang_masuk 
                            WHERE tanggal = CURRENT_DATE()
                        ");
                        echo mysqli_fetch_assoc($today_in)['total'] ?? 0;
                        ?>
                    </h3>
                </div>
            </div>

            <div class="col-md-3">
                <div class="p-3 bg-secondary text-white rounded shadow-sm">
                    <h6>Barang Keluar Hari Ini</h6>
                    <h3>
                        <?php
                        $today_out = mysqli_query($conn, "
                            SELECT SUM(jumlah) AS total 
                            FROM barang_keluar 
                            WHERE tanggal = CURRENT_DATE()
                        ");
                        echo mysqli_fetch_assoc($today_out)['total'] ?? 0;
                        ?>
                    </h3>
                </div>
            </div>

            <div class="col-md-3">
                <div class="p-3 bg-warning text-dark rounded shadow-sm">
                    <h6>Permintaan Restock Pending</h6>
                    <h3><?= mysqli_num_rows($q_restock_pending) ?></h3>
                </div>
            </div>

            <div class="col-md-3">
                <div class="p-3 bg-success text-white rounded shadow-sm">
                    <h6>Total Stok</h6>
                    <h3><?= $total_barang ?></h3>
                </div>
            </div>

        </div>

        <!-- Restock -->
        <h5 class="mt-4">Permintaan Restock Perlu Dipenuhi</h5>
        <table class="table table-bordered">
            <tr><th>Nama Barang</th><th>Jumlah</th><th>Aksi</th></tr>
            <?php while ($rs = mysqli_fetch_assoc($q_restock_pending)): ?>
            <tr>
                <td><?= $rs['nama_barang'] ?></td>
                <td><?= $rs['jumlah'] ?></td>
                <td><a href="penuhi.php?id=<?= $rs['id'] ?>" class="btn btn-success btn-sm">Penuhi</a></td>
            </tr>
            <?php endwhile; ?>
        </table>



    <!-- ======================== PEMILIK ======================== -->
    <?php elseif ($role === 'pemilik'): ?>

        <h5>Informasi Pemilik</h5>

        <div class="row">

            <div class="col-md-6">
                <div class="p-3 bg-danger text-white rounded">
                    <h6>Barang Hampir Habis</h6>
                    <h3><?= $barang_hampir_habis ?></h3>
                </div>
            </div>

            <div class="col-md-6">
                <div class="p-3 bg-warning text-dark rounded">
                    <h6>Restock Pending</h6>
                    <h3><?= mysqli_num_rows($q_restock_pending) ?></h3>
                </div>
            </div>

        </div>

    <?php endif; ?>

</div>

</body>
</html>

<div class="sidebar">
    <small>DATA TOKO</small>
    <h2>TOKO MY_Official</h2>

    <div class="profile">
        <img src="../assets/logo.jpeg" class="logo">
        <div>
            <b><?= $_SESSION['nama']; ?></b><br>
          <small><?= htmlspecialchars(strtoupper($_SESSION['role'])) ?></small>

        </div>
    </div>

    <a href="../dashboard/gudang.php">Dashboard</a>
    <a href="../barang/rekap_barang.php" class="active">Rekap Barang</a>
    <a href="../restok/pemenuhan_stok.php">Pemenuhan Stok</a>
    <a href="../laporan/index.php">Laporan Barang</a>
    <a href="../auth/logout.php">Logout</a>
</div>

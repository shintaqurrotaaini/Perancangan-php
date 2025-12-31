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

    <a href="../dashboard/pemilik.php">Dashboard</a>
    <a href="../restok/persetujuan.php">Persetujuan Restock</a>
    <a href="../laporan/index.php" class="active">Laporan Barang</a>
    <a href="../auth/logout.php">Logout</a>
</div>

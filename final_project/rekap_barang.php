<?php
session_start();
if(!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php"); exit;
}

$allowed_roles = ['admin','gudang'];
if (!in_array($_SESSION['role'], $allowed_roles)) {// admin | gudang | pemilik
    exit('Akses ditolak');
}
    include "../config/db.php";
    $role = $_SESSION['role'];

// Ambil data
$q_masuk = mysqli_query($conn,"
    SELECT bm.id_masuk, bm.tanggal, b.nama_barang, bm.jumlah, bm.input_by
    FROM barang_masuk bm
    JOIN barang b ON bm.id_barang = b.id_barang
    ORDER BY bm.tanggal DESC
");

$q_keluar = mysqli_query($conn,"
    SELECT bk.id_keluar, bk.tanggal, b.nama_barang, bk.jumlah, bk.input_by
    FROM barang_keluar bk
    JOIN barang b ON bk.id_barang = b.id_barang
    ORDER BY bk.tanggal DESC
");

$q_stok = mysqli_query($conn,"
    SELECT 
        b.id_barang,
        b.nama_barang,
        b.kategori,
        b.harga,
        b.satuan,
        COALESCE(SUM(bm.jumlah),0) - COALESCE(SUM(bk.jumlah),0) AS stok_saat_ini
    FROM barang b
    LEFT JOIN barang_masuk bm ON b.id_barang = bm.id_barang
    LEFT JOIN barang_keluar bk ON b.id_barang = bk.id_barang
    GROUP BY b.id_barang
    ORDER BY b.nama_barang ASC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Rekap Barang | Toko MY_Official</title>
<style>
body{margin:0;font-family:'Segoe UI';background:#eef5ff}
.sidebar{width:240px;height:100vh;position:fixed;background:#4da3ff;color:#fff;padding:20px}
.sidebar h2{margin:5px 0 10px}
.sidebar a{display:block;padding:10px;color:#fff;text-decoration:none;border-radius:6px;margin-bottom:6px}
.sidebar a:hover,.sidebar a.active{background:rgba(255,255,255,.25)}
.profile{display:flex;align-items:center;gap:10px;margin:20px 0}
.profile img{width:45px;height:45px;border-radius:50%;object-fit:cover}
.main{margin-left:260px;padding:30px}
.header h2{background:#fff;border:3px solid #000;border-radius:50px;padding:10px 30px;display:inline-block}
.tabs{margin:20px 0}
.tabs button{padding:8px 15px;border-radius:6px;border:none;margin-right:8px;background:#e0e0e0;color:#333;font-size:14px;cursor:pointer}
.tabs button.active{background:#4da3ff;color:#fff}
.table-box{background:#fff;padding:20px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,.1)}
table{width:100%;border-collapse:collapse}
th,td{border:1px solid #ccc;padding:10px;font-size:14px;text-align:center}
th{background:#f1f1f1}
.btn-detail{background:#1e88e5;color:#fff;padding:6px 10px;border-radius:6px;text-decoration:none}
</style>
</head>
<body>

<?php
// ===== SIDEBAR SESUAI ROLE =====
if($role=='admin'){
    include "../sidebar/sidebar_admin.php";
}elseif($role=='gudang'){
    include "../sidebar/sidebar_gudang.php";
}
?>



<div class="main">
    <div class="header"><h2> Rekap Barang</h2></div>

    <div class="tabs">
        <button class="tablink active" onclick="openTab(event,'masuk')">Barang Masuk</button>
        <button class="tablink" onclick="openTab(event,'keluar')">Barang Keluar</button>
        <button class="tablink" onclick="openTab(event,'stok')">Stok Saat Ini</button>
    </div>

    <!-- Tab Masuk -->
    <div id="masuk" class="tabcontent" style="display:block">
        <div class="table-box">
        <table>
            <tr><th>No</th><th>Tanggal</th><th>Nama Barang</th><th>Kuantitas</th><th>Oleh</th><th>Aksi</th></tr>
            <?php $no=1; while($r=mysqli_fetch_assoc($q_masuk)): ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= date('d-m-Y',strtotime($r['tanggal'])) ?></td>
                <td><?= $r['nama_barang'] ?></td>
                <td><?= $r['jumlah'] ?></td>
                <td><?= ucfirst($r['input_by']) ?></td>
                <td>
                    <button type="button"
                    class="btn-detail"
                onclick="showDetail('masuk', '<?= $r['id_masuk'] ?>')">
                Detail  
                </button>

            </td>
            </tr>
            <?php endwhile; ?>
        </table>
        </div>
    </div>

    <!-- Tab Keluar -->
    <div id="keluar" class="tabcontent" style="display:none">
        <div class="table-box">
        <table>
            <tr><th>No</th><th>Tanggal</th><th>Nama Barang</th><th>Kuantitas</th><th>Oleh</th><th>Aksi</th></tr>
            <?php $no=1; mysqli_data_seek($q_keluar,0); while($r=mysqli_fetch_assoc($q_keluar)): ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= date('d-m-Y',strtotime($r['tanggal'])) ?></td>
                <td><?= $r['nama_barang'] ?></td>
                <td><?= $r['jumlah'] ?></td>
                <td><?= ucfirst($r['input_by']) ?></td>
                <td>
                    <button type="button"
                class="btn-detail"
                onclick="showDetail('keluar', '<?= $r['id_keluar'] ?>')">
                Detail
            </button>

            </td>
            </tr>
            <?php endwhile; ?>
        </table>
        </div>
    </div>

    <!-- Tab Stok -->
   <div id="stok" class="tabcontent" style="display:none">
    <div class="table-box">
        <table>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok Saat Ini</th>
                <th>Satuan</th>
            </tr>
            <?php $no=1; while($b=mysqli_fetch_assoc($q_stok)): ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= $b['nama_barang'] ?></td>
                <td><?= $b['kategori'] ?></td>
                <td>Rp <?= number_format($b['harga']) ?></td>
                <td><?= $b['stok_saat_ini'] ?></td>
                <td><?= $b['satuan'] ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>
 </div>
</div>

<script>
function openTab(evt, tabName){
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for(i=0;i<tabcontent.length;i++){tabcontent[i].style.display="none";}
    tablinks = document.getElementsByClassName("tablink");
    for(i=0;i<tablinks.length;i++){tablinks[i].classList.remove("active");}
    document.getElementById(tabName).style.display="block";
    evt.currentTarget.classList.add("active");
}

</script>
<script>
function showDetail(tipe, id){
    document.getElementById('detailModal').style.display = 'block';
    document.getElementById('detailContent').innerHTML = 'Loading...';

    fetch('../barang/detail_rekap.php?tipe=' + tipe + '&id=' + id)
        .then(res => res.text())
        .then(data => {
            document.getElementById('detailContent').innerHTML = data;
        });
}

function closeDetail(){
    document.getElementById('detailModal').style.display = 'none';
}
</script>


<div id="detailModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5)">
  <div style="background:#fff; width:500px; margin:100px auto; padding:20px; border-radius:10px">
    <h3>Detail Rekap</h3>
    <div id="detailContent">Loading...</div>
    <br>
    <button onclick="closeDetail()">Tutup</button>
  </div>
  <script>
document.body.addEventListener('click', function(){
    console.log('HALAMAN DIKLIK');
});
</script>

</div>

</body>
</html>

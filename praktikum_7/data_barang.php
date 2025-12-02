<?php
include 'db_config.php';

// Batasi hanya admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("<div class='alert alert-danger'>Akses ditolak.</div>");
}

$error_message = '';
$success_message = '';
$barang_to_edit = null;


// ================================================
// 1. CREATE - Tambah Barang
// ================================================
if (isset($_POST['tambah_barang'])) {

    $nama       = trim($_POST['nama_barang']);
    $kategori   = trim($_POST['kategori']);
    $harga      = intval($_POST['harga']);
    $stok       = intval($_POST['stok']);
    $satuan     = trim($_POST['satuan']);

    // Upload Gambar
    $gambar = '';
    if (!empty($_FILES['gambar']['name'])) {
        $dir = "uploads/";
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $newName = time() . "_" . uniqid() . "." . $ext;
        $target = $dir . $newName;

        if (!in_array($ext, ['jpg','jpeg','png','gif'])) {
            $error_message = "Format gambar tidak valid.";
        } elseif ($_FILES['gambar']['size'] > 500000) {
            $error_message = "Ukuran gambar maksimal 500KB.";
        } else {
            move_uploaded_file($_FILES['gambar']['tmp_name'], $target);
            $gambar = $newName;
        }
    }

    if ($error_message === '') {
        $stmt = $conn->prepare("INSERT INTO barang(nama_barang,kategori,harga,stok,satuan,gambar)
                                VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("ssiiss", $nama, $kategori, $harga, $stok, $satuan, $gambar);

        if ($stmt->execute()) {
            $success_message = "Barang <b>$nama</b> berhasil ditambahkan.";
        } else {
            $error_message = "Gagal tambah barang: " . $stmt->error;
        }
    }
}


// ================================================
// 2. DELETE - Hapus Barang
// ================================================
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);

    // Ambil gambar lama
    $q = $conn->query("SELECT gambar FROM barang WHERE id_barang=$id");
    $img = $q->fetch_assoc()['gambar'] ?? '';

    if ($conn->query("DELETE FROM barang WHERE id_barang=$id")) {

        if ($img && file_exists("uploads/$img")) unlink("uploads/$img");

        header("Location: index.php?page=data_barang&success=" . urlencode("Barang berhasil dihapus"));
        exit;
    }
}


// ================================================
// 3. GET DATA EDIT
// ================================================
if (isset($_GET['edit_id'])) {

    $id = intval($_GET['edit_id']);
    $res = $conn->query("SELECT * FROM barang WHERE id_barang=$id");

    if ($res->num_rows === 1) {
        $barang_to_edit = $res->fetch_assoc();
    } else {
        $error_message = "Data tidak ditemukan.";
    }
}


// ================================================
// 4. UPDATE DATA BARANG
// ================================================
if (isset($_POST['update_barang'])) {

    $id         = intval($_POST['id_barang']);
    $nama       = trim($_POST['nama_barang']);
    $kategori   = trim($_POST['kategori']);
    $harga      = intval($_POST['harga']);
    $stok       = intval($_POST['stok']);
    $satuan     = trim($_POST['satuan']);

    $old_gambar = $_POST['old_gambar'];
    $gambar_baru = $old_gambar;

    // Upload gambar baru jika ada
    if (!empty($_FILES['gambar']['name'])) {
        $dir = "uploads/";
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $newName = time() . "_" . uniqid() . "." . $ext;

        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $dir.$newName)) {
            // Hapus gambar lama
            if ($old_gambar && file_exists("uploads/$old_gambar")) unlink("uploads/$old_gambar");
            $gambar_baru = $newName;
        }
    }

    $stmt = $conn->prepare("UPDATE barang 
                            SET nama_barang=?, kategori=?, harga=?, stok=?, satuan=?, gambar=? 
                            WHERE id_barang=?");

    $stmt->bind_param("ssiissi", $nama, $kategori, $harga, $stok, $satuan, $gambar_baru, $id);

    if ($stmt->execute()) {
        $success_message = "Barang <b>$nama</b> berhasil diperbarui.";
    } else {
        $error_message = "Gagal update: " . $stmt->error;
    }
}


// ================================================
// 5. GET ALL DATA
// ================================================
$data = $conn->query("SELECT * FROM barang ORDER BY id_barang DESC");
$barang_list = $data->fetch_all(MYSQLI_ASSOC);


// Pesan redirect
if (isset($_GET['success'])) {
    $success_message = urldecode($_GET['success']);
}
?>


<!-- ============================
     TAMPILAN DATA BARANG
=============================== -->
<div class="card p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi bi-box-seam me-2"></i> Data Barang</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-1"></i> Tambah Barang
        </button>
    </div>

    <?php if ($error_message): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>


    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-primary">
            <tr>
                <th>#</th>
                <th>Gambar</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Satuan</th>
                <th>Aksi</th>
            </tr>
            </thead>

            <tbody>
            <?php if ($barang_list): ?>
                <?php $no=1; foreach ($barang_list as $b): ?>
                <tr>
                    <td><?= $no++ ?></td>

                    <td>
                        <?php if ($b['gambar'] && file_exists("uploads/".$b['gambar'])): ?>
                            <img src="uploads/<?= $b['gambar'] ?>" style="width:60px;height:60px;border-radius:5px;">
                        <?php else: ?>
                            <i class="bi bi-image" style="font-size:30px;color:#ccc"></i>
                        <?php endif; ?>
                    </td>

                    <td><?= htmlspecialchars($b['nama_barang']) ?></td>
                    <td><?= htmlspecialchars($b['kategori']) ?></td>
                    <td>Rp <?= number_format($b['harga']) ?></td>
                    <td><?= number_format($b['stok']) ?></td>
                    <td><?= htmlspecialchars($b['satuan']) ?></td>

                    <td>
                        <a href="index.php?page=data_barang&edit_id=<?= $b['id_barang'] ?>"
                           class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil"></i>
                        </a>

                        <a href="#" onclick="hapusBarang(<?= $b['id_barang'] ?>, '<?= $b['nama_barang'] ?>')"
                           class="btn btn-danger btn-sm">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8" class="text-center">Belum ada data.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>


<script>
function hapusBarang(id, nama){
    if(confirm("Hapus barang '"+nama+"' ?")){
        window.location.href = "index.php?page=data_barang&delete_id="+id;
    }
}
</script>



<!-- ============================
     MODAL TAMBAH
=============================== -->
<div class="modal fade" id="modalTambah">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="POST" enctype="multipart/form-data">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-plus-square me-2"></i> Tambah Barang
                </h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Nama Barang</label>
                        <input name="nama_barang" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Kategori</label>
                        <input name="kategori" class="form-control" required>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-4">
                        <label class="form-label">Harga</label>
                        <input type="number" name="harga" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Stok Awal</label>
                        <input type="number" name="stok" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Satuan</label>
                        <input name="satuan" class="form-control" required>
                    </div>
                </div>

                <div class="mt-2">
                    <label class="form-label">Gambar</label>
                    <input type="file" name="gambar" class="form-control">
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button name="tambah_barang" class="btn btn-primary">Simpan</button>
            </div>

        </form>
    </div>
</div>



<!-- ============================
     MODAL EDIT
=============================== -->
<?php if ($barang_to_edit): ?>
<div class="modal fade show" id="modalEdit" style="display:block;background:rgba(0,0,0,0.5)">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="id_barang" value="<?= $barang_to_edit['id_barang'] ?>">
            <input type="hidden" name="old_gambar" value="<?= $barang_to_edit['gambar'] ?>">

            <div class="modal-header bg-warning">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square me-2"></i> Edit Barang
                </h5>
                <a href="index.php?page=data_barang" class="btn-close"></a>
            </div>

            <div class="modal-body">

                <div class="row">
                    <div class="col-md-6">
                        <label>Nama Barang</label>
                        <input name="nama_barang" class="form-control" value="<?= $barang_to_edit['nama_barang'] ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label>Kategori</label>
                        <input name="kategori" class="form-control" value="<?= $barang_to_edit['kategori'] ?>" required>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-4">
                        <label>Harga</label>
                        <input type="number" name="harga" class="form-control" value="<?= $barang_to_edit['harga'] ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label>Stok</label>
                        <input type="number" name="stok" class="form-control" value="<?= $barang_to_edit['stok'] ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label>Satuan</label>
                        <input name="satuan" class="form-control" value="<?= $barang_to_edit['satuan'] ?>" required>
                    </div>
                </div>

                <div class="mt-2">
                    <label>Gambar Baru</label>
                    <input type="file" name="gambar" class="form-control">

                    <?php if ($barang_to_edit['gambar']): ?>
                        <small class="d-block mt-1">
                            <img src="uploads/<?= $barang_to_edit['gambar'] ?>" style="width:60px">
                        </small>
                    <?php endif; ?>
                </div>
            </div>

            <div class="modal-footer">
                <a href="index.php?page=data_barang" class="btn btn-secondary">Batal</a>
                <button name="update_barang" class="btn btn-warning">Simpan Perubahan</button>
            </div>

        </form>
    </div>
</div>
<?php endif; ?>

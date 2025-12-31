<?php
session_start();
include "../config/db.php";

if($_SERVER['REQUEST_METHOD']=='POST'){
    $id_barang = $_POST['id_barang'];
    $jumlah = $_POST['jumlah'];
    $tanggal = date('Y-m-d');
    $input_by = $_SESSION['role'];

    $stok = mysqli_fetch_assoc(mysqli_query($conn,"SELECT stok FROM barang WHERE id_barang='$id_barang'"))['stok'];
    if($jumlah > $stok){
        die("Stok tidak cukup!");
    }

    // insert barang_keluar
    mysqli_query($conn,"INSERT INTO barang_keluar (id_keluar,id_barang,jumlah,tanggal,input_by)
        VALUES (UUID(),'$id_barang',$jumlah,'$tanggal','$input_by')");

    // update stok
    mysqli_query($conn,"UPDATE barang SET stok = stok - $jumlah WHERE id_barang='$id_barang'");

    header("Location: rekap_barang.php");
}
?>

<form method="post">
<select name="id_barang" required>
<?php
$q = mysqli_query($conn,"SELECT * FROM barang");
while($b=mysqli_fetch_assoc($q)){
    echo "<option value='{$b['id_barang']}'>{$b['nama_barang']} (Stok: {$b['stok']})</option>";
}
?>
</select>
<input type="number" name="jumlah" min="1" required>
<button type="submit">Submit Barang Keluar</button>
</form>

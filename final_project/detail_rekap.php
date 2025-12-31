<?php
include "../config/db.php";

if (!isset($_GET['tipe']) || !isset($_GET['id'])) {
    exit('Data tidak lengkap');
}

$tipe = $_GET['tipe'];
$id   = $_GET['id'];

if($tipe == 'masuk'){
    $q = mysqli_query($conn,"
        SELECT bm.tanggal, b.nama_barang, bm.jumlah, bm.input_by
        FROM barang_masuk bm
        JOIN barang b ON bm.id_barang=b.id_barang
        WHERE bm.id_masuk='$id'
    ");
}elseif($tipe == 'keluar'){
    $q = mysqli_query($conn,"
        SELECT bk.tanggal, b.nama_barang, bk.jumlah, bk.input_by
        FROM barang_keluar bk
        JOIN barang b ON bk.id_barang=b.id_barang
        WHERE bk.id_keluar='$id'
    ");
}else{
    exit('Tipe tidak valid');
}

$r = mysqli_fetch_assoc($q);
if(!$r){
    exit('Data tidak ditemukan');
}
?>

<table width="100%" border="1" cellpadding="8">
<tr><th>Tanggal</th><td><?= date('d-m-Y',strtotime($r['tanggal'])) ?></td></tr>
<tr><th>Nama Barang</th><td><?= $r['nama_barang'] ?></td></tr>
<tr><th>Kuantitas</th><td><?= $r['jumlah'] ?></td></tr>
<tr><th>Oleh</th><td><?= ucfirst($r['input_by']) ?></td></tr>
</table>

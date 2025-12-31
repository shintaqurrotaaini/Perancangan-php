<?php
session_start();
if(!isset($_SESSION['login'])){
    exit('Akses ditolak');
}

include "../config/db.php";

/* FILTER */
$tipe  = $_GET['tipe'] ?? 'masuk';
$start = $_GET['start'] ?? '';
$end   = $_GET['end'] ?? '';

$where = '';
if($start && $end){
    $where = " AND tanggal BETWEEN '$start' AND '$end'";
}

/* QUERY */
if($tipe == 'masuk'){
    $sql = "
        SELECT bm.tanggal, b.nama_barang, bm.jumlah, bm.input_by
        FROM barang_masuk bm
        JOIN barang b ON bm.id_barang = b.id_barang
        WHERE 1=1 $where
        ORDER BY bm.tanggal ASC
    ";
    $judul = "Laporan_Barang_Masuk";
}else{
    $sql = "
        SELECT bk.tanggal, b.nama_barang, bk.jumlah, bk.input_by
        FROM barang_keluar bk
        JOIN barang b ON bk.id_barang = b.id_barang
        WHERE 1=1 $where
        ORDER BY bk.tanggal ASC
    ";
    $judul = "Laporan_Barang_Keluar";
}

$q = mysqli_query($conn, $sql);
if(!$q){
    die(mysqli_error($conn));
}

/* OUTPUT CSV */
header('Content-Type: text/csv; charset=utf-8');
header("Content-Disposition: attachment; filename=$judul.csv");

$output = fopen('php://output', 'w');

/* HEADER */
fputcsv($output, ['No','Tanggal','Nama Barang','Jumlah','Input By']);

$no = 1;
while($r = mysqli_fetch_assoc($q)){
    fputcsv($output, [
        $no++,
        date('d-m-Y', strtotime($r['tanggal'])),
        $r['nama_barang'],
        $r['jumlah'],
        $r['input_by']
    ]);
}

fclose($output);
exit;

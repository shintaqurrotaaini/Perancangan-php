<?php
session_start();
if(!isset($_SESSION['login'])){
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";
require('../vendor/fpdf/fpdf.php'); // pastikan path sesuai

$filter_tipe = $_GET['tipe'] ?? 'masuk';
$filter_start = $_GET['start'] ?? '';
$filter_end = $_GET['end'] ?? '';

// Filter tanggal
$where_date = '';
if($filter_start && $filter_end){
    $where_date = " AND tanggal BETWEEN '$filter_start' AND '$filter_end'";
}

// Query sesuai tipe
if($filter_tipe=='masuk'){
    $sql = "SELECT bm.id_masuk AS id, bm.tanggal, bm.jumlah, b.nama_barang, bm.input_by 
            FROM barang_masuk bm 
            JOIN barang b ON bm.id_barang=b.id_barang 
            WHERE 1=1 $where_date 
            ORDER BY bm.tanggal DESC";
    $judul = "LAPORAN BARANG MASUK";
} else {
    $sql = "SELECT bk.id_keluar AS id, bk.tanggal, bk.jumlah, b.nama_barang, bk.input_by 
            FROM barang_keluar bk 
            JOIN barang b ON bk.id_barang=b.id_barang 
            WHERE 1=1 $where_date 
            ORDER BY bk.tanggal DESC";
    $judul = "LAPORAN BARANG KELUAR";
}

$q = mysqli_query($conn,$sql);
if(!$q){ die("Query gagal: ".mysqli_error($conn)); }

// Buat PDF
$pdf = new FPDF('P','mm','A4');
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,$judul,0,1,'C');

$pdf->SetFont('Arial','',12);
$pdf->Cell(0,10,'Tanggal Cetak: '.date('d-m-Y'),0,1,'R');
$pdf->Ln(5);

// Header tabel
$pdf->SetFont('Arial','B',12);
$pdf->Cell(10,10,'No',1,0,'C');
$pdf->Cell(35,10,'Tanggal',1,0,'C');
$pdf->Cell(50,10,'Nama Barang',1,0,'C');
$pdf->Cell(40,10,'Input By',1,0,'C');
$pdf->Cell(25,10,'Jumlah',1,1,'C');

// Data
$pdf->SetFont('Arial','',12);
$no=1;
while($r=mysqli_fetch_assoc($q)){
    $pdf->Cell(10,10,$no++,1,0,'C');
    $pdf->Cell(35,10,date('d-m-Y',strtotime($r['tanggal'])),1,0,'C');
    $pdf->Cell(50,10,$r['nama_barang'],1,0);
    $pdf->Cell(40,10,$r['input_by'],1,0,'C');
    $pdf->Cell(25,10,$r['jumlah'],1,1,'C');
}

$pdf->Output("I","Laporan_{$filter_tipe}_".date('dmY').".pdf");

$jenis = ($filter_tipe=='masuk') ? 'Barang Masuk' : 'Barang Keluar';
$penanggungjawab = $_SESSION['role'];
$status = 'dicetak';
$keterangan = "Laporan $jenis periode $filter_start s/d $filter_end";

mysqli_query($conn, "
    INSERT INTO laporan (tanggal, penanggungjawab, jenis_laporan, status, keterangan)
    VALUES (CURDATE(), '$penanggungjawab', '$jenis', '$status', '$keterangan')
");

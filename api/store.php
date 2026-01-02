<?php
header("Content-Type: application/json");
include "../../config/db.php";

/* ======================
   HANYA TERIMA POST
====================== */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "status" => false,
        "message" => "Method tidak diizinkan"
    ]);
    exit;
}

/* ======================
   AMBIL DATA JSON
====================== */
$raw = file_get_contents("php://input");
$input = json_decode($raw, true);

if (!$input) {
    echo json_encode([
        "status" => false,
        "message" => "Format JSON tidak valid"
    ]);
    exit;
}

/* ======================
   VALIDASI DATA
====================== */
$required = ['id_barang','nama_barang','kategori','harga','stok','satuan'];

foreach ($required as $field) {
    if (!isset($input[$field]) || $input[$field] === '') {
        echo json_encode([
            "status" => false,
            "message" => "Data tidak lengkap: $field"
        ]);
        exit;
    }
}

/* ======================
   SIAPKAN DATA
====================== */
$id_barang   = mysqli_real_escape_string($conn, $input['id_barang']);
$nama_barang = mysqli_real_escape_string($conn, $input['nama_barang']);
$kategori    = mysqli_real_escape_string($conn, $input['kategori']);
$harga       = (int) $input['harga'];
$stok        = (int) $input['stok'];
$satuan      = mysqli_real_escape_string($conn, $input['satuan']);
$gambar      = $input['gambar'] ?? '';

/* ======================
   INSERT DATABASE
====================== */
$query = mysqli_query($conn, "
    INSERT INTO barang
    (id_barang, nama_barang, kategori, harga, stok, satuan, gambar)
    VALUES
    ('$id_barang','$nama_barang','$kategori','$harga','$stok','$satuan','$gambar')
");

if ($query) {
    echo json_encode([
        "status" => true,
        "message" => "Barang berhasil ditambahkan"
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Gagal menyimpan data"
    ]);
}

<?php
$host = 'localhost:3307';
$user = 'root';
$password = '';
$database = 'pendaftaran_mahasiswa';

$koneksi = new mysqli($host, $user, $password, $database);

if ($koneksi->connect_error) {
    die("koneksi gagal: " . $koneksi->connect_error);
}

$nama_lengkap = $_POST['nama_lengkap'];
$email = $_POST['email'];
$tanggal_lahir = $_POST['tanggal_lahir'];
$alamat = $_POST['alamat'];
$program_dipilih = $koneksi->real_escape_string($_POST['program_dipilih']);

$sql = "INSERT INTO biodata (nama_lengkap, email, tanggal_lahir, alamat, program_dipilih) VALUES ('$nama_lengkap', '$email', '$tanggal_lahir', '$alamat', '$program_dipilih')";

if ($koneksi->query($sql) === TRUE) {
    echo "data berhasil disimpan!<br>";
    echo "<a href='pendaftaran.html'>tambah mahasiswa baru</a>";
} else {
    echo "error: " . $sql . "<br>" . $koneksi->error;
}

$koneksi->close();
    
?>
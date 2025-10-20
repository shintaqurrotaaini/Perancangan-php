<?php
$host = 'localhost:3307';
$user = 'root';
$password = '';
$database = 'db_pendaftaran';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("koneksi gagal: " . $conn->connect_error);
}

$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];

$sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";

if ($conn->query($sql) === TRUE) {
    echo "data berhasil disimpan!<br>";
    echo "<a href='pendaftaran.html'>tambah user baru</a>";
} else {
    echo "error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
    
?>
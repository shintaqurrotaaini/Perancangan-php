<?php
$servername = "localhost:3307"; 
$username = "root";      
$password = "";          
$dbname = "tutorial_form_per4"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
echo "Koneksi berhasil"; 
?>
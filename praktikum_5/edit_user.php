<?php
include 'koneksi.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID tidak valid.");
}

$id = $_GET['id'];

// 1. Menggunakan Prepared Statement untuk SELECT
$sql = "SELECT username, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);

// Bind parameter: 'i' artinya integer
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    $stmt->close();
    $conn->close();
    die("Data tidak ditemukan.");
}

$stmt->close();
// Tidak menutup koneksi di sini karena akan digunakan di proses_edit_user.php

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Data User</title>
</head>
<body>
    <h2>Edit Data User</h2>
    <form action="proses_edit_user.php" method="POST">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>"> 
        
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($row['username']); ?>" required><br><br>
        
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required><br><br>
        
        <label for="password">Password (Kosongkan jika tidak diubah):</label><br>
        <input type="password" id="password" name="password"><br><br> 
        
        <button type="submit" name="update">Update</button>
    </form>
    <p><a href="index.php">Kembali ke Daftar User</a></p>
</body>
</html>
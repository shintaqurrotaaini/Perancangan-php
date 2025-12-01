<?php
include 'koneksi.php';

if (isset($_POST['simpan'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password_plain = $_POST['password'];

    $password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    $stmt->bind_param("sss", $username, $email, $password_hashed);

    if ($stmt->execute()) {
        // --- Menetapkan Session Status ---
        $_SESSION['status_message'] = "Data user **" . htmlspecialchars($username) . "** berhasil ditambahkan.";
        
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

} else {
    header('Location: index.php');
    exit();
}
?>
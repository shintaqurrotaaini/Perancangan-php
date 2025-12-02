<?php
include 'koneksi.php';

// --- Tambahkan Proteksi Session ---
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // --- Menetapkan Session Status ---
        $_SESSION['status_message'] = "Data user dengan ID **" . htmlspecialchars($id) . "** berhasil dihapus.";
        
        header("Location: index.php");
        exit();
    } else {
        echo "Error deleting record: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    header('Location: index.php');
    exit();
}
?>
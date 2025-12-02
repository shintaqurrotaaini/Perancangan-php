<?php
include 'koneksi.php';


// --- Tambahkan Proteksi Session ---
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}


if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password_plain = $_POST['password'];

    $bind_params_type = "ssi"; 
    $bind_params_values = [$username, $email, $id];

    if (!empty($password_plain)) {
        $password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);
        
        $sql = "UPDATE users SET username=?, email=?, password=? WHERE id=?";
        $bind_params_type = "sssi"; 
        $bind_params_values = [$username, $email, $password_hashed, $id];
    } else {
        $sql = "UPDATE users SET username=?, email=? WHERE id=?";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($bind_params_type, ...$bind_params_values);


    if ($stmt->execute()) {
        // --- Menetapkan Session Status ---
        $_SESSION['status_message'] = "Data user **" . htmlspecialchars($username) . "** berhasil diubah.";
        
        header("Location: index.php");
        exit();
    } else {
        echo "Error updating record: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

} else {
    header('Location: index.php');
    exit();
}
?>
<?php
include 'koneksi.php'; 

// Jika user sudah login, arahkan ke index.php
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$login_error = '';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password_plain = $_POST['password'];

    // 1. Menggunakan Prepared Statement untuk SELECT user berdasarkan username
    $sql = "SELECT id, username, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
     
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // 2. Verifikasi Password (Proses Authentikasi)
        if (password_verify($password_plain, $user['password'])) {
            // Password cocok. Login berhasil!

            // --- Menetapkan Session Login ---
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Redirect ke halaman utama
            header('Location: index.php');
            exit();
        } else {
            // Jika password salah (meskipun username benar)
            $login_error = "Username atau Password salah.";
        }
    } else {
        // Jika username tidak ditemukan
        $login_error = "Username atau Password salah.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login User</title>
    <style>
        /* CSS Untuk Login Page */
        body { font-family: sans-serif; background-color: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); width: 300px; }
        h2 { text-align: center; color: #333; }
        input[type="text"], input[type="password"] { 
            width: 100%; 
            padding: 10px; 
            margin: 8px 0 15px 0; 
            display: inline-block; 
            border: 1px solid #ccc; 
            border-radius: 4px; 
            box-sizing: border-box; /* Agar padding tidak menambah lebar */
        }
        button { 
            background-color: #007bff; 
            color: white; 
            padding: 10px 15px; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            width: 100%; 
        }
        button:hover { background-color: #0056b3; }
        .error { color: #dc3545; margin-bottom: 15px; text-align: center; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Login Aplikasi</h2>
        <?php if (!empty($login_error)): ?>
            <p class="error"><?php echo $login_error; ?></p>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit" name="login">Login</button>
        </form>
    </div>
</body>
</html>
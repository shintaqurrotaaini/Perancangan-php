<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../config/db.php";
include "../config/mail.php"; // PHPMailer (kalau belum ada, boleh dikomen)

// ================= PROSES REGISTER =================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_user  = "USR" . rand(1000,9999);
    $nama     = trim($_POST['nama']);
    $email    = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // 🔒 ROLE DIKUNCI (HAKIKAT SISTEM)
    $role = 'gudang';

    // VALIDASI
    if ($nama === '' || $email === '' || $username === '' || $password === '') {
        $error = "Semua data wajib diisi";
    } else {

        // CEK DUPLIKAT USERNAME / EMAIL
        $cek = mysqli_query($conn, "
            SELECT id_user FROM login 
            WHERE username='$username' OR email='$email'
        ");

        if (!$cek) {
            die("QUERY ERROR: " . mysqli_error($conn));
        }

        if (mysqli_num_rows($cek) > 0) {
            $error = "Username atau email sudah terdaftar";
        } else {

            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "
                INSERT INTO login (id_user, nama, email, username, password, role)
                VALUES ('$id_user','$nama','$email','$username','$password_hash','$role')
            ";

            if (mysqli_query($conn, $sql)) {

                // 📧 Kirim email (jika mail.php aktif)
                if (function_exists('kirimEmail')) {
                    kirimEmail($email, $nama, $role);
                }

                header("Location: login.php?register=success");
                exit;

            } else {
                $error = mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi Gudang | Sistem Toko MY</title>

    <link rel="stylesheet" href="../assets/style.css">

    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }

        body {
            background: linear-gradient(135deg, #1cc88a, #4e73df);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            background: #fff;
            width: 430px;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 15px 30px rgba(0,0,0,.15);
        }

        h2 { text-align: center; margin-bottom: 5px; }
        .subtitle {
            text-align: center;
            font-size: 14px;
            color: #777;
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        input:focus {
            border-color: #1cc88a;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #1cc88a;
            border: none;
            border-radius: 6px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover { background: #17a673; }

        .error {
            background: #f8d7da;
            color: #842029;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            text-align: center;
            font-size: 14px;
        }

        .login-link {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }

        .login-link a {
            color: #4e73df;
            text-decoration: none;
        }
    </style>
</head>

<body>

<div class="card">
    <h2>Registrasi Akun Gudang</h2>
    <p class="subtitle">Sistem Informasi Toko MY_Official</p>

    <?php if (isset($error)): ?>
        <div class="error"><?= $error; ?></div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
        <input type="text" name="nama" placeholder="Nama Lengkap" required>
        <input type="email" name="email" placeholder="Email Aktif" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>

        <!-- ROLE DIKUNCI -->
        <input type="hidden" name="role" value="gudang">

        <button type="submit">Daftar sebagai Gudang</button>
    </form>

    <div class="login-link">
        Sudah punya akun? <a href="login.php">Login</a>
    </div>
</div>

</body>
</html>

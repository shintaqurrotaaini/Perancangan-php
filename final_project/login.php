<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | Sistem Toko MY_Official</title>

    <link rel="stylesheet" href="../assets/style.css">

    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }

        body {
            background: linear-gradient(135deg, #4e73df, #1cc88a);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            background: #fff;
            width: 400px;
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
            border-color: #4e73df;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #4e73df;
            border: none;
            border-radius: 6px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover { background: #2e59d9; }

        .error {
            background: #f8d7da;
            color: #842029;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            text-align: center;
            font-size: 14px;
        }

        .success {
            background: #d1e7dd;
            color: #0f5132;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            text-align: center;
            font-size: 14px;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }

        .footer a {
            color: #1cc88a;
            text-decoration: none;
        }
    </style>
</head>

<body>

<div class="card">
    <h2>Sistem Toko MY_Official</h2>
    <p class="subtitle">Silakan login untuk masuk ke sistem</p>

    <?php if (isset($_GET['error'])): ?>
        <div class="error">Username atau password salah</div>
    <?php endif; ?>

    <?php if (isset($_GET['register'])): ?>
        <div class="success">Registrasi berhasil, silakan login</div>
    <?php endif; ?>

    <form action="login_process.php" method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <div class="footer">
        Belum punya akun?
        <a href="register.php">Registrasi Gudang</a>
    </div>
</div>

</body>
</html>

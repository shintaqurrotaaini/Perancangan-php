<?php

$feedback = "";
$data_input = [];

$host = "localhost:3307";
$user = "root";      
$pass = "";          
$db   = "latihan_3";
$koneksi = null;

$koneksi = new mysqli($host, $user, $pass, $db);

if ($koneksi->connect_error) {
    
    die("Koneksi Database Gagal: " . $koneksi->connect_error);
}

// --- Bagian 2: Logika Proses Registrasi ---
if (isset($_POST['register'])) {
    // Ambil data dari form
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $konfirmasi_password = $_POST['konfirmasi_password'];

    // Validasi Password
    if ($password !== $konfirmasi_password) {
        $feedback = "<p style='color: red;'> **Gagal Registrasi:** Konfirmasi Password tidak cocok!</p>";
    } else {
        // Enkripsi Password (WAJIB!)
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        
        $stmt = $koneksi->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password_hash);

        if ($stmt->execute()) {
            $feedback = "<p style='color: green; font-weight: bold;'> **Registrasi Berhasil!** Data Anda telah tersimpan di database.</p>";
            
           
            $data_input = [
                'Username' => htmlspecialchars($username),
                'Email' => htmlspecialchars($email),
                'Password' => '*********', 
                'Status' => 'Tersimpan'
            ];
            
        } else {
            // Error handling (misal: duplikasi username/email)
            if ($koneksi->errno == 1062) {
                 $feedback = "<p style='color: red;'> **Gagal Registrasi:** Username atau Email sudah terdaftar.</p>";
            } else {
                $feedback = "<p style='color: red;'> **Gagal Registrasi:** " . $stmt->error . "</p>";
            }
        }
        $stmt->close();
    }
}


if ($koneksi) {
    $koneksi->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>LATIHAN 3 - Form Registrasi</title>
    <style>
        /* Styling CSS untuk tampilan yang bersih */
        body { font-family: sans-serif; background: #e9ecef; display: flex; justify-content: center; align-items: flex-start; min-height: 100vh; padding-top: 50px; }
        .main-container { display: flex; gap: 50px; }
        .card { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .form-container { width: 350px; }
        .output-container { width: 400px; }
        h2 { text-align: center; margin-bottom: 25px; color: #333; }
        label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9em; }
        input { width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ced4da; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #28a745; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; width: 100%; font-size: 1em; font-weight: bold; }
        button:hover { background-color: #218838; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .data-table td { padding: 10px; border: 1px solid #dee2e6; }
        .data-table td:first-child { font-weight: bold; width: 40%; background: #f8f9fa; }
    </style>
</head>
<body>

<div class="main-container">
    
    <div class="card form-container">
        <h2>Form Registrasi</h2>
        <form action="" method="POST">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <label for="konfirmasi_password">Konfirmasi Password</label>
            <input type="password" id="konfirmasi_password" name="konfirmasi_password" required>

            <button type="submit" name="register">Daftar</button>
        </form>
    </div>

    <?php if ($feedback): ?>
    <div class="card output-container">
        <h2>Hasil Output</h2>
        
        <div class="feedback">
            <?php echo $feedback; ?>
        </div>
        
        <?php if (!empty($data_input)): ?>
            <h3>Detail Inputan Anda:</h3>
            <table class="data-table">
                <?php foreach ($data_input as $key => $value): ?>
                    <tr>
                        <td><?php echo $key; ?></td>
                        <td><?php echo $value; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

    </div>
    <?php endif; ?>
</div>

</body>
</html>
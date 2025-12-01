<?php
include 'koneksi.php';

// Ambil data user dari database
$sql = "SELECT id, username, email FROM users";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Data User</title>
    <style>
        /* CSS Dasar */
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: #f4f7f6; /* Warna latar belakang */
            margin: 0; 
            padding: 20px; 
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Styling Header */
        .page-header {
            text-align: center;
            margin-bottom: 25px;
            color: #343a40;
            font-weight: 600;
        }

        /* Styling Form (Area Tambah Data) */
        .form-section {
            padding: 20px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            margin-bottom: 30px;
            background-color: #f8f9fa;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: calc(100% - 12px); /* Menyesuaikan dengan padding */
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-sizing: border-box;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .btn-submit {
            background-color: #007bff; /* Biru Primer */
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-submit:hover {
            background-color: #0056b3;
        }
        
        /* Styling Notifikasi Session */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-weight: 500;
        }
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }

        /* Styling Tabel */
        table { 
            width: 100%; 
            border-collapse: separate; 
            border-spacing: 0;
            margin-top: 20px; 
            border: 1px solid #dee2e6;
            border-radius: 6px;
            overflow: hidden; 
        }
        th, td { 
            border-bottom: 1px solid #dee2e6; 
            padding: 12px 15px; 
            text-align: left; 
        }
        /* Header Tabel */
        th { 
            background-color: #28a745; 
            color: white; 
            font-weight: 600; 
            border: none;
        }
        tr:last-child td {
            border-bottom: none; 
        }
        tbody tr:hover {
            background-color: #f1f1f1;
        }

        /* Styling Tombol Aksi */
        .action-link { 
            text-decoration: none; 
            padding: 6px 12px; 
            margin-right: 5px; 
            border-radius: 4px; 
            font-weight: 500;
            display: inline-block;
        }
        .edit { 
            background-color: #28a745; 
            color: white; 
        }
        .hapus { 
            background-color: #dc3545; 
            color: white; 
        }
    </style>
</head>
<body>
    <div class="container"> 
    
    <h2 class="page-header">Manajemen Data User</h2>

    <?php
    // --- Tampilkan Notifikasi Session di sini ---
    if (isset($_SESSION['status_message'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['status_message']; ?>
        </div>
    <?php
        // Hapus pesan setelah ditampilkan
        unset($_SESSION['status_message']); 
    endif;
    ?>

    <div class="form-section">
        <h3 style="margin-top: 0;">Tambah Data User</h3>
        <form action="proses_tambah_user.php" method="POST">
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" required><br>
            
            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" required><br>
            
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br>
            
            <button type="submit" name="simpan" class="btn-submit">Simpan</button>
        </form>
    </div>

    <hr style="border: 0; border-top: 1px solid #ccc;">

    <h2 class="page-header" style="margin-top: 30px;">Daftar User</h2>
    
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th> 
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1; 
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>"; 
                    echo "<td>" . htmlspecialchars($row["id"]). "</td>";
                    echo "<td>" . htmlspecialchars($row["username"]). "</td>";
                    echo "<td>" . htmlspecialchars($row["email"]). "</td>";
                    echo "<td>";
                    echo "<a href='edit_user.php?id=" . htmlspecialchars($row["id"]) . "' class='action-link edit'>Edit</a>";
                    echo "<a href='hapus_user.php?id=" . htmlspecialchars($row["id"]) . "' class='action-link hapus' onclick='return confirm(\"Apakah Anda yakin ingin menghapus user ini?\")'>Hapus</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Tidak ada data user.</td></tr>";
            }
            $stmt->close();
            $conn->close();
            ?>
        </tbody>
    </table>
    
    </div>
</body>
</html>
<?php
include 'koneksi.php';

// ==========================================================
// 1. KODE PROTEKSI SESSION
// Cek apakah session user_id sudah diset (artinya user sudah login dari login.php)
if (!isset($_SESSION['user_id'])) {
    // Jika belum login, arahkan ke halaman login
    header('Location: login.php');
    exit();
}
// ==========================================================


// ==========================================================
// 2. INTEGRASI PAGINATION (Menggantikan KODE PENGAMBILAN DATA USER lama)
// Include file pagination.php untuk menjalankan query dengan LIMIT
include 'pagination.php';
// Variabel $result_pagination, $pagenum, dan $page_rows sekarang tersedia.
// ==========================================================

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
        /* ... (Sisa CSS yang sudah Anda berikan) ... */

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
        
        /* CSS KHUSUS PAGINATION (Ditambahkan) */
        .pagination-controls {
            text-align: center;
            margin-top: 20px;
            padding: 10px 0;
            border-top: 1px solid #dee2e6;
        }
        .pagination-link {
            background-color: #6c757d; 
        }
        .pagination-link:hover {
            background-color: #5a6268;
        }
        .current-page {
            font-weight: bold;
            color: #343a40;
            padding: 6px 12px;
            border: 1px solid #adb5bd;
            border-radius: 4px;
            background-color: #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container"> 
    
    <h2 class="page-header">Manajemen Data User</h2>

    <p style="text-align: right; margin-top: -30px; margin-bottom: 20px;">
        Selamat datang, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>! 
        | 
        <a href="logout.php" style="color: #dc3545; text-decoration: none; font-weight: 500;">Logout</a>
    </p>
    <hr>

    <?php
    // Tampilkan Notifikasi Session status_message
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
            // PERUBAHAN 1: Hitung nomor urut yang benar
            // $pagenum dan $page_rows didapat dari pagination.php
            $no = (($pagenum - 1) * $page_rows) + 1; 
            
            // PERUBAHAN 2: Gunakan $result_pagination dari pagination.php
            if ($result_pagination->num_rows > 0) {
                while($row = $result_pagination->fetch_assoc()) {
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
            
            // PERUBAHAN 3: Tutup statement dari pagination.php dan koneksi
            $nquery->close(); // $nquery adalah statement dari pagination.php
            $conn->close();
            ?>
        </tbody>
    </table>

    <div class="pagination-controls">
        <?php echo $paginationCtrls; // Menampilkan kontrol yang dibuat di pagination.php ?>
    </div>
    
    </div>
</body>
</html>
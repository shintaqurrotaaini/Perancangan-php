<?php
// PASTIKAN AKSES KE VARIABEL KONEKSI GLOBAL DARI koneksi.php
global $conn; 

$page_rows = 1; // Jumlah baris per halaman (Anda bisa ubah)

// 1. Hitung total baris
$query = $conn->prepare("SELECT count(id) FROM users"); 
$query->execute();
$row_result = $query->get_result();
$row = $row_result->fetch_row();
$rows = $row[0]; // Total baris data
$query->close();

$last = ceil($rows / $page_rows); // Total halaman

if ($last < 1) {
    $last = 1;
}

// 2. Tentukan halaman saat ini (Current Page Number)
$pagenum = 1; // Nilai default

// Pengecekan aman untuk mengatasi "Undefined index: pn"
if (isset($_GET['pn'])) { 
    $pagenum = preg_replace('#[^0-9]#', '', $_GET['pn']);
}

// Memastikan $pagenum berada dalam batas yang valid
if ($pagenum < 1) {
    $pagenum = 1;
} else if ($pagenum > $last) {
    $pagenum = $last;
}

// 3. Buat klausul LIMIT untuk query SQL
// Syntax LIMIT: LIMIT [offset], [row_count]
$limit = 'LIMIT ' . ($pagenum - 1) * $page_rows . ',' . $page_rows;

// 4. Ambil data untuk halaman saat ini
// VARIABEL PENTING: $result_pagination dan $nquery akan digunakan di index.php
$sql = "SELECT id, username, email FROM users ORDER BY id ASC $limit";
$nquery = $conn->prepare($sql);
$nquery->execute();
$result_pagination = $nquery->get_result(); 

// 5. Bangun Kontrol Pagination
$paginationCtrls = '';

if ($last != 1) {
    
    // Tombol Previous
    if ($pagenum > 1) {
        $previous = $pagenum - 1;
        $paginationCtrls .= '<a href="' . $_SERVER['PHP_SELF'] . '?pn=' . $previous . '" class="action-link pagination-link">Previous</a> &nbsp; &nbsp; ';
        
        for ($i = $pagenum - 4; $i < $pagenum; $i++){
            if ($i > 0) {
                $paginationCtrls .= '<a href="' . $_SERVER['PHP_SELF'] . '?pn=' . $i . '" class="action-link pagination-link">' . $i . '</a> &nbsp; ';
            }
        }
    }

    // Tampilkan halaman saat ini
    $paginationCtrls .= '<span class="current-page">[' . $pagenum . ']</span> &nbsp; '; 

    // Tampilkan beberapa link setelah halaman saat ini
    for ($i = $pagenum + 1; $i <= $last; $i++) {
        $paginationCtrls .= '<a href="' . $_SERVER['PHP_SELF'] . '?pn=' . $i . '" class="action-link pagination-link">' . $i . '</a> &nbsp; ';
        if ($i >= $pagenum + 4) {
            break;
        }
    }

    // Tombol Next
    if ($pagenum != $last) {
        $next = $pagenum + 1;
        $paginationCtrls .= ' &nbsp; &nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?pn=' . $next . '" class="action-link pagination-link">Next</a> ';
    }
}
?>
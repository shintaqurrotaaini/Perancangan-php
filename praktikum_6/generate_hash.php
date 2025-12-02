<?php
$new_password = "reset123"; // Ganti dengan password baru yang Anda inginkan
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

echo "Password Baru (reset123) telah di-hash menjadi:<br>";
echo "<strong>" . $hashed_password . "</strong>";

// Hapus file ini setelah Anda mendapatkan hasil hash
?>
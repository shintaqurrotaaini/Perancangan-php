<?php
function tambah_log($conn, $id_user, $aktivitas) {
    $id_log = uniqid('LOG-'); // karena varchar
    $waktu  = date('Y-m-d H:i:s');

    mysqli_query($conn, "
        INSERT INTO log_aktivitas (id_log, id_user, aktivitas, waktu)
        VALUES ('$id_log', '$id_user', '$aktivitas', '$waktu')
    ");
}

<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function kirimEmail($email, $nama, $role) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        // EMAIL SISTEM
        $mail->Username = 'shintaqurrotaaini@gmail.com';
        $mail->Password = 'eltentxykzfgtepk'; // contoh TANPA spasi
         // WAJIB TANPA SPASI

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // ⏱️ KUNCI ANTI LOADING
        $mail->Timeout = 5;

        $mail->setFrom('shintaqurrotaaini@gmail.com', 'Sistem Toko MY_Official');
        $mail->addAddress($email, $nama);

        $mail->isHTML(true);
        $mail->Subject = 'Registrasi Berhasil';
        $mail->Body = "
            <h3>Halo $nama</h3>
            <p>Akun anda berhasil didaftarkan sebagai <b>$role</b>.</p>
        ";

        $mail->send();
    } catch (Exception $e) {
        // JANGAN echo error, biar tidak ngeblok register
    }
}

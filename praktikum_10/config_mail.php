<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// === Konfigurasi Kredensial Email (DISARANKAN MENGGUNAKAN VARIABEL LINGKUNGAN) ===
// Dalam contoh ini, tetap hardcoded, tapi pastikan ini adalah APP PASSWORD!
$SENDER_EMAIL = 'shintaqurrotaaini@gmail.com'; 
$SENDER_PASS = 'sezc ufgs wypm axvm';   

function sendLoginAlert($username, $authType) {
    global $SENDER_EMAIL, $SENDER_PASS; // Akses variabel global

    $mail = new PHPMailer(true); //

    try {
        // === SMTP Server Gmail (Menggunakan SMTPS port 465) ===
        $mail->isSMTP(); //
        $mail->Host       = 'smtp.gmail.com'; //
        $mail->SMTPAuth   = true; //

        // KREDENSIAL PENGIRIM
        $mail->Username   = $SENDER_EMAIL; //
        $mail->Password   = $SENDER_PASS;   //

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Gunakan SMTPS
        $mail->Port       = 465; // Port untuk SMTPS

        // === EMAIL TUJUAN ===
        $mail->setFrom($SENDER_EMAIL, 'Sistem Login Notif'); //
        $mail->addAddress('32.shintaqurrotaaini@gmail.com'); //

        // === Format Email ===
        $mail->isHTML(true); //

        // Format tanggal untuk subject
        $tanggal = date("Ymd_His");  //

        // SUBJECT
        $mail->Subject = "ALERT {$tanggal}_{$authType}"; //

        // Body email
        $mail->Body = "
            <h3>Alert Login Sistem</h3>
            <p><b>User:</b> {$username}</p>
            <p><b>Jenis Autentikasi:</b> {$authType}</p>
            <p><b>Waktu Login:</b> " . date("Y-m-d H:i:s") . "</p>
            <hr>
            <p>Notifikasi dikirim otomatis oleh sistem.</p>
        "; //

        $mail->send(); //
        return true;

    } catch (Exception $e) {
        // Hanya log error, jangan tampilkan ke pengguna di lingkungan produksi
        // echo "Error: {$mail->ErrorInfo}";
        return false;
    }
}
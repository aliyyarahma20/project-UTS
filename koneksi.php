<?php

// Informasi koneksi database
$host = "localhost";
$user = "root";
$password = "";
$database = "poliklinik-1";

// Fungsi untuk menyimpan log error
function logError($error_message) {
    $log_file = 'error_log.txt';
    $current_time = date('Y-m-d H:i:s');
    $message = "[$current_time] - $error_message" . PHP_EOL;
    file_put_contents($log_file, $message, FILE_APPEND);
}

// Membuat koneksi
$koneksi = mysqli_connect($host, $user, $password, $database);

// Cek koneksi dan tampilkan pesan
if (!$koneksi) {
    $error_message = "Koneksi database gagal : " . mysqli_connect_error();
    echo $error_message;

    // Simpan pesan error ke log file
    logError($error_message);

    // Anda bisa menghentikan eksekusi jika koneksi gagal
    die("Aplikasi tidak dapat berjalan tanpa koneksi database.");
}

// Opsi tambahan: verifikasi koneksi ulang sebelum eksekusi query
function checkConnection($connection) {
    if (!mysqli_ping($connection)) {
        $error_message = "Koneksi terputus: " . mysqli_error($connection);
        echo $error_message;
        logError($error_message);
        die("Aplikasi tidak dapat melanjutkan tanpa koneksi yang stabil.");
    }
}

// Contoh pemanggilan checkConnection sebelum query
checkConnection($koneksi);

?>

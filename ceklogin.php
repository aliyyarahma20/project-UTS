<?php
session_start();
include 'koneksi.php';

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Gunakan prepared statement dengan pengecekan error
    $query = "SELECT id_staff, username, nama, posisi FROM staff WHERE username = ? AND password = ?";
    
    // Cek apakah prepared statement berhasil dibuat
    if ($stmt = mysqli_prepare($koneksi, $query)) {
        mysqli_stmt_bind_param($stmt, "ss", $username, $password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) === 1) {
            // Jika user ditemukan
            $user = mysqli_fetch_assoc($result);
            
            // Simpan data ke session
            $_SESSION['id_staff'] = $user['id_staff'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['posisi'] = $user['posisi'];

            // Redirect berdasarkan posisi
            if (strtolower($user['posisi']) == 'staff') {
                header("Location: index.php");
            } elseif (strtolower($user['posisi']) == 'dokter') {
                header("Location: dashboard_dokter.php");
            }
            exit;
        } else {
            // Jika login gagal
            header("Location: login.php?error=1");
            exit;
        }
        
        mysqli_stmt_close($stmt);
    } else {
        // Jika ada error dalam prepared statement
        echo "Error dalam query: " . mysqli_error($koneksi);
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}
?>
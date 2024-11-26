<?php
// Include koneksi database
include 'koneksi.php';

// Data untuk diinput
$nama_pasien = "Test Pasien";
$no_bpjs = "123456789";
$waktu_daftar = date('Y-m-d H:i:s');
$status = 'Terdaftar';

// Query untuk memasukkan data ke dalam tabel pendaftaran
$query = "INSERT INTO pendaftaran (nama_pasien, no_bpjs, waktu_daftar, status_pendaftaran) 
          VALUES ('$nama_pasien', '$no_bpjs', '$waktu_daftar', '$status')";

// Eksekusi query dan cek apakah berhasil
if (mysqli_query($koneksi, $query)) {
    echo "Data berhasil ditambahkan.";
} else {
    echo "Terjadi kesalahan: " . mysqli_error($koneksi);
}

// Tutup koneksi setelah selesai
mysqli_close($koneksi);
?>

<?php 
// koneksi database
include 'koneksi.php';
 
// menangkap data id yang di kirim dari url
$id = $_GET['id'];
 
 
// menghapus data dari database
mysqli_query($koneksi,"delete from report where id_report='$id'");

 
// mengalihkan halaman kembali ke index.php
header("location:riwayat.php");
 
?>
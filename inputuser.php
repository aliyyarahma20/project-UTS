<?php 
// koneksi database
include 'koneksi.php';
 
// menangkap data yang di kirim dari form
$nama      = $_POST['nama'];
$no_bpjs = $_POST['no_bpjs'];
$password   = $_POST['password'];
$gender      = $_POST['gender'];
$favgame     = $_POST['favgame'];
$favfood     = $_POST['favfood'];

// menginput data ke database
mysqli_query($koneksi,"insert into favorit values('','$username','$password','$name','$favgame', '$favfood', '$gender')");
 
// mengalihkan halaman kembali ke index.php
header("location:tampil.php");

?>
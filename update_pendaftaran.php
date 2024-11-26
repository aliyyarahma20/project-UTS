<?php

//include koneksi database
include('koneksi.php');

//get data dari form
$id = $_POST['id_pendaftaran'];
$name      = $_POST['nama_pasien'];
$username = $_POST['no_bpjs'];
$password   = $_POST['waktu_daftar'];
$gender      = $_POST['status_daftar'];


//query update data ke dalam database berdasarkan ID
$query = "UPDATE pendaftaran SET nama_pasien = '$name', no_bpjs = '$username', waktu_daftar = '$password', status_daftar = '$gender' WHERE id_pendaftaran = '$id'";

//kondisi pengecekan apakah data berhasil diupdate atau tidak
if($koneksi->query($query)) {
    //redirect ke halaman tampil.php 
    header("location: pendaftaran.php");
} else {
    //pesan error gagal update data
    echo "Data Gagal Diupate!";
}

?>
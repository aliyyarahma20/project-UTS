<?php
include 'koneksi.php';

// Proses penambahan data ketika form disubmit
if (isset($_POST['submit'])) {
    // Ambil data dari form dan sanitasi
    $name = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $gender = mysqli_real_escape_string($koneksi, $_POST['gender']);
    $no_telepon = mysqli_real_escape_string($koneksi, $_POST['no_telepon']);
    $posisi = mysqli_real_escape_string($koneksi, $_POST['posisi']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    // Insert data ke dalam database
    $query = "INSERT INTO staff (nama, gender, no_telepon, posisi, username, password) 
              VALUES ('$name', '$gender', '$no_telepon', '$posisi', '$username', '$password')";

    if (mysqli_query($koneksi, $query)) {
        // Redirect jika berhasil tambah data
        header("Location: staff.php");
        exit();
    } else {
        echo "Terjadi kesalahan saat menambah data.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Staff Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="tambah_user_style.css">
</head>
<body>
    <div class="add-user-container">
        <div class="add-user-card">
            <div class="add-user-header">
                <h4 class="add-user-title">Tambah Staff Baru</h4>
                <button class="add-user-back-btn" onclick="window.location.href='staff.php'">Back</button>
            </div>
            <div class="add-user-body">
                <form method="post" action="tambah_staff.php">
                    <div class="add-user-form-group">
                        <label>Nama</label>
                        <input type="text" name="nama" required>
                    </div>
                    <div class="add-user-form-group">
                        <label>Gender</label>
                        <select name="gender" required>
                            <option value="">Pilih Gender</option>
                            <option value="Wanita">Wanita</option>
                            <option value="Pria">Pria</option>
                        </select>
                    </div>
                    <div class="add-user-form-group">
                        <label>No Telepon</label>
                        <input type="text" name="no_telepon" required>
                    </div>
                    <div class="add-user-form-group">
                        <label>Posisi</label>
                        <select name="posisi" required>
                            <option value="">Pilih Posisi</option>
                            <option value="Staff">Staff</option>
                            <option value="Dokter">Dokter</option>
                        </select>
                    </div>
                    <div class="add-user-form-group">
                        <label>Username</label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="add-user-form-group">
                        <label>Password</label>
                        <input type="text" name="password" required>
                    </div>
                    <div class="add-user-footer">
                        <button type="submit" name="submit" class="add-user-save-btn">
                            <i class="fas fa-save"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
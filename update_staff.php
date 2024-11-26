<?php
include 'koneksi.php';

// Cek jika ada ID yang diteruskan dalam URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $data = mysqli_query($koneksi, "SELECT * FROM staff WHERE id_staff='$id'");
    $d = mysqli_fetch_array($data);
} else {
    // Jika tidak ada ID yang diteruskan, arahkan kembali ke halaman utama
    header('Location: staff.php');
    exit();
}

// Proses update ketika form disubmit
if (isset($_POST['submit'])) {
    // Ambil data dari form dan sanitasi
    $id = $_POST['id_staff'];  // Changed from 'staff' to 'id_staff'
    $name = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $gender = mysqli_real_escape_string($koneksi, $_POST['gender']);  // Changed from 'status_daftar'
    $no_telepon = mysqli_real_escape_string($koneksi, $_POST['no_telepon']);
    $posisi = mysqli_real_escape_string($koneksi, $_POST['posisi']);  // Changed from 'status_daftar'
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    // Update data ke dalam database
    $query = "UPDATE staff 
              SET nama='$name', gender='$gender', no_telepon='$no_telepon', posisi='$posisi', username='$username', password='$password'
              WHERE id_staff='$id'";  // Changed from 'staff' to 'id_staff'

    if (mysqli_query($koneksi, $query)) {
        // Redirect jika berhasil update
        header("Location: staff.php");
        exit();
    } else {
        echo "Terjadi kesalahan saat mengupdate data.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="tambah_user_style.css">
</head>
<body>
    <div class="add-user-container">
        <div class="add-user-card">
            <div class="add-user-header">
                <h4 class="add-user-title">Update Data Staff</h4>
                <button class="add-user-back-btn" onclick="window.location.href='staff.php'">Back</button>
            </div>
            <div class="add-user-body">
                <form method="post" action="update_staff.php?id=<?php echo $d['id_staff']; ?>">
                    <div class="add-user-form-group">
                        <label>Nama</label>
                        <input type="hidden" name="id_staff" value="<?php echo $d['id_staff']; ?>">
                        <input type="text" name="nama" value="<?php echo $d['nama']; ?>" required>
                    </div>
                    <div class="add-user-form-group">
                        <label>Gender</label>
                        <select name="gender" required>
                            <option value="Wanita" <?php echo ($d['gender'] == 'Wanita') ? 'selected' : ''; ?>>Wanita</option>
                            <option value="Pria" <?php echo ($d['gender'] == 'Pria') ? 'selected' : ''; ?>>Pria</option>
                        </select>
                    </div>
                    <div class="add-user-form-group">
                        <label>No Telepon</label>
                        <input type="text" name="no_telepon" value="<?php echo $d['no_telepon']; ?>" required>
                    </div>
                    <div class="add-user-form-group">
                        <label>Posisi</label>
                        <select name="posisi" required>
                            <option value="Staff" <?php echo ($d['posisi'] == 'Staff') ? 'selected' : ''; ?>>Staff</option>
                            <option value="Dokter" <?php echo ($d['posisi'] == 'Dokter') ? 'selected' : ''; ?>>Dokter</option>
                        </select>
                    </div>
                    <div class="add-user-form-group">
                        <label>Username</label>
                        <input type="text" name="username" value="<?php echo $d['username']; ?>" required>
                    </div>
                    <div class="add-user-form-group">
                        <label>Password</label>
                        <input type="text" name="password" value="<?php echo $d['password']; ?>" required>
                    </div>
                    <div class="add-user-footer">
                        <button type="submit" name="submit" class="add-user-save-btn">
                            <i class="fas fa-save"></i> Save Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
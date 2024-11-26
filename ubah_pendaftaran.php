<?php
include 'koneksi.php';

// Cek jika ada ID yang diteruskan dalam URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $data = mysqli_query($koneksi, "SELECT * FROM pendaftaran WHERE id_pendaftaran='$id'");
    $d = mysqli_fetch_array($data);
} else {
    // Jika tidak ada ID yang diteruskan, arahkan kembali ke halaman utama
    header('Location: pendaftaran.php');
    exit();
}

// Proses update ketika form disubmit
if (isset($_POST['submit'])) {
    // Ambil data dari form dan sanitasi
    $id = $_POST['id_pendaftaran'];
    $name = mysqli_real_escape_string($koneksi, $_POST['nama_pasien']);
    $no_bpjs = mysqli_real_escape_string($koneksi, $_POST['no_bpjs']);
    $waktu_daftar = mysqli_real_escape_string($koneksi, $_POST['waktu_daftar']);
    $status_daftar = mysqli_real_escape_string($koneksi, $_POST['status_daftar']);

    // Update data ke dalam database
    $query = "UPDATE pendaftaran 
              SET nama_pasien='$name', no_bpjs='$no_bpjs', waktu_daftar='$waktu_daftar', status_daftar='$status_daftar' 
              WHERE id_pendaftaran='$id'";

    if (mysqli_query($koneksi, $query)) {
        // Redirect jika berhasil update
        header("Location: pendaftaran.php");
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
                <h4 class="add-user-title">Update Data User</h4>
                <button class="add-user-back-btn" onclick="window.location.href='pendaftaran.php'">Back</button>
            </div>
            <div class="add-user-body">
                <form method="post" action="update_pendaftaran.php">
                    <div class="add-user-form-group">
                        <label>Nama Pasien</label>
                        <input type="hidden" name="id_pendaftaran" value="<?php echo $d['id_pendaftaran']; ?>">
                        <input type="text" name="nama_pasien" value="<?php echo $d['nama_pasien']; ?>" required>
                    </div>
                    <div class="add-user-form-group">
                        <label>No BPJS</label>
                        <input type="text" name="no_bpjs" value="<?php echo $d['no_bpjs']; ?>" required>
                    </div>
                    <div class="add-user-form-group">
                        <label>Waktu Daftar</label>
                        <input type="text" name="waktu_daftar" value="<?php echo $d['waktu_daftar']; ?>" required>
                    </div>
                    <div class="add-user-form-group">
                        <label>Status</label>
                        <select name="status_daftar" required>
                            <option value="Terdaftar" <?php echo ($d['status_daftar'] == 'Terdaftar') ? 'selected' : ''; ?>>Terdaftar</option>
                            <option value="Dalam Proses" <?php echo ($d['status_daftar'] == 'Dalam Proses') ? 'selected' : ''; ?>>Dalam Proses</option>
                            <option value="Selesai" <?php echo ($d['status_daftar'] == 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                        </select>
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

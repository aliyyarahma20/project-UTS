<?php
include 'koneksi.php';

// Set zona waktu sesuai dengan wilayah Anda
date_default_timezone_set('Asia/Jakarta');

// Get ID pendaftaran from URL
$id_pendaftaran = isset($_GET['id']) ? $_GET['id'] : null;

// Fetch patient data
$patient_data = null;
if ($id_pendaftaran) {
    $query = "SELECT * FROM pendaftaran WHERE id_pendaftaran = '$id_pendaftaran'";
    $result = mysqli_query($koneksi, $query);
    $patient_data = mysqli_fetch_assoc($result);
}

// Handle form submission
if (isset($_POST['submit'])) {
    $keluhan = mysqli_real_escape_string($koneksi, $_POST['keluhan']);
    $tindakan = mysqli_real_escape_string($koneksi, $_POST['tindakan']);
    $obat = mysqli_real_escape_string($koneksi, $_POST['obat']);
    $waktu_report = date('Y-m-d H:i:s');

    // Insert into report table
    $insert_query = "INSERT INTO report (id_pendaftaran, keluhan, tindakan, obat, waktu_report) 
                    VALUES ('$id_pendaftaran', '$keluhan', '$tindakan', '$obat', '$waktu_report')";
    
    // Update pendaftaran status
    $update_query = "UPDATE pendaftaran 
                    SET status_daftar = 'Selesai' 
                    WHERE id_pendaftaran = '$id_pendaftaran'";

    if (mysqli_query($koneksi, $insert_query) && mysqli_query($koneksi, $update_query)) {
        header("Location: pemeriksaan_menu.php");
        exit();
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Pemeriksaan Pasien</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="pendaftaran.css">
    <style>
        .examination-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            max-width: 800px;
        }
        .patient-info {
            background-color: #f0f7ff;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .form-label {
            font-weight: 500;
            color: #001f3f;
        }
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        /* Tambahan style untuk buttons */
        .btn-primary {
            background-color: #ffffff;
            color: #001F3F
        }
        .btn-primary:hover {
            background-color: #001F3F;
            border-color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="table-container">
        <div class="card examination-card">
            <div class="card-header">
                <h4 class="card-title">Pemeriksaan Pasien</h4>
                <button id="navbarToggle" class="navbar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <!-- Side Navbar -->
            <div id="sideNavbar" class="side-navbar">
                <div class="navbar-content">
                    <a href="dashboard_dokter.php" class="nav-item"><i class="fas fa-home"></i> Home</a>
                    <a href="pemeriksaan_menu.php" class="nav-item active"><i class="fas fa-stethoscope"></i>Pemeriksaan</a>
                    <a href="riwayat.php" class="nav-item"><i class="fas fa-history"></i>Riwayat</a>
                    <a href="logout.php" class="nav-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>

            <div class="card-body">
                <?php if ($patient_data): ?>
                    <!-- Patient Information -->
                    <div class="patient-info">
                        <h5 class="mb-3">Informasi Pasien</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>ID Pendaftaran:</strong> <?php echo $patient_data['id_pendaftaran']; ?></p>
                                <p><strong>Nama Pasien:</strong> <?php echo $patient_data['nama_pasien']; ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>No BPJS:</strong> <?php echo $patient_data['no_bpjs'] ?: 'Tidak ada'; ?></p>
                                <p><strong>Waktu Daftar:</strong> <?php echo date('d/m/Y H:i', strtotime($patient_data['waktu_daftar'])); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Examination Form -->
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="keluhan" class="form-label">Keluhan Pasien</label>
                            <textarea class="form-control" id="keluhan" name="keluhan" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="tindakan" class="form-label">Tindakan</label>
                            <select class="form-control" id="tindakan" name="tindakan" required>
                                <option value="">Pilih Tindakan</option>
                                <option value="pemberian obat">Pemberian Obat</option>
                                <option value="rujukan">Rujukan</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="obat" class="form-label">Obat/Catatan</label>
                            <textarea class="form-control" id="obat" name="obat" required></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-center">
                            <a href="pemeriksaan_menu.php" class="btn btn-primary me-md-2">Kembali</a>
                            <button type="submit" name="submit" class="btn btn-primary">Simpan Pemeriksaan</button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-warning">
                        Data pasien tidak ditemukan. Silakan kembali ke <a href="dashboard_dokter.php">halaman utama</a>.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navbar Toggle functionality
        const navbarToggle = document.getElementById('navbarToggle');
        const sideNavbar = document.getElementById('sideNavbar');
        
        const overlay = document.createElement('div');
        overlay.className = 'navbar-overlay';
        document.body.appendChild(overlay);
        
        navbarToggle.addEventListener('click', () => {
            sideNavbar.classList.toggle('active');
            overlay.classList.toggle('active');
        });
        
        overlay.addEventListener('click', () => {
            sideNavbar.classList.remove('active');
            overlay.classList.remove('active');
        });
    </script>
</body>
</html>
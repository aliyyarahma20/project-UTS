<?php
include 'koneksi.php';

// Set zona waktu sesuai dengan wilayah Anda
date_default_timezone_set('Asia/Jakarta');

// Handle delete action
if (isset($_POST['delete'])) {
    $id_report = mysqli_real_escape_string($koneksi, $_POST['id_report']);
    
    // Get id_report before deleting the report
    $query_get_pendaftaran = "SELECT id_pendaftaran FROM report WHERE id_report = '$id_report'";
    $result_pendaftaran = mysqli_query($koneksi, $query_get_pendaftaran);
    $row_pendaftaran = mysqli_fetch_assoc($result_pendaftaran);
    
    if ($row_pendaftaran) {
        $id_pendaftaran = $row_pendaftaran['id_pendaftaran'];
        
        // Start transaction
        mysqli_begin_transaction($koneksi);
        
        try {
            // Delete the report
            $delete_query = "DELETE FROM report WHERE id_report = '$id_report'";
            mysqli_query($koneksi, $delete_query);
            
            // Update pendaftaran status back to 'Menunggu'
            $update_query = "UPDATE pendaftaran SET status_daftar = 'Menunggu' WHERE id_pendaftaran = '$id_pendaftaran'";
            mysqli_query($koneksi, $update_query);
            
            // Commit transaction
            mysqli_commit($koneksi);
            
            // Redirect with success message
            header("Location: riwayat.php?status=success&message=Data berhasil dihapus");
            exit();
        } catch (Exception $e) {
            // Rollback transaction on error
            mysqli_rollback($koneksi);
            header("Location: riwayat.php?status=error&message=Gagal menghapus data");
            exit();
        }
    }
}

// Query to fetch all reports with patient information
$query = "SELECT r.*, p.nama_pasien, p.no_bpjs 
          FROM report r 
          JOIN pendaftaran p ON r.id_pendaftaran = p.id_pendaftaran 
          ORDER BY r.waktu_report DESC";
$result = mysqli_query($koneksi, $query);

// Display status messages if any
if (isset($_GET['status']) && isset($_GET['message'])) {
    $status = $_GET['status'];
    $message = $_GET['message'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Pemeriksaan Pasien</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="pendaftaran.css">
    <style>
        .history-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            max-width: 1200px;
        }
        .table th {
            background-color: #f0f7ff;
            color: #001f3f;
        }
        .action-button {
            padding: 5px 10px;
            font-size: 0.9rem;
            margin: 0 2px;
        }
        .search-box {
            max-width: 300px;
            margin-bottom: 20px;
        }
        .table-responsive {
            padding: 15px;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
        }
        .alert {
            margin-bottom: 20px;
        }
        .actions-cell {
            white-space: nowrap;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
        }
    </style>
</head>
<body>
    <div class="table-container">
        <div class="card">
            <!-- Display status messages -->
            <?php if (isset($status) && isset($message)): ?>
                <div class="alert alert-<?php echo $status == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show m-3">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card-header">
                <h4 class="card-title">Riwayat Pemeriksaan Pasien</h4>
                <div class="search-box">
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari pasien...">
                </div>
                <button id="navbarToggle" class="navbar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <!-- Side Navbar -->
            <div id="sideNavbar" class="side-navbar">
                <div class="navbar-content">
                    <a href="dashboard_dokter.php" class="nav-item"><i class="fas fa-home"></i> Home</a>
                    <a href="pemeriksaan_menu.php" class="nav-item"><i class="fas fa-stethoscope"></i>Pemeriksaan</a>
                    <a href="riwayat.php" class="nav-item active"><i class="fas fa-history"></i>Riwayat</a>
                    <a href="logout.php" class="nav-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>

            <!-- Patients Table -->
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Pasien</th>
                            <th>Nama Pasien</th>
                            <th>No. BPJS</th>
                            <th>Waktu Pemeriksaan</th>
                            <th>Keluhan</th>
                            <th>Tindakan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php 
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result)): 
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row['id_pendaftaran']; ?></td>
                            <td><?php echo $row['nama_pasien']; ?></td>
                            <td><?php echo $row['no_bpjs'] ?: '-'; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($row['waktu_report'])); ?></td>
                            <td><?php echo substr($row['keluhan'], 0, 50) . (strlen($row['keluhan']) > 50 ? '...' : ''); ?></td>
                            <td><?php echo $row['tindakan']; ?></td>
                            <td class="actions-cell">
                                <div class="action-buttons">
                                    <a href="print_report.php?id_report=<?php echo $row['id_report']; ?>" 
                                    class="btn-action btn-edit" 
                                    title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form method="POST" style="display: inline;">
                                    <input type="hidden" name="id_report" value="<?php echo $row['id_report']; ?>">
                                    <input type="hidden" name="delete" value="true">
                                    <button type="submit" 
                                        style="
                                            width: 32px; 
                                            height: 32px;
                                            border: none;
                                            background-color: #E8ECF1;
                                            color: #051B2C;
                                            border-radius: 6px;
                                            display: inline-flex;
                                            align-items: center;
                                            justify-content: center;
                                            cursor: pointer;
                                            transition: all 0.2s ease;
                                        "
                                        onmouseover="this.style.backgroundColor='#051b2c'; this.style.color='white';"
                                        onmouseout="this.style.backgroundColor='#E8ECF1'; this.style.color='#051B2C';"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"
                                        title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let searchValue = this.value.toLowerCase();
            let tableRows = document.querySelectorAll('tbody tr');
            
            tableRows.forEach(row => {
                let text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchValue) ? '' : 'none';
            });
        });

        // Delete confirmation
        function confirmDelete(form) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        form.delete.value = 'true';
        form.submit();
        }
    }

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
<?php
// index.php untuk dashboard dokter
error_reporting(0);
include 'koneksi.php';

// Get current date
$today = date('Y-m-d');

// Get total patients for today
$patient_query = mysqli_query($koneksi, "SELECT COUNT(*) as total_patients 
    FROM pendaftaran 
    WHERE DATE(waktu_daftar) = '$today'");
$patient_data = mysqli_fetch_assoc($patient_query);
$total_patients = $patient_data['total_patients'];

// Get waiting patients (status = 'Terdaftar')
$waiting_query = mysqli_query($koneksi, "SELECT COUNT(*) as waiting_patients 
    FROM pendaftaran 
    WHERE DATE(waktu_daftar) = '$today' 
    AND status_daftar = 'Terdaftar'");
$waiting_data = mysqli_fetch_assoc($waiting_query);
$waiting_patients = $waiting_data['waiting_patients'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Dokter</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="pendaftaran.css">
    <style>
        .summary-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .summary-card:hover {
            transform: translateY(-5px);
        }
        .bg-soft-blue {
            background-color: #f0f7ff;
            border: 1px solid #cce5ff;
        }
        .text-soft-blue {
            color: #001f3f;
        }
        .card-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.875rem;
            color: #001f3f;
        }
    </style>
</head>
<body>
    <div class="table-container">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Daftar Pasien</h4>
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
                    <a href="dashboard_dokter.php" class="nav-item active"><i class="fas fa-home"></i> Home</a>
                    <a href="pemeriksaan_menu.php" class="nav-item"><i class="fas fa-stethoscope"></i>Pemeriksaan</a>
                    <a href="riwayat.php" class="nav-item"><i class="fas fa-history"></i>Riwayat</a>
                    <a href="logout.php" class="nav-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>

                <!-- Patients Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ID Pendaftaran</th>
                                <th>Nama Pasien</th>
                                <th>No BPJS</th>
                                <th>Waktu Daftar</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                $query = "SELECT id_pendaftaran, nama_pasien, no_bpjs, waktu_daftar, status_daftar 
                                        FROM pendaftaran 
                                        WHERE DATE(waktu_daftar) = '$today'
                                        ORDER BY waktu_daftar ASC";
                                        
                                $data = mysqli_query($koneksi, $query);
                                
                                while ($d = mysqli_fetch_array($data)) {
                            ?>
                            <tr>
                                <td><?php echo $d['id_pendaftaran']; ?></td>
                                <td><?php echo $d['nama_pasien']; ?></td>
                                <td><?php echo $d['no_bpjs']; ?></td>
                                <td><?php echo date('H:i', strtotime($d['waktu_daftar'])); ?></td>
                                <td>
                                    <span class="status-badge">
                                        <?php echo $d['status_daftar']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if($d['status_daftar'] == 'Terdaftar'): ?>
                                    <a href="pemeriksaan.php?id=<?php echo $d['id_pendaftaran']; ?>" 
                                       class="btn btn-primary btn-sm">
                                        Mulai Pemeriksaan
                                    </a>
                                    <?php else: ?>
                                    <button class="btn btn-secondary btn-sm" disabled>
                                        Mulai Pemeriksaan
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php 
                                }
                            } catch (Exception $e) {
                                echo '<tr><td colspan="6" class="text-center">Error loading data</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
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
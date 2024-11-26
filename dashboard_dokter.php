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

// Get medical actions distribution
$actions_query = mysqli_query($koneksi, "SELECT 
    SUM(CASE WHEN tindakan = 'Pemberian Obat' THEN 1 ELSE 0 END) as pemberian_obat,
    SUM(CASE WHEN tindakan = 'Rujukan' THEN 1 ELSE 0 END) as rujukan
    FROM report");
$actions_data = mysqli_fetch_assoc($actions_query);
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .chart-container {
            position: relative;
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            height: 300px;
        }
    </style>
</head>
<body>
    <div class="table-container">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Dashboard Dokter</h4>
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

            <div class="card-body">
                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <div class="card summary-card bg-soft-blue">
                            <div class="card-body">
                                <i class="fas fa-users card-icon text-soft-blue"></i>
                                <h5 class="card-title text-soft-blue">Total Pasien Hari Ini</h5>
                                <h2 class="display-4 text-soft-blue"><?php echo $total_patients; ?></h2>
                                <p class="card-text text-muted"><?php echo date('d F Y'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card summary-card bg-soft-blue">
                            <div class="card-body">
                                <i class="fas fa-user-clock card-icon text-soft-blue"></i>
                                <h5 class="card-title text-soft-blue">Pasien Menunggu</h5>
                                <h2 class="display-4 text-soft-blue"><?php echo $waiting_patients; ?></h2>
                                <p class="card-text text-muted">Belum Diperiksa</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card summary-card">
                            <div class="card-body">
                                <h5 class="card-title text-center mb-4">Distribusi Tindakan Medis</h5>
                                <div class="chart-container">
                                    <canvas id="medicalActionsChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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

        // Medical Actions Chart
        const ctx = document.getElementById('medicalActionsChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Pemberian Obat', 'Rujukan'],
                datasets: [{
                    data: [
                        <?php echo $actions_data['pemberian_obat']; ?>,
                        <?php echo $actions_data['rujukan']; ?>
                    ],
                    backgroundColor: [
                        '#001f3f',  // for Pemberian Obat
                        '#cce5ff'   // for Rujukan
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((acc, curr) => acc + curr, 0);
                                const percentage = ((value / total) * 100).toFixed(0);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    </script>
</body>
</html>
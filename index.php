<?php
// index.php
error_reporting(0);
include 'koneksi.php';

// Existing queries remain the same
$today = date('Y-m-d');

$visit_query = mysqli_query($koneksi, "SELECT COUNT(*) as total_visits 
    FROM pendaftaran 
    WHERE DATE(waktu_daftar) = '$today'");
$visit_data = mysqli_fetch_assoc($visit_query);
$total_visits = $visit_data['total_visits'];

$patient_query = mysqli_query($koneksi, "SELECT COUNT(*) as total_patients 
    FROM pendaftaran");
$patient_data = mysqli_fetch_assoc($patient_query);
$total_patients = $patient_data['total_patients'];

$weekly_query = mysqli_query($koneksi, "
    SELECT 
        DATE(waktu_daftar) as tanggal,
        COUNT(*) as jumlah_kunjungan,
        DAYNAME(waktu_daftar) as hari
    FROM pendaftaran 
    WHERE waktu_daftar >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)
    GROUP BY DATE(waktu_daftar)
    ORDER BY tanggal DESC 
    LIMIT 7
");

$dates = [];
$visitors = [];

while ($row = mysqli_fetch_assoc($weekly_query)) {
    $formatted_date = date('d/m', strtotime($row['tanggal']));
    $dates[] = $formatted_date;
    $visitors[] = (int)$row['jumlah_kunjungan'];
}

// New query for patient distribution by BPJS numbers
$bpjs_query = mysqli_query($koneksi, "
    SELECT 
        CASE 
            WHEN LENGTH(no_bpjs) > 2 THEN 'BPJS'
            ELSE 'Non-BPJS'
        END as kategori,
        COUNT(*) as jumlah
    FROM pendaftaran 
    GROUP BY 
        CASE 
            WHEN LENGTH(no_bpjs) > 2 THEN 'BPJS'
            ELSE 'Non-BPJS'
        END
");

$bpjs_data = [];
while ($row = mysqli_fetch_assoc($bpjs_query)) {
    $bpjs_data[] = $row;
}

// Reverse arrays for chronological order
$dates = array_reverse($dates);
$visitors = array_reverse($visitors);
?>

<!DOCTYPE html>
<html>
<head>
    <!-- Previous head content remains the same -->
    <title>Dashboard Poliklinik</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="pendaftaran.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Previous styles remain the same */
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
        .chart-container {
            position: relative;
            margin: 20px 0;
            height: 300px;
        }
        .charts-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .chart-card {
            flex: 1;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
    </style>
</head>
<body>
    <!-- Previous navbar and header structure remains the same -->
    <div class="table-container">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Dashboard Poliklinik</h4>
                <div class="search-box">
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari dokter...">
                </div>
                <button id="navbarToggle" class="navbar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <div id="sideNavbar" class="side-navbar">
                <div class="navbar-content">
                    <a href="index.php" class="nav-item active"><i class="fas fa-home"></i> Home</a>
                    <a href="pendaftaran.php" class="nav-item"><i class="fas fa-clipboard-list"></i>Data Pendaftaran</a>
                    <a href="staff.php" class="nav-item"><i class="fas fa-user"></i>Data Staff</a>
                    <a href="logout.php" class="nav-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>

            <div class="card-body">
                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <div class="card summary-card bg-soft-blue">
                            <div class="card-body">
                                <i class="fas fa-calendar-check card-icon text-soft-blue"></i>
                                <h5 class="card-title text-soft-blue">Total Kunjungan Hari Ini</h5>
                                <h2 class="display-4 text-soft-blue"><?php echo $total_visits; ?></h2>
                                <p class="card-text text-muted"><?php echo date('d F Y'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card summary-card bg-soft-blue">
                            <div class="card-body">
                                <i class="fas fa-users card-icon text-soft-blue"></i>
                                <h5 class="card-title text-soft-blue">Total Pasien</h5>
                                <h2 class="display-4 text-soft-blue"><?php echo $total_patients; ?></h2>
                                <p class="card-text text-muted">Keseluruhan Pasien Terdaftar</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="charts-row">
                    <!-- Weekly Visitors Chart -->
                    <div class="chart-card">
                        <h5 class="card-title mb-3">Kunjungan Pasien per Hari</h5>
                        <p class="text-muted">Data kunjungan 7 hari terakhir</p>
                        <div class="chart-container">
                            <canvas id="visitorsChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- BPJS Distribution Chart -->
                    <div class="chart-card">
                        <h5 class="card-title mb-3">Distribusi Pasien BPJS</h5>
                        <p class="text-muted">Total Pasien berdasarkan kepemilikan BPJS</p>
                        <div class="chart-container">
                            <canvas id="bpjsChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Previous table structure remains the same -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Dokter</th>
                                <th>No Telepon</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            try {
                                $query = "SELECT nama, no_telepon 
                                        FROM staff 
                                        WHERE posisi = 'Dokter' 
                                        ORDER BY nama ASC";
                                        
                                $data = mysqli_query($koneksi, $query);
                                
                                while ($d = mysqli_fetch_array($data)) {
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $d['nama']; ?></td>
                                <td><?php echo $d['no_telepon']; ?></td>
                            </tr>
                            <?php 
                                }
                            } catch (Exception $e) {
                                echo '<tr><td colspan="3" class="text-center">Error loading data</td></tr>';
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
        // Visitors Chart
        const ctx = document.getElementById('visitorsChart').getContext('2d');

        function getIndonesianDay(dateStr) {
            const days = ['Ming', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            const date = new Date(dateStr.split('/').reverse().join('-'));
            return days[date.getDay()];
        }

        const dayLabels = <?php echo json_encode($dates); ?>.map(date => {
            const [day, month] = date.split('/');
            return getIndonesianDay(`${day}/${month}/2024`);
        });

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: dayLabels,
                datasets: [{
                    label: 'Jumlah Kunjungan Pasien',
                    data: <?php echo json_encode($visitors); ?>,
                    backgroundColor: '#001f3f',
                    borderColor: 'rgba(59, 130, 246, 0)',
                    borderWidth: 0,
                    borderRadius: 8,
                    maxBarThickness: 35,
                    categoryPercentage: 0.5,
                    barPercentage: 0.8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: '#334155',
                        bodyColor: '#334155',
                        borderColor: '#e2e8f0',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            title: function(context) {
                                const originalDate = <?php echo json_encode($dates); ?>[context[0].dataIndex];
                                return `Tanggal: ${originalDate}`;
                            },
                            label: function(context) {
                                return `Kunjungan: ${context.raw}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0
                        }
                    }
                }
            }
        });

        // BPJS Distribution Chart
        const bpjsCtx = document.getElementById('bpjsChart').getContext('2d');
        new Chart(bpjsCtx, {
            type: 'doughnut',
            data: {
                labels: ['BPJS', 'Non-BPJS'],
                datasets: [{
                    data: [
                        <?php 
                        foreach ($bpjs_data as $data) {
                            echo $data['jumlah'] . ',';
                        }
                        ?>
                    ],
                    backgroundColor: [
                        '#001f3f',
                        '#cce5ff'
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
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: '#334155',
                        bodyColor: '#334155',
                        borderColor: '#e2e8f0',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: false
                    }
                }
            }
        });

        // Previous search and navbar functionality remains the same
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let searchValue = this.value.toLowerCase();
            let tableRows = document.querySelectorAll('tbody tr');
            
            tableRows.forEach(row => {
                let text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchValue) ? '' : 'none';
            });
        });

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
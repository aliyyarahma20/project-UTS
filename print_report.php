<?php
session_start(); // Tambahkan ini di paling atas
include 'koneksi.php';

// Tambahkan pengecekan session
if (!isset($_SESSION['nama'])) {
}

// Initialize $report_data as null
$report_data = null;
$error_message = null;

// Validate and sanitize id_report
$id_report = isset($_GET['id_report']) ? filter_var($_GET['id_report'], FILTER_SANITIZE_NUMBER_INT) : null;

if ($id_report) {
    try {
        // Prepare the query using prepared statements to prevent SQL injection
        $query = "SELECT r.*, p.nama_pasien, p.no_bpjs, p.waktu_daftar 
                  FROM report r 
                  JOIN pendaftaran p ON r.id_pendaftaran = p.id_pendaftaran 
                  WHERE r.id_report = ?";
        
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $id_report);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $report_data = mysqli_fetch_assoc($result);
        } else {
            $error_message = "Laporan tidak ditemukan.";
        }
    } catch (Exception $e) {
        $error_message = "Terjadi kesalahan saat mengambil data.";
    }
} else {
    $error_message = "ID Report tidak valid.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title class="no-print">Laporan Pemeriksaan Medis</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            .print-container {
                padding: 20px;
            }
            .report-header {
                border-bottom: 2px solid #000;
                margin-bottom: 20px;
                padding-bottom: 20px;
            }
            title {
                display: none;
            }
            @page {
                margin-top: 0;
                margin-bottom: 0;
            }
            body {
                padding-top: 0;
            }
        }
        
        .print-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
        }
        .clinic-info {
            text-align: center;
            margin-bottom: 30px;
        }
        .report-header {
            margin-bottom: 30px;
        }
        .report-section {
            margin-bottom: 20px;
        }
        .signature-section {
            margin-top: 50px;
        }
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 50px;
            margin-bottom: 30px;
            padding: 10px;
        }
        .btn-print, .btn-back {
            padding: 8px 25px;
            border-radius: 5px;
            font-weight: 500;
            min-width: 140px;
        }
        .btn-print {
            background-color: #ffffff;
            color: #001F3F
            border: none;
            transition: background-color 0.3s;
        }
        .btn-print:hover {
            background-color: #001F3F;
            color: white;
        }
        .btn-back {
            background-color: #ffffff;
            color: #001F3F
            border: none;
            transition: background-color 0.3s;
        }
        .btn-back:hover {
            background-color: #001F3F;
            color: white;
        }
    </style>
</head>
<body>
    <?php if ($error_message): ?>
        <div class="alert alert-warning m-4">
            <?php echo htmlspecialchars($error_message); ?> 
            <a href="riwayat.php">Kembali ke Dashboard</a>
        </div>
    <?php endif; ?>

    <?php if ($report_data): ?>
        <div class="print-container">
            <!-- Clinic Information -->
            <div class="clinic-info">
                <h2>POLIKLINIK </h2>
                <p>Jl. Sehat Selalu No. 123, Kota Kesehatan</p>
                <p>Telp: (021) 123-4567</p>
            </div>

            <!-- Report Header -->
            <div class="report-header">
                <h3 class="text-center mb-4">LAPORAN PEMERIKSAAN PASIEN</h3>
                <div class="row">
                    <div class="col-6">
                        <p><strong>Nama Pasien:</strong> <?php echo htmlspecialchars($report_data['nama_pasien']); ?></p>
                        <p><strong>No. BPJS:</strong> <?php echo htmlspecialchars($report_data['no_bpjs'] ?: '-'); ?></p>
                    </div>
                    <div class="col-6 text-end">
                        <p><strong>Tanggal Pemeriksaan:</strong> <?php echo date('d/m/Y', strtotime($report_data['waktu_report'])); ?></p>
                        <p><strong>Waktu:</strong> <?php echo date('H:i', strtotime($report_data['waktu_report'])); ?></p>
                    </div>
                </div>
            </div>

            <!-- Report Content -->
            <div class="report-content">
                <div class="report-section">
                    <h5>Keluhan Pasien:</h5>
                    <p><?php echo nl2br(htmlspecialchars($report_data['keluhan'])); ?></p>
                </div>

                <div class="report-section">
                    <h5>Tindakan:</h5>
                    <p><?php echo htmlspecialchars($report_data['tindakan']); ?></p>
                </div>

                <div class="report-section">
                    <h5>Obat/Catatan:</h5>
                    <p><?php echo nl2br(htmlspecialchars($report_data['obat'])); ?></p>
                </div>
            </div>

            <!-- Signature Section -->
            <div class="signature-section">
                <div class="row">
                    <div class="col-6">
                        <p>Pasien,</p>
                        <br><br><br>
                        <p>( <?php echo htmlspecialchars($report_data['nama_pasien']); ?> )</p>
                    </div>
                    <div class="col-6 text-end">
                        <p>Dokter Pemeriksa,</p>
                        <br><br><br>
                        <p>( dr. <?php echo isset($_SESSION['nama']) ? htmlspecialchars($_SESSION['nama']) : 'Tidak tersedia'; ?> )</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons no-print">
                <?php if ($report_data): ?>
                    <button onclick="window.print()" class="btn btn-print no-print">
                        <i class="fas fa-print"></i> Print Report
                    </button>
                <?php endif; ?>
                <a href="riwayat.php" class="btn btn-back no-print">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    <?php endif; ?>

    <script>
        // Add a print shortcut
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    </script>
    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">   
</body>
</html>
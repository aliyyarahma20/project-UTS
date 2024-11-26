<?php
include 'koneksi.php'; // Pastikan koneksi.php sudah ada dan terhubung dengan benar

// Set zona waktu sesuai dengan wilayah Anda
date_default_timezone_set('Asia/Jakarta');

$successMessage = '';

if (isset($_POST['submit'])) {
    // Ambil data dari form dan sanitasi untuk keamanan
    $nama_pasien = mysqli_real_escape_string($koneksi, $_POST['nama_pasien']);
    $no_bpjs = mysqli_real_escape_string($koneksi, $_POST['no_bpjs']);
    $waktu_daftar = date('Y-m-d H:i:s'); // Waktu otomatis
    $status_daftar = 'Terdaftar'; // Status default

    // Query untuk memasukkan data ke tabel pendaftaran
    $query = "REPLACE INTO pendaftaran (nama_pasien, no_bpjs, waktu_daftar, status_daftar) 
              VALUES ('$nama_pasien', '$no_bpjs', '$waktu_daftar', '$status_daftar')";

    // Jalankan query dan cek apakah berhasil
    if (mysqli_query($koneksi, $query)) {
        // Ambil ID pendaftaran yang baru dimasukkan
        $id_pendaftaran = mysqli_insert_id($koneksi);
        $successMessage = "Data berhasil ditambahkan.<br>ID Pendaftaran Anda: $id_pendaftaran.<br>Atas nama: $nama_pasien";
        echo "<script>var shouldRedirect = true;</script>";
    } else {
        $successMessage = "Terjadi kesalahan: " . mysqli_error($koneksi);
        echo "<script>var shouldRedirect = false;</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penambahan Pendaftaran Pasien</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="form_pendaftaran.css">
    <style>
        .redirect-message {
            margin-top: 10px;
            font-style: italic;
            color: #666;
        }
        
        /* Tambahan style untuk animasi loading */
        .loading-dots {
            display: inline-block;
        }
        
        .loading-dots::after {
            content: '...';
            animation: dots 1.5s steps(4, end) infinite;
        }
        
        @keyframes dots {
            0%, 20% { content: ''; }
            40% { content: '.'; }
            60% { content: '..'; }
            80%, 100% { content: '...'; }
        }
    </style>
</head>
<body>
    <!-- Dekorasi Background -->
    <div class="decoration">
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
        <div class="wave"></div>
        <div class="dots"></div>
    </div>

    <div class="wrapper">
        <div class="logo-text">Welcome!</div>
        <div class="subtitle">Silakan masukan data anda</div>
        <div class="container">
            <!-- Form untuk pendaftaran pasien -->
            <form method="POST" action="" id="formPendaftaran">
                <div class="form-group">
                    <label for="nama_pasien">Nama Pasien:</label>
                    <input type="text" id="nama_pasien" name="nama_pasien" required>
                </div>

                <div class="form-group">
                    <label for="no_bpjs">Nomor BPJS:</label>
                    <input type="text" id="no_bpjs" name="no_bpjs" required>
                </div>

                <input type="submit" name="submit" value="Daftar">
            </form>
        </div>

        <!-- Modal untuk menampilkan ID Pendaftaran -->
        <div id="myModal" class="modal" style="display: <?= ($successMessage) ? 'block' : 'none'; ?>">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h3>Pendaftaran Berhasil!</h3>
                <p class="success-message"><?= $successMessage ?></p>
                <p class="redirect-message">
                    Data diterima, kembali ke data pendaftaran
                    <span class="loading-dots"></span>
                </p>
            </div>
        </div>

        <script>
            let redirectTimer;

            function redirectToPendaftaran() {
                window.location.href = 'pendaftaran.php';
            }

            // Fungsi untuk memulai timer redirect
            function startRedirectTimer() {
                if (typeof shouldRedirect !== 'undefined' && shouldRedirect) {
                    redirectTimer = setTimeout(redirectToPendaftaran, 5000); // Redirect setelah 3 detik
                }
            }

            // Fungsi untuk membersihkan timer
            function clearRedirectTimer() {
                if (redirectTimer) {
                    clearTimeout(redirectTimer);
                }
            }

            // Tutup modal saat tombol "close" diklik
            document.querySelector('.close').addEventListener('click', function() {
                document.getElementById('myModal').style.display = "none";
                clearRedirectTimer();
                if (typeof shouldRedirect !== 'undefined' && shouldRedirect) {
                    redirectToPendaftaran();
                }
            });

            // Tutup modal jika pengguna mengklik di luar modal
            window.addEventListener('click', function(event) {
                let modal = document.getElementById('myModal');
                if (event.target == modal) {
                    modal.style.display = "none";
                    clearRedirectTimer();
                    if (typeof shouldRedirect !== 'undefined' && shouldRedirect) {
                        redirectToPendaftaran();
                    }
                }
            });

            // Mulai timer redirect saat modal ditampilkan
            if (document.getElementById('myModal').style.display === 'block') {
                startRedirectTimer();
            }
        </script>
    </div>
</body>
</html>
<?php
// index.php
error_reporting(0);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Data Pendaftaran Poliklinik</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="pendaftaran.css">
</head>
<body>
    <div class="table-container">
        <div class="card">
            <div class="card-header">
                <button id="navbarToggle" class="navbar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h4 class="card-title">Data Pendaftaran Poliklinik</h4>
                <div class="search-box">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search...">
                </div>
            </div>

            <!-- Side Navbar -->
            <div id="sideNavbar" class="side-navbar">
                <div class="navbar-content">
                    <a href="index.php" class="nav-item"><i class="fas fa-home"></i> Home</a>
                    <a href="pendaftaran.php" class="nav-item"><i class="fas fa-user"></i> Data Pendaftaran</a>
                    <a href="staff.php" class="nav-item"><i class="fas fa-cog"></i> Data Staff</a>
                    <a href="logout.php" class="nav-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Pendaftaran</th>
                                <th>Nama</th>
                                <th>No BPJS</th> 
                                <th>Waktu Daftar</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include 'koneksi.php';
                            $no = 1;
                            try {
                                $data = mysqli_query($koneksi, "SELECT * FROM pendaftaran");
                                while ($d = mysqli_fetch_array($data)) {
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($d['id_pendaftaran']); ?></td>
                                <td><?php echo htmlspecialchars($d['nama_pasien']); ?></td>
                                <td><?php echo htmlspecialchars($d['no_bpjs']); ?></td>
                                <td><?php echo htmlspecialchars($d['waktu_daftar']); ?></td>
                                <td><?php echo htmlspecialchars($d['status_daftar']); ?></td>
                                <td class="actions-cell">
                                    <div class="action-buttons">
                                        <a href="ubah_pendaftaran.php?id=<?php echo $d['id_pendaftaran']; ?>" 
                                           class="btn-action btn-edit" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="javascript:void(0)" 
                                           onclick="confirmDelete(<?php echo $d['id_pendaftaran']; ?>)"
                                           class="btn-action btn-delete" 
                                           title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php 
                                }
                            } catch (Exception $e) {
                                echo '<tr><td colspan="8" class="text-center">Error loading data</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div style="display: flex; justify-content: center;" class="card-footer text-end">
                <button class="btn btn-primary" onclick="window.location.href='tambah_pendaftaran.php'">
                    <i class="fas fa-plus"></i> Tambah Pasien
                </button>
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
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this user?')) {
                window.location.href = 'hapus_pendaftaran.php?id=' + id;
            }
        }

        // Navbar functionality
        const navbarToggle = document.getElementById('navbarToggle');
        const sideNavbar = document.getElementById('sideNavbar');
        
        // Create overlay element
        const overlay = document.createElement('div');
        overlay.className = 'navbar-overlay';
        document.body.appendChild(overlay);

        function toggleBodyScroll(disable) {
            document.body.style.overflow = disable ? 'hidden' : '';
        }
        
        navbarToggle.addEventListener('click', () => {
            const isActive = sideNavbar.classList.toggle('active');
            overlay.classList.toggle('active');
            toggleBodyScroll(isActive);
        });
        
        overlay.addEventListener('click', () => {
            sideNavbar.classList.remove('active');
            overlay.classList.remove('active');
            toggleBodyScroll(false);
        });
    </script>
</body>
</html>
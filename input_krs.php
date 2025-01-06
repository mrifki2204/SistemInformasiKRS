<?php
require_once 'koneksi.php';

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    die("ID mahasiswa tidak valid.");
}

$queryMahasiswa = "SELECT * FROM inputmhs WHERE id = $id";
$resultMahasiswa = mysqli_query($conn, $queryMahasiswa);

if (!$resultMahasiswa) {
    die("Gagal mengambil data mahasiswa: " . mysqli_error($conn));
}

$mahasiswa = mysqli_fetch_assoc($resultMahasiswa);

if (!$mahasiswa) {
    die("Data mahasiswa tidak ditemukan.");
}

$queryTakenCourses = "SELECT * FROM jwl_mhs WHERE mhs_id = $id";
$resultTakenCourses = mysqli_query($conn, $queryTakenCourses);

$takenCourses = [];
while ($row = mysqli_fetch_assoc($resultTakenCourses)) {
    $takenCourses[] = $row['matakuliah'];
}

$queryMatakuliah = !empty($takenCourses)
    ? "SELECT * FROM jwl_matakuliah WHERE matakuliah NOT IN ('" . implode("','", $takenCourses) . "')"
    : "SELECT * FROM jwl_matakuliah";

$resultMatakuliah = mysqli_query($conn, $queryMatakuliah);

if (!$resultMatakuliah) {
    die("Gagal mengambil data mata kuliah: " . mysqli_error($conn));
}

$queryTotalSKS = "SELECT SUM(sks) AS total_sks FROM jwl_mhs WHERE mhs_id = $id";
$resultTotalSKS = mysqli_query($conn, $queryTotalSKS);

$totalSKS = mysqli_fetch_assoc($resultTotalSKS)['total_sks'] ?? 0;
$maxSKS = $mahasiswa['ipk'] < 3 ? 20 : 24;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input KRS - Sistem Informasi KRS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #34495e;
            --accent: #3498db;
            --success: #2ecc71;
            --warning: #f1c40f;
            --danger: #e74c3c;
        }

        body {
            background-color: #ecf0f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar-custom {
            background: linear-gradient(120deg, var(--primary), var(--secondary));
            padding: 1rem 0;
        }

        .student-info-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-left: 5px solid var(--accent);
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border-left: 5px solid var(--accent);
        }

        .stat-card.sks-card {
            border-left-color: var(--success);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .krs-form {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .data-table {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .table thead th {
            background-color: var(--primary);
            color: white;
            font-weight: 500;
            border: none;
            padding: 1rem;
        }

        .btn-custom {
            padding: 0.8rem 2rem;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .action-btn {
            padding: 0.5rem;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 0.2rem;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            transform: translateY(-2px);
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--secondary);
            margin-bottom: 1rem;
        }

        .badge-ipk {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 500;
        }

        .loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid var(--accent);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Loading Indicator -->
    <div class="loading">
        <div class="loading-spinner"></div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-university me-2"></i>
                Sistem Informasi KRS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-arrow-left me-1"></i>
                            Kembali ke Dashboard
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Student Info Card -->
        <div class="student-info-card">
            <div class="row align-items-center">
                <div class="col-md-1 text-center">
                    <i class="fas fa-user-circle fa-3x text-secondary"></i>
                </div>
                <div class="col-md-11">
                    <h5 class="mb-2"><?= $mahasiswa['namaMhs'] ?></h5>
                    <div>
                        <span class="me-3">
                            <i class="fas fa-id-card me-1"></i>
                            <?= $mahasiswa['nim'] ?>
                        </span>
                        <span>
                            <i class="fas fa-chart-line me-1"></i>
                            IPK: <span class="badge bg-<?= $mahasiswa['ipk'] >= 3 ? 'success' : 'warning' ?> badge-ipk">
                                <?= $mahasiswa['ipk'] ?>
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total SKS Diambil</h6>
                            <h2 class="mb-0"><?= $totalSKS ?> SKS</h2>
                        </div>
                        <i class="fas fa-book fa-2x text-muted"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card sks-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">SKS Maksimal</h6>
                            <h2 class="mb-0"><?= $maxSKS ?> SKS</h2>
                        </div>
                        <i class="fas fa-chart-line fa-2x text-muted"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- KRS Form -->
        <div class="krs-form">
            <h5 class="mb-4">
                <i class="fas fa-plus-circle me-2"></i>
                Tambah Mata Kuliah
            </h5>
            <form id="formKRS" action="proses_krs.php" method="POST">
                <input type="hidden" name="mhs_id" value="<?= $id ?>">
                <div class="mb-3">
                    <label for="matakuliah" class="form-label">Pilih Mata Kuliah</label>
                    <select name="matakuliah" id="matakuliah" class="form-select" required>
                        <option value="" selected disabled>-- Pilih Mata Kuliah --</option>
                        <?php if (mysqli_num_rows($resultMatakuliah) > 0): ?>
                            <?php while ($rowMatakuliah = mysqli_fetch_assoc($resultMatakuliah)): ?>
                                <option value="<?= $rowMatakuliah['id'] ?>">
                                    <?= $rowMatakuliah['matakuliah'] ?> (<?= $rowMatakuliah['sks'] ?> SKS)
                                </option>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <option disabled>Semua mata kuliah telah diambil.</option>
                        <?php endif; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-custom btn-primary">
                    <i class="fas fa-save me-2"></i>
                    Simpan Mata Kuliah
                </button>
            </form>
        </div>

        <!-- Courses Table -->
        <div class="data-table">
            <h5 class="mb-4">
                <i class="fas fa-list me-2"></i>
                Mata Kuliah yang Sudah Diambil
            </h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="30%">Mata Kuliah</th>
                            <th width="15%" class="text-center">SKS</th>
                            <th width="20%" class="text-center">Kelompok</th>
                            <th width="20%" class="text-center">Ruangan</th>
                            <th width="10%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($resultTakenCourses) > 0): ?>
                            <?php $no = 1; mysqli_data_seek($resultTakenCourses, 0); ?>
                            <?php while ($rowKRS = mysqli_fetch_assoc($resultTakenCourses)): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= $rowKRS['matakuliah'] ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-info badge-ipk">
                                            <?= $rowKRS['sks'] ?> SKS
                                        </span>
                                    </td>
                                    <td class="text-center"><?= $rowKRS['kelp'] ?></td>
                                    <td class="text-center"><?= $rowKRS['ruangan'] ?></td>
                                    <td class="text-center">
                                        <button class="action-btn btn btn-danger"
                                                onclick="hapusKRS(<?= $rowKRS['id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <h5>Belum Ada Data</h5>
                                        <p class="text-muted">Silakan tambah mata kuliah baru</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Tentang Sistem KRS</h5>
                    <p>Sistem Informasi KRS adalah platform untuk mengelola kartu rencana studi mahasiswa secara efektif dan efisien.</p>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12 text-center">
                    <hr class="bg-light">
                    <p class="mb-0">&copy; <?= date('Y') ?> Sistem Informasi KRS by Muhammad Rifki Kurniawan</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fungsi untuk menampilkan loading
        function showLoading() {
            document.querySelector('.loading').style.display = 'flex';
        }

        function hideLoading() {
            document.querySelector('.loading').style.display = 'none';
        }

        // Fungsi untuk hapus KRS
        function hapusKRS(id) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: "Mata kuliah akan dihapus dari KRS!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#7f8c8d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                backdrop: 'rgba(0,0,0,0.4)'
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoading();
                    const formData = new FormData();
                    formData.append('id', id);

                    fetch('hapus_krs.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        hideLoading();
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            throw new Error(data.message);
                        }
                    })
                    .catch(error => {
                        hideLoading();
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: error.message
                        });
                    });
                }
            });
        }

        // Form submit handler dengan validasi SKS
        document.getElementById('formKRS').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const matakuliahSelect = document.getElementById('matakuliah');
            const selectedOption = matakuliahSelect.options[matakuliahSelect.selectedIndex];
            
            if (!selectedOption.value) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Silakan pilih mata kuliah terlebih dahulu!'
                });
                return;
            }

            const totalSKS = <?= $totalSKS ?>;
            const maxSKS = <?= $maxSKS ?>;
            const sksPilihan = parseInt(selectedOption.textContent.match(/\((\d+) SKS\)/)[1]);
            
            if (totalSKS + sksPilihan > maxSKS) {
                Swal.fire({
                    icon: 'error',
                    title: 'Melebihi Batas SKS',
                    text: `Total SKS (${totalSKS + sksPilihan}) akan melebihi batas maksimal (${maxSKS} SKS)`
                });
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menambahkan mata kuliah ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3498db',
                cancelButtonColor: '#7f8c8d',
                confirmButtonText: 'Ya, tambahkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoading();
                    const formData = new FormData(this);
                    
                    fetch('proses_krs.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        hideLoading();
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Mata kuliah berhasil ditambahkan',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            throw new Error(data.message || 'Terjadi kesalahan saat menyimpan data');
                        }
                    })
                    .catch(error => {
                        hideLoading();
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: error.message
                        });
                    });
                }
            });
        });

        // Event listener saat dokumen dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi tooltip
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Animasi untuk cards
            const cards = document.querySelectorAll('.stat-card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 200);
            });
        });
    </script>
</body>
</html>
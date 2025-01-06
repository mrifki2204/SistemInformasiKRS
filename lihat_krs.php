<?php
require_once 'koneksi.php';

// Validasi ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo "<script>
        alert('ID mahasiswa tidak valid');
        window.location.href = 'index.php';
    </script>";
    exit;
}

// Ambil data mahasiswa
$queryMahasiswa = "SELECT * FROM inputmhs WHERE id = $id";
$resultMahasiswa = mysqli_query($conn, $queryMahasiswa);
$mahasiswa = mysqli_fetch_assoc($resultMahasiswa);

if (!$mahasiswa) {
    echo "<script>
        alert('Data mahasiswa tidak ditemukan');
        window.location.href = 'index.php';
    </script>";
    exit;
}

// Ambil mata kuliah yang diambil
$queryKRS = "SELECT * FROM jwl_mhs WHERE mhs_id = $id";
$resultKRS = mysqli_query($conn, $queryKRS);

// Hitung total SKS
$totalSKS = 0;
if (mysqli_num_rows($resultKRS) > 0) {
    while ($row = mysqli_fetch_assoc($resultKRS)) {
        $totalSKS += $row['sks'];
    }
    mysqli_data_seek($resultKRS, 0);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat KRS - <?= $mahasiswa['namaMhs'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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

        .navbar-brand {
            font-size: 1.5rem;
            color: white !important;
            font-weight: 600;
        }

        .student-info-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-left: 5px solid var(--accent);
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

        .data-table {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .table thead th {
            background-color: var(--primary);
            color: white;
            font-weight: 500;
            border: none;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
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

        .badge-ipk {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 500;
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

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .footer {
            background: var(--primary);
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            .data-table {
                box-shadow: none;
            }
            .table thead th {
                background-color: #f8f9fa !important;
                color: #000 !important;
            }
        }
    </style>
</head>
<body>
    <!-- Loading Indicator -->
    <div class="loading">
        <div class="loading-spinner"></div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom no-print">
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
                <div class="col-md-8">
                    <h5 class="mb-3">Detail Mahasiswa</h5>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-circle fa-2x text-secondary me-3"></i>
                        <div>
                            <h6 class="mb-1"><?= $mahasiswa['namaMhs'] ?></h6>
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
                <div class="col-md-4 text-md-end mt-3 mt-md-0 no-print">
                    <a href="cetak_pdf.php?id=<?= $id ?>" class="btn btn-custom btn-success me-2" target="_blank">
                        <i class="fas fa-download me-1"></i>
                        Cetak PDF
                    </a>
                    <a href="input_krs.php?id=<?= $id ?>" class="btn btn-custom btn-primary">
                        <i class="fas fa-edit me-1"></i>
                        Edit KRS
                    </a>
                </div>
            </div>
        </div>

        <!-- KRS Table -->
        <div class="data-table">
            <h5 class="mb-4">
                <i class="fas fa-list me-2"></i>
                Daftar Mata Kuliah yang Diambil
            </h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="35%">Mata Kuliah</th>
                            <th width="15%" class="text-center">SKS</th>
                            <th width="20%" class="text-center">Kelompok</th>
                            <th width="25%" class="text-center">Ruangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($resultKRS) > 0): ?>
                            <?php $no = 1; while ($rowKRS = mysqli_fetch_assoc($resultKRS)): ?>
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
                                </tr>
                            <?php endwhile; ?>
                            <tr class="bg-light">
                                <td colspan="2" class="text-end"><strong>Total SKS</strong></td>
                                <td class="text-center">
                                    <span class="badge bg-primary badge-ipk">
                                        <?= $totalSKS ?> SKS
                                    </span>
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <h5>Belum Ada Mata Kuliah</h5>
                                        <p class="text-muted">Silakan input mata kuliah terlebih dahulu</p>
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

        // Event listener saat dokumen dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi tooltip
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</body>
</html>
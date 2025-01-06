<?php
require_once 'koneksi.php';

$query = "SELECT 
    m.id, 
    m.namaMhs, 
    m.nim, 
    m.ipk, 
    (CASE WHEN m.ipk < 3 THEN 20 ELSE 24 END) AS sks_maksimal, 
    GROUP_CONCAT(k.matakuliah SEPARATOR ', ') AS matkul_diambil 
    
    FROM inputmhs m 
    LEFT JOIN jwl_mhs k ON m.id = k.mhs_id 
    GROUP BY m.id 
    ORDER BY 
        SUBSTRING_INDEX(SUBSTRING_INDEX(m.nim, '.', 2), '.', -1) ASC,
        CAST(SUBSTRING_INDEX(m.nim, '.', -1) AS UNSIGNED) ASC";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Gagal mengambil data: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem KRS Mahasiswa</title>
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

        .navbar-brand {
            font-size: 1.5rem;
            color: white !important;
            font-weight: 600;
        }

        .dashboard-container {
            margin-top: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border-left: 5px solid var(--accent);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-card.total-mhs {
            border-left-color: var(--accent);
        }

        .stat-card.avg-ipk {
            border-left-color: var(--success);
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

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        .btn-custom-primary {
            background-color: var(--accent);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-custom-primary:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .badge-ipk {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 500;
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

        .search-box {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .search-box input {
            padding-left: 3rem;
            border-radius: 50px;
            border: 1px solid #ddd;
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary);
        }

        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
        }

        .footer {
            background: var(--primary);
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }

        @media (max-width: 768px) {
            .stat-card {
                margin-bottom: 1rem;
            }
            
            .action-btn {
                width: 30px;
                height: 30px;
                padding: 0.3rem;
            }
            
            .table thead th {
                padding: 0.5rem;
            }
            
            .table tbody td {
                padding: 0.5rem;
            }
        }

        /* Animasi Loading */
        .loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
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
    <div class="loading" style="display: none;">
        <div class="loading-spinner"></div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container"></div>

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
                            <i class="fas fa-home me-1"></i> Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="input_matakuliah.php">
                            <i class="fas fa-book me-1"></i> Input Mata Kuliah
                        </a>
                    </li>
            </div>
        </div>
    </nav>

    <div class="container dashboard-container">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="stat-card total-mhs">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Mahasiswa</h6>
                            <h2 class="mb-0"><?= mysqli_num_rows($result) ?></h2>
                        </div>
                        <i class="fas fa-users fa-2x text-muted"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card avg-ipk">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Rata-rata IPK</h6>
                            <h2 class="mb-0">
                                <?php
                                    $total_ipk = 0;
                                    $count = mysqli_num_rows($result);
                                    mysqli_data_seek($result, 0);
                                    while($row = mysqli_fetch_assoc($result)) {
                                        $total_ipk += $row['ipk'];
                                    }
                                    echo number_format($count > 0 ? $total_ipk/$count : 0, 2);
                                    mysqli_data_seek($result, 0);
                                ?>
                            </h2>
                        </div>
                        <i class="fas fa-chart-line fa-2x text-muted"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Button & Search -->
        <div class="row mb-4">
            <div class="col-md-6">
                <button id="btnInputMahasiswa" class="btn btn-custom-primary">
                    <i class="fas fa-user-plus me-2"></i>Tambah Mahasiswa
                </button>
            </div>
            <div class="col-md-6">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari mahasiswa...">
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="data-table">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="25%">Nama Mahasiswa</th>
                            <th width="15%">NIM</th>
                            <th width="10%">IPK</th>
                            <th width="15%">SKS Maksimal</th>
                            <th width="15%">KRS</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-3">
                                                <i class="fas fa-user-circle fa-2x text-secondary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold"><?= $row['namaMhs'] ?></div>
                                                <small class="text-muted">Mahasiswa Aktif</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= $row['nim'] ?></td>
                                    <td>
                                        <span class="badge badge-ipk bg-<?= $row['ipk'] >= 3 ? 'success' : 'warning' ?>">
                                            <?= $row['ipk'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info badge-ipk">
                                            <?= $row['sks_maksimal'] ?> SKS
                                        </span>
                                    </td>
                                    <td>
                                        <a href="lihat_krs.php?id=<?= $row['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i> Lihat KRS
                                        </a>
                                    </td>
                                    <td>
                                        <a href="input_krs.php?id=<?= $row['id'] ?>" 
                                           class="action-btn btn btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="action-btn btn btn-danger"
                                                onclick="hapusMahasiswa(<?= $row['id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <h5>Belum Ada Data</h5>
                                        <p class="text-muted">Silakan tambah data mahasiswa baru</p>
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

        // Fungsi untuk menampilkan toast
        function showToast(message, type = 'success') {
            const toastContainer = document.querySelector('.toast-container');
            const toast = document.createElement('div');
            toast.classList.add('toast', 'show', `bg-${type}`, 'text-white');
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <div class="toast-body">
                    ${message}
                </div>
            `;
            toastContainer.appendChild(toast);
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        // Fungsi pencarian
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let searchValue = this.value.toLowerCase();
            let tableRows = document.querySelectorAll('tbody tr');
            
            tableRows.forEach(row => {
                let text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchValue) ? '' : 'none';
            });
        });
// Fungsi hapus mahasiswa
function hapusMahasiswa(id) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: "Data yang dihapus tidak dapat dikembalikan!",
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
            
            // Buat FormData untuk kirim data
            const formData = new FormData();
            formData.append('id', id);
            
            // Kirim request POST ke hapus_mahasiswa.php
            fetch('hapus_mahasiswa.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data berhasil dihapus',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message || 'Gagal menghapus data'
                    });
                }
            })
            .catch(error => {
                hideLoading();
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat menghapus data.'
                });
            });
        }
    });
}

        // Fungsi input mahasiswa
        document.getElementById('btnInputMahasiswa').addEventListener('click', function() {
            Swal.fire({
                title: 'Input Data Mahasiswa Baru',
                html: `
                    <form id="formInputMahasiswa">
                        <div class="mb-3">
                            <label class="form-label">Nama Mahasiswa</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="namaMhs" 
                                   placeholder="Masukkan Nama Lengkap"
                                   required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">NIM</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="nim" 
                                   placeholder="Contoh: A11.2021.13840"
                                   pattern="[A-Z][0-9]{2}\.[0-9]{4}\.[0-9]{5}"
                                   title="Format: A11.2021.13840"
                                   required>
                            <div class="form-text">Format: A11.YYYY.XXXXX</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">IPK</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="ipk" 
                                   step="0.01" 
                                   min="0" 
                                   max="4" 
                                   placeholder="0.00"
                                   required>
                            <div class="form-text">Masukkan IPK (0.00 - 4.00)</div>
                        </div>
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#3498db',
                cancelButtonColor: '#7f8c8d',
                width: '500px',
                preConfirm: () => {
                    const namaMhs = document.getElementById('namaMhs').value;
                    const nim = document.getElementById('nim').value;
                    const ipk = document.getElementById('ipk').value;

                    // Validasi nama
                    if (!namaMhs) {
                        Swal.showValidationMessage('Nama mahasiswa harus diisi!');
                        return false;
                    }

                    // Validasi format NIM
                    const nimPattern = /^[A-Z][0-9]{2}\.[0-9]{4}\.[0-9]{5}$/;
                    if (!nim || !nimPattern.test(nim)) {
                        Swal.showValidationMessage('Format NIM tidak sesuai! Contoh: A11.2021.13840');
                        return false;
                    }

                    // Validasi IPK
                    if (!ipk || ipk < 0 || ipk > 4) {
                        Swal.showValidationMessage('IPK harus antara 0.0 dan 4.0!');
                        return false;
                    }

                    return { namaMhs, nim, ipk };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoading();
                    // Kirim data ke server menggunakan AJAX
                    fetch('proses_mahasiswa.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(result.value)
                    })
                    .then(response => response.json())
                    .then(data => {
                        hideLoading();
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Data mahasiswa berhasil ditambahkan.',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.message
                            });
                        }
                    })
                    .catch(error => {
                        hideLoading();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat menyimpan data.'
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

            // Cek status query parameter
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            const message = urlParams.get('message');
            
            if (status && message) {
                showToast(decodeURIComponent(message), status);
            }
        });
    </script>
</body>
</html>
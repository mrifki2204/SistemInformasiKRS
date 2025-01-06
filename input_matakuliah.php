<?php
require_once 'koneksi.php';

// Query untuk mengambil semua data mata kuliah
$query = "SELECT * FROM jwl_matakuliah ORDER BY matakuliah ASC";
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
    <title>Input Mata Kuliah - Sistem KRS</title>
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

        .input-form-container {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-left: 5px solid var(--accent);
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

        .footer {
            background: var(--primary);
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
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
        <!-- Form Input -->
        <div class="input-form-container">
            <h4 class="mb-4">
                <i class="fas fa-plus-circle me-2"></i>
                Input Mata Kuliah Baru
            </h4>
            <form id="formMatakuliah">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="matakuliah" class="form-label">Nama Mata Kuliah</label>
                        <input type="text" class="form-control" id="matakuliah" name="matakuliah" 
                               placeholder="Masukkan Nama Mata Kuliah" maxlength="250" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="sks" class="form-label">SKS</label>
                        <input type="number" class="form-control" id="sks" name="sks" 
                               min="1" max="6" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="kelp" class="form-label">Kelompok</label>
                        <input type="text" class="form-control" id="kelp" name="kelp" 
                               placeholder="Contoh: A11.4011" maxlength="10">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="ruangan" class="form-label">Ruangan</label>
                        <input type="text" class="form-control" id="ruangan" name="ruangan" 
                               placeholder="Contoh: H.3.5" maxlength="5">
                    </div>
                </div>
                <div class="text-end mt-3">
                    <button type="reset" class="btn btn-secondary me-2">
                        <i class="fas fa-undo me-1"></i>
                        Reset
                    </button>
                    <button type="submit" class="btn btn-custom btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Simpan Mata Kuliah
                    </button>
                </div>
            </form>
        </div>

        <!-- Data Table -->
        <div class="data-table">
            <h4 class="mb-4">
                <i class="fas fa-list me-2"></i>
                Daftar Mata Kuliah
            </h4>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="45%">Mata Kuliah</th>
                            <th width="10%" class="text-center">SKS</th>
                            <th width="15%" class="text-center">Kelompok</th>
                            <th width="15%" class="text-center">Ruangan</th>
                            <th width="10%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (mysqli_num_rows($result) > 0): 
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result)):
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $row['matakuliah'] ?></td>
                                <td class="text-center"><?= $row['sks'] ?></td>
                                <td class="text-center"><?= $row['kelp'] ?? '-' ?></td>
                                <td class="text-center"><?= $row['ruangan'] ?? '-' ?></td>
                                <td class="text-center">
                                    <button class="action-btn btn btn-danger" 
                                            onclick="hapusMatakuliah(<?= $row['id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php 
                            endwhile;
                        else: 
                        ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 text-secondary"></i>
                                    <p class="mb-0">Belum ada data mata kuliah.</p>
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
       // Form submit handler
document.getElementById('formMatakuliah').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validasi form
    const matakuliah = document.getElementById('matakuliah').value;
    const sks = document.getElementById('sks').value;

    if (matakuliah.length < 3) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Nama mata kuliah terlalu pendek'
        });
        return;
    }

    if (sks < 1 || sks > 6) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'SKS harus antara 1-6'
        });
        return;
    }

    // Konfirmasi sebelum submit
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah data mata kuliah sudah benar?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3498db',
        cancelButtonColor: '#7f8c8d',
        confirmButtonText: 'Ya, Simpan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            const formData = new FormData(this);
            
            fetch('proses_matakuliah.php', {
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
});

// Fungsi untuk menampilkan loading
function showLoading() {
    document.querySelector('.loading').style.display = 'flex';
}

function hideLoading() {
    document.querySelector('.loading').style.display = 'none';
}

// Fungsi untuk hapus mata kuliah
function hapusMatakuliah(id) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: "Data mata kuliah akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74c3c',
        cancelButtonColor: '#7f8c8d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            const formData = new FormData();
            formData.append('id', id);

            fetch('hapus_matakuliah.php', {
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
                        text: 'Data mata kuliah berhasil dihapus',
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

// Event listener saat dokumen dimuat
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi tooltip
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Animasi untuk form container
    const formContainer = document.querySelector('.input-form-container');
    formContainer.style.opacity = '0';
    formContainer.style.transform = 'translateY(-20px)';
    setTimeout(() => {
        formContainer.style.opacity = '1';
        formContainer.style.transform = 'translateY(0)';
        formContainer.style.transition = 'all 0.3s ease-in-out';
    }, 200);
});

</script>
</body>
</html>
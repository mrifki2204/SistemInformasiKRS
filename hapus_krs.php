<?php
header('Content-Type: application/json');
require_once 'koneksi.php';

// Validasi method request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed'
    ]);
    exit;
}

// Validasi ID
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'ID tidak valid'
    ]);
    exit;
}

// Periksa apakah data KRS ada
$checkQuery = "SELECT id FROM jwl_mhs WHERE id = $id";
$result = mysqli_query($conn, $checkQuery);

if (mysqli_num_rows($result) === 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Data KRS tidak ditemukan'
    ]);
    exit;
}

// Hapus data KRS
$deleteQuery = "DELETE FROM jwl_mhs WHERE id = $id";
if (mysqli_query($conn, $deleteQuery)) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Data KRS berhasil dihapus'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal menghapus data KRS'
    ]);
}

mysqli_close($conn);
?>
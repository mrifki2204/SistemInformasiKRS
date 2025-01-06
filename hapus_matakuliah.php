<?php
require_once 'koneksi.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed'
    ]);
    exit;
}

$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'ID mata kuliah tidak valid'
    ]);
    exit;
}

// Cek apakah mata kuliah sudah diambil mahasiswa
$checkQuery = "SELECT COUNT(*) as count FROM jwl_mhs WHERE matakuliah = (
    SELECT matakuliah FROM jwl_matakuliah WHERE id = $id
)";

$result = mysqli_query($conn, $checkQuery);
$row = mysqli_fetch_assoc($result);

if ($row['count'] > 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Mata kuliah tidak dapat dihapus karena sudah diambil oleh mahasiswa'
    ]);
    exit;
}

// Query delete
$query = "DELETE FROM jwl_matakuliah WHERE id = $id";

if (mysqli_query($conn, $query)) {
    if (mysqli_affected_rows($conn) > 0) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Data mata kuliah berhasil dihapus'
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'Data mata kuliah tidak ditemukan'
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal menghapus data: ' . mysqli_error($conn)
    ]);
}
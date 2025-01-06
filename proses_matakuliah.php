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

$matakuliah = trim($_POST['matakuliah'] ?? '');
$sks = (int)($_POST['sks'] ?? 0);
$kelp = trim($_POST['kelp'] ?? '');
$ruangan = trim($_POST['ruangan'] ?? '');

if (empty($matakuliah)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Nama mata kuliah tidak boleh kosong'
    ]);
    exit;
}

if ($sks < 1 || $sks > 6) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'SKS harus antara 1-6'
    ]);
    exit;
}

$query = "INSERT INTO jwl_matakuliah (matakuliah, sks, kelp, ruangan) 
          VALUES ('$matakuliah', $sks, '$kelp', '$ruangan')";

if (mysqli_query($conn, $query)) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Data mata kuliah berhasil disimpan'
    ]);
} else {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal menyimpan data: ' . mysqli_error($conn)
    ]);
}
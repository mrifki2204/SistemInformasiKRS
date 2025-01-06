<?php
require_once 'koneksi.php';

// Terima data JSON
$data = json_decode(file_get_contents('php://input'), true);

// Validasi data
if (empty($data['namaMhs']) || empty($data['nim']) || !isset($data['ipk'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Semua kolom harus diisi!'
    ]);
    exit;
}

// Sanitasi input
$namaMhs = mysqli_real_escape_string($conn, $data['namaMhs']);
$nim = mysqli_real_escape_string($conn, $data['nim']);
$ipk = floatval($data['ipk']);

// Validasi NIM unik
$checkNim = mysqli_query($conn, "SELECT nim FROM inputmhs WHERE nim = '$nim'");
if (mysqli_num_rows($checkNim) > 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'NIM sudah terdaftar!'
    ]);
    exit;
}

// Insert data
$query = "INSERT INTO inputmhs (namaMhs, nim, ipk) VALUES ('$namaMhs', '$nim', $ipk)";

if (mysqli_query($conn, $query)) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Data berhasil ditambahkan'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal menambahkan data: ' . mysqli_error($conn)
    ]);
}

mysqli_close($conn);
?>
<?php
header('Content-Type: application/json');
require_once 'koneksi.php';

try {
    $mhs_id = $_POST['mhs_id'] ?? null;
    $matakuliah_id = $_POST['matakuliah'] ?? null;

    if (!$mhs_id || !$matakuliah_id) {
        throw new Exception('Data tidak lengkap');
    }

    // Ambil data mata kuliah
    $queryMatkul = "SELECT * FROM jwl_matakuliah WHERE id = ?";
    $stmt = $conn->prepare($queryMatkul);
    $stmt->bind_param("i", $matakuliah_id);
    $stmt->execute();
    $resultMatkul = $stmt->get_result();
    $matkul = $resultMatkul->fetch_assoc();

    if (!$matkul) {
        throw new Exception('Mata kuliah tidak ditemukan');
    }

    // Insert ke tabel jwl_mhs
    $query = "INSERT INTO jwl_mhs (mhs_id, matakuliah, sks, kelp, ruangan) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isiss", $mhs_id, $matkul['matakuliah'], $matkul['sks'], $matkul['kelp'], $matkul['ruangan']);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Data berhasil disimpan']);
    } else {
        throw new Exception('Gagal menyimpan data');
    }

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
<?php
require_once 'koneksi.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Hapus data dari tabel jwl_mhs terlebih dahulu
    $query_krs = "DELETE FROM jwl_mhs WHERE mhs_id = $id";
    mysqli_query($conn, $query_krs);

    // Hapus data dari tabel inputmhs
    $query = "DELETE FROM inputmhs WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus data: ' . mysqli_error($conn)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID tidak tersedia']);
}
<?php
require_once 'koneksi.php';
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$id = isset($_GET['id']) ? $_GET['id'] : 0;
if ($id <= 0) {
    die("ID mahasiswa tidak valid.");
}

// Ambil data mahasiswa
$queryMahasiswa = "SELECT * FROM inputmhs WHERE id = $id";
$resultMahasiswa = mysqli_query($conn, $queryMahasiswa);
$mahasiswa = mysqli_fetch_assoc($resultMahasiswa);

// Ambil mata kuliah
$queryKRS = "SELECT * FROM jwl_mhs WHERE mhs_id = $id";
$resultKRS = mysqli_query($conn, $queryKRS);

// Hitung total SKS
$totalSKS = 0;
while ($row = mysqli_fetch_assoc($resultKRS)) {
    $totalSKS += $row['sks'];
}
mysqli_data_seek($resultKRS, 0);

$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>KRS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 15px;
            font-size: 12pt;
            background-color: #f8f9fc;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px;
            background: #4e73df;
            color: white;
            border-radius: 10px;
        }
        .header h1 {
            margin: 0;
            padding: 0;
            font-size: 18pt;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .header p {
            margin: 0;
            font-size: 10pt;
            color: rgba(255, 255, 255, 0.9);
        }
        .info-box {
            background-color: white;
            padding: 12px;
            margin: 15px 0;
            border-radius: 10px;
            font-size: 11pt;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-left: 5px solid #3498db;
        }
        .data-table {
            background: white;
            border-radius: 10px;
            padding: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 10pt;
        }
        th {
            background-color: #4e73df;
            color: white;
            padding: 8px;
            border: 1px solid #ddd;
            font-weight: 500;
        }
        td {
            padding: 6px 8px;
            border: 1px solid #ddd;
            vertical-align: middle;
        }
        tr:nth-child(even) {
            background-color: #f8f9fc;
        }
        .total-row {
            font-weight: bold;
            background-color: #eaecf4 !important;
        }
        .badge {
            padding: 3px 6px;
            border-radius: 15px;
            font-size: 9pt;
            color: white;
            display: inline-block;
        }
        .badge-success { 
            background-color: #1cc88a;
        }
        .badge-warning { 
            background-color: #f6c23e;
        }
        .text-center { 
            text-align: center; 
        }
        .timestamp {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 9pt;
            color: #666;
        }
        .info-text strong {
            color: #2c3e50;
        }
        body { 
            padding-top: 30px; 
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="timestamp">
        Dicetak: ' . date('d-m-Y H:i:s') . '
    </div>

    <div class="header">
        <h1><i class="fas fa-university"></i> Kartu Rencana Studi</h1>
        <p>Sistem Informasi KRS Mahasiswa</p>
    </div>

    <div class="info-box">
        <div class="info-text">
            <strong>Mahasiswa:</strong> ' . ($mahasiswa['namaMhs']) . ' | 
            <strong>NIM:</strong> ' . ($mahasiswa['nim']) . ' | 
            <strong>IPK:</strong> ' . $mahasiswa['ipk'] . '
        </div>
    </div>

    <div class="data-table">
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="40%">Matakuliah</th>
                    <th width="15%">SKS</th>
                    <th width="20%">Kelompok</th>
                    <th width="20%">Ruangan</th>
                </tr>
            </thead>
            <tbody>';

$no = 1;
while ($row = mysqli_fetch_assoc($resultKRS)) {
    $html .= '
                <tr>
                    <td class="text-center">' . $no++ . '</td>
                    <td>' . ($row['matakuliah']) . '</td>
                    <td class="text-center">' . $row['sks'] . '</td>
                    <td class="text-center">' . ($row['kelp']) . '</td>
                    <td class="text-center">' . ($row['ruangan']) . '</td>
                </tr>';
}

$html .= '
                <tr class="total-row">
                    <td colspan="2" style="text-align: right"><strong>Total SKS</strong></td>
                    <td class="text-center"><strong>' . $totalSKS . '</strong></td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>';

// Konfigurasi DomPDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$options->set('defaultFont', 'Arial');
$options->set('defaultMediaType', 'screen');
$options->set('dpi', 96);
$options->set('debugKeepTemp', false);
$options->set('debugCss', false);

// Buat instance DomPDF
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);

// Set paper size dan orientation
$dompdf->setPaper('A4', 'portrait');

// Render PDF
$dompdf->render();

// Output PDF (true untuk download otomatis)
$dompdf->stream('KRS_' . $mahasiswa['nim'] . '.pdf', array('Attachment' => true));
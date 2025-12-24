<?php
include "koneksi.php";

$kode = $_GET['kode'];

// Ambil data (tambahkan error handling sedikit jika data kosong)
$query = $koneksi->query("
    SELECT 
        p.kd_pendaftaran,
        p.tgl_pendaftaran,
        ps.nm_pasien,
        ps.nm_orangtua,
        ps.no_telp,
        ps.alamat,
        pk.nm_paket
    FROM tb_pendaftaran p
    JOIN tb_pasien ps ON p.id_pasien = ps.id_pasien
    JOIN tb_paket pk ON p.id_paket = pk.id_paket
    WHERE p.kd_pendaftaran = '$kode'
");

$data = $query->fetch_assoc();

// Jika data tidak ditemukan, redirect atau tampilkan pesan
if (!$data) {
    echo "<div style='text-align:center; margin-top:50px;'>Data pendaftaran tidak ditemukan.</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pendaftaran - <?= $data['kd_pendaftaran'] ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
    body {
        background-color: #f4f6f9;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Container Struk */
    .receipt-container {
        max-width: 600px;
        margin: 50px auto;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        position: relative;
    }

    /* Hiasan Atas (Top Border) */
    .receipt-top-border {
        height: 6px;
        background: linear-gradient(to right, #0d6efd, #0dcaf0);
    }

    .receipt-header {
        text-align: center;
        padding: 30px 20px 10px;
    }

    .clinic-name {
        font-size: 24px;
        font-weight: bold;
        color: #333;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .receipt-title {
        color: #6c757d;
        font-size: 14px;
        margin-top: 5px;
    }

    /* Box Kode Pendaftaran */
    .code-box {
        background-color: #e7f1ff;
        color: #0d6efd;
        padding: 15px;
        text-align: center;
        border-radius: 8px;
        margin: 20px 30px;
        border: 2px dashed #b6d4fe;
    }

    .code-label {
        font-size: 12px;
        text-transform: uppercase;
        color: #555;
        margin-bottom: 5px;
        display: block;
    }

    .code-value {
        font-size: 28px;
        font-weight: 800;
        letter-spacing: 2px;
    }

    /* Tabel Data */
    .receipt-body {
        padding: 0 30px 30px;
    }

    .table-data {
        width: 100%;
        font-family: 'Courier New', Courier, monospace;
        /* Font monospace agar rapi seperti struk */
        font-size: 15px;
    }

    .table-data td {
        padding: 8px 0;
        vertical-align: top;
        border-bottom: 1px dashed #eee;
    }

    .table-data tr:last-child td {
        border-bottom: none;
    }

    .label-col {
        width: 140px;
        color: #666;
        font-weight: 600;
    }

    .colon {
        width: 20px;
        text-align: center;
    }

    .value-col {
        color: #000;
        font-weight: 600;
    }

    /* Footer */
    .receipt-footer {
        background-color: #f8f9fa;
        padding: 20px;
        text-align: center;
        font-size: 13px;
        color: #6c757d;
        border-top: 1px solid #eee;
    }

    /* Area Tombol */
    .action-buttons {
        max-width: 600px;
        margin: 20px auto;
        text-align: center;
    }

    /* CSS KHUSUS UNTUK PRINT */
    @media print {
        body {
            background-color: #fff;
            -webkit-print-color-adjust: exact;
            /* Agar warna background tercetak */
        }

        .no-print {
            display: none !important;
            /* Hilangkan tombol saat print */
        }

        .receipt-container {
            box-shadow: none;
            margin: 0;
            width: 100%;
            max-width: 100%;
            border: 1px solid #ddd;
        }

        /* Menghilangkan header/footer browser bawaan (opsional) */
        @page {
            margin: 0.5cm;
        }
    }
    </style>
</head>

<body>

    <div class="receipt-container">
        <div class="receipt-top-border"></div>

        <div class="receipt-header">
            <div class="clinic-name"><i class="fas fa-clinic-medical me-2"></i>RUMAH SUNAT AZ-ZAINY</div>
            <div class="receipt-title">BUKTI PENDAFTARAN PASIEN</div>
        </div>

        <div class="code-box">
            <span class="code-label">Kode Pendaftaran</span>
            <span class="code-value"><?= $data['kd_pendaftaran']; ?></span>
        </div>

        <div class="receipt-body">
            <table class="table-data">
                <tr>
                    <td class="label-col">Tanggal Daftar</td>
                    <td class="colon">:</td>
                    <td class="value-col"><?= date('d/m/Y', strtotime($data['tgl_pendaftaran'])); ?></td>
                </tr>
                <tr>
                    <td class="label-col">Nama Pasien</td>
                    <td class="colon">:</td>
                    <td class="value-col"><?= strtoupper($data['nm_pasien']); ?></td>
                </tr>
                <tr>
                    <td class="label-col">Orang Tua</td>
                    <td class="colon">:</td>
                    <td class="value-col"><?= $data['nm_orangtua']; ?></td>
                </tr>
                <tr>
                    <td class="label-col">No. Telepon</td>
                    <td class="colon">:</td>
                    <td class="value-col"><?= $data['no_telp']; ?></td>
                </tr>
                <tr>
                    <td class="label-col">Paket Layanan</td>
                    <td class="colon">:</td>
                    <td class="value-col text-primary"><?= $data['nm_paket']; ?></td>
                </tr>
                <tr>
                    <td class="label-col">Alamat</td>
                    <td class="colon">:</td>
                    <td class="value-col"><?= $data['alamat']; ?></td>
                </tr>
            </table>
        </div>

        <div class="receipt-footer">
            <i class="fas fa-info-circle me-1"></i> Harap simpan struk ini sebagai bukti pendaftaran.<br>
            Tunjukkan kepada petugas resepsionis saat kedatangan.
        </div>
    </div>

    <div class="action-buttons no-print">
        <button onclick="window.print()" class="btn btn-primary btn-lg shadow-sm me-2">
            <i class="fas fa-print me-2"></i>Cetak Struk
        </button>
        <a href="form_pendaftaran.php" class="btn btn-outline-secondary btn-lg shadow-sm">
            <i class="fas fa-plus me-2"></i>Daftar Baru
        </a>
    </div>

</body>

</html>
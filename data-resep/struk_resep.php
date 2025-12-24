<?php
session_start();
include "../koneksi.php";

// Validasi Akses
if (!isset($_GET['kd_resep'])) {
    echo "Kode resep tidak ditemukan";
    exit();
}

$kode = $_GET["kd_resep"];

// 1. AMBIL HEADER (Prepared Statement)
$query_header = "SELECT a.*, d.nm_pasien, d.no_telp
                 FROM tb_resep a
                 JOIN tb_pemeriksaan b ON a.id_pemeriksaan = b.id_pemeriksaan
                 JOIN tb_pendaftaran c ON b.id_pendaftaran = c.id_pendaftaran
                 JOIN tb_pasien d ON c.id_pasien = d.id_pasien
                 WHERE a.kd_resep = ?";

$stmt = $koneksi->prepare($query_header);
$stmt->bind_param("s", $kode);
$stmt->execute();
$header = $stmt->get_result()->fetch_assoc();

if (!$header) {
    echo "Data resep tidak ditemukan";
    exit();
}

// 2. AMBIL DETAIL OBAT
$query_detail = "SELECT o.nm_obat, dr.jumlah_obat, o.harga_obat, dr.subharga_obat
                 FROM tb_detail_resep dr
                 JOIN tb_obat o ON dr.id_obat = o.id_obat
                 WHERE dr.id_resep = ?";

$stmt2 = $koneksi->prepare($query_detail);
$stmt2->bind_param("i", $header['id_resep']);
$stmt2->execute();
$detail = $stmt2->get_result();

// Helper: Format Tanggal Indo
function tgl_indo($tanggal) {
    $bulan = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
        5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Ags',
        9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
    ];
    $pecah = explode('-', $tanggal);
    return $pecah[2] . ' ' . $bulan[(int)$pecah[1]] . ' ' . $pecah[0];
}

// Helper: Format Rupiah
function rupiah($angka) {
    return number_format($angka, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Resep - <?= $header['kd_resep'] ?></title>
    <style>
    /* Reset & Base */
    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        padding: 20px;
        background-color: #f0f0f0;
        font-family: 'Courier New', Courier, monospace;
        /* Font Struk */
        font-size: 12px;
        color: #000;
    }

    /* Container Struk (Lebar Thermal 58mm/80mm) */
    .receipt {
        width: 100%;
        max-width: 300px;
        /* Lebar maksimal struk */
        margin: 0 auto;
        background: #fff;
        padding: 15px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    /* Header */
    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    .bold {
        font-weight: bold;
    }

    .logo {
        width: 60px;
        margin-bottom: 5px;
        filter: grayscale(100%);
        /* Agar logo terlihat bagus di print hitam putih */
    }

    .clinic-name {
        font-size: 14px;
        font-weight: bold;
        text-transform: uppercase;
        margin-bottom: 2px;
    }

    .clinic-address {
        font-size: 10px;
        margin-bottom: 10px;
    }

    /* Separator */
    .dashed {
        border-bottom: 1px dashed #000;
        margin: 8px 0;
        display: block;
    }

    /* Info Section */
    .info-table {
        width: 100%;
        margin-bottom: 5px;
    }

    .info-table td {
        padding: 1px 0;
        vertical-align: top;
    }

    .label {
        width: 80px;
    }

    /* Item List */
    .item-row {
        margin-bottom: 5px;
    }

    .item-name {
        font-weight: bold;
        display: block;
    }

    .item-calc {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
    }

    /* Total */
    .total-section {
        display: flex;
        justify-content: space-between;
        font-weight: bold;
        font-size: 14px;
        margin-top: 5px;
    }

    /* Footer */
    .footer {
        margin-top: 15px;
        font-size: 10px;
        text-align: center;
    }

    /* Buttons (No Print) */
    .no-print {
        text-align: center;
        margin-top: 20px;
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .btn {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-family: sans-serif;
        text-decoration: none;
        font-size: 13px;
    }

    .btn-print {
        background-color: #28a745;
        color: white;
    }

    .btn-close {
        background-color: #6c757d;
        color: white;
    }

    /* Print Media Query */
    @media print {
        body {
            background: none;
            padding: 0;
        }

        .receipt {
            box-shadow: none;
            max-width: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        .no-print {
            display: none;
        }

        @page {
            margin: 0;
            size: auto;
        }
    }
    </style>
</head>

<body>

    <div class="receipt">

        <div class="text-center">
            <div class="clinic-name"><i class="fas fa-clinic-medical me-2"></i>RUMAH SUNAT AZ-ZAINY</div>
        </div>

        <div class="dashed"></div>

        <table class="info-table">
            <tr>
                <td class="label">No. Resep</td>
                <td>: <?= $header['kd_resep'] ?></td>
            </tr>
            <tr>
                <td class="label">Tanggal</td>
                <td>: <?= tgl_indo($header['tgl_resep']) ?></td>
            </tr>
            <tr>
                <td class="label">Pasien</td>
                <td>: <?= strtoupper($header['nm_pasien']) ?></td>
            </tr>
        </table>

        <div class="dashed"></div>

        <?php while ($obat = $detail->fetch_assoc()): ?>
        <div class="item-row">
            <span class="item-name"><?= $obat['nm_obat'] ?></span>
            <div class="item-calc">
                <span><?= $obat['jumlah_obat'] ?> x <?= rupiah($obat['harga_obat']) ?></span>
                <span><?= rupiah($obat['subharga_obat']) ?></span>
            </div>
        </div>
        <?php endwhile; ?>

        <div class="dashed"></div>

        <div class="total-section">
            <span>TOTAL TAGIHAN</span>
            <span>Rp <?= rupiah($header['total']) ?></span>
        </div>

        <div class="dashed"></div>

        <div class="footer">
            -- TERIMA KASIH --<br>
            Semoga Lekas Sembuh<br>
            <br>
            <small>Dicetak oleh: <?= isset($_SESSION['user']) ? ucfirst($_SESSION['user']) : 'Admin' ?></small>
        </div>

    </div>

    <div class="no-print">
        <button onclick="window.print()" class="btn btn-print">Cetak Struk</button>
        <button onclick="window.close()" class="btn btn-close">Tutup</button>
    </div>

    <script>
    // Opsional: Otomatis print saat halaman dibuka
    window.onload = function() {
        // window.print(); // Uncomment jika ingin otomatis print
    }
    </script>

</body>

</html>
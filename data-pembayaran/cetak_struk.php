<?php
session_start();
include '../koneksi.php';

// Validasi ID
if (!isset($_GET['id'])) {
    echo "ID Pembayaran tidak ditemukan.";
    exit();
}

$id_pembayaran = $_GET['id'];

// 1. AMBIL DATA UTAMA (Header Struk)
// Join: Pembayaran -> Resep -> Pemeriksaan -> Pendaftaran -> Pasien & Paket
$query_header = "SELECT 
                    byr.kd_pembayaran, byr.tgl_pembayaran, byr.total_pembayaran, byr.jumlah_bayar, byr.kembalian,
                    pas.nm_pasien, pas.no_telp,
                    pkt.nm_paket, pem.hrg_paket,
                    rsp.id_resep, pem.id_pemeriksaan
                    
                 FROM tb_pembayaran byr
                 JOIN tb_resep rsp ON byr.id_resep = rsp.id_resep
                 JOIN tb_pemeriksaan pem ON rsp.id_pemeriksaan = pem.id_pemeriksaan
                 JOIN tb_pendaftaran pend ON pem.id_pendaftaran = pend.id_pendaftaran
                 JOIN tb_pasien pas ON pend.id_pasien = pas.id_pasien
                 LEFT JOIN tb_paket pkt ON pend.id_paket = pkt.id_paket
                 WHERE byr.id_pembayaran = ?";

$stmt = $koneksi->prepare($query_header);
$stmt->bind_param("i", $id_pembayaran);
$stmt->execute();
$header = $stmt->get_result()->fetch_assoc();

if (!$header) {
    echo "Data transaksi tidak ditemukan.";
    exit();
}

// 2. AMBIL DATA TINDAKAN
$query_tindakan = "SELECT tind.nm_tindakan, dp.hrg_tindakan 
                   FROM tb_detail_pemeriksaan dp
                   JOIN tb_tindakan_tambahan tind ON dp.id_tindakan = tind.id_tindakan
                   WHERE dp.id_pemeriksaan = ?";
$stmt_tind = $koneksi->prepare($query_tindakan);
$stmt_tind->bind_param("i", $header['id_pemeriksaan']);
$stmt_tind->execute();
$res_tindakan = $stmt_tind->get_result();

// 3. AMBIL DATA OBAT
$query_obat = "SELECT obt.nm_obat, dro.jumlah_obat, dro.subharga_obat 
               FROM tb_detail_resep dro
               JOIN tb_obat obt ON dro.id_obat = obt.id_obat
               WHERE dro.id_resep = ?";
$stmt_obat = $koneksi->prepare($query_obat);
$stmt_obat->bind_param("i", $header['id_resep']);
$stmt_obat->execute();
$res_obat = $stmt_obat->get_result();

// Helper Rupiah
function rupiah($angka){
    return number_format($angka, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Struk - <?= $header['kd_pembayaran'] ?></title>
    <style>
    body {
        font-family: 'Courier New', Courier, monospace;
        /* Font struk klasik */
        background-color: #f4f4f4;
        margin: 0;
        padding: 20px;
        font-size: 12px;
        color: #000;
    }

    /* Container Struk */
    .receipt {
        max-width: 300px;
        /* Lebar standar kertas thermal 80mm */
        margin: 0 auto;
        background-color: #fff;
        padding: 15px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    .text-left {
        text-align: left;
    }

    .bold {
        font-weight: bold;
    }

    .header {
        margin-bottom: 10px;
        padding-bottom: 10px;
        border-bottom: 1px dashed #000;
    }

    .logo {
        font-size: 16px;
        font-weight: bold;
        text-transform: uppercase;
    }

    .address {
        font-size: 10px;
        margin-bottom: 5px;
    }

    .meta-info {
        margin-bottom: 10px;
        border-bottom: 1px dashed #000;
        padding-bottom: 5px;
    }

    .meta-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2px;
    }

    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
    }

    .items-table th {
        border-bottom: 1px dashed #000;
        padding: 5px 0;
        text-align: left;
        font-size: 11px;
    }

    .items-table td {
        padding: 3px 0;
        vertical-align: top;
    }

    .total-section {
        border-top: 1px dashed #000;
        padding-top: 5px;
        margin-top: 5px;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 3px;
        font-size: 12px;
    }

    .grand-total {
        font-size: 14px;
        font-weight: bold;
        margin-top: 5px;
        border-top: 1px solid #000;
        padding-top: 5px;
    }

    .footer {
        margin-top: 15px;
        text-align: center;
        font-size: 10px;
        border-top: 1px dashed #000;
        padding-top: 10px;
    }

    /* Tombol Print (Hilang saat print) */
    .no-print {
        text-align: center;
        margin-top: 20px;
    }

    .btn {
        padding: 8px 15px;
        background: #007bff;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        font-family: Arial, sans-serif;
    }

    .btn-back {
        background: #6c757d;
    }

    @media print {
        body {
            background: none;
            padding: 0;
            margin: 0;
        }

        .receipt {
            box-shadow: none;
            max-width: 100%;
            width: 100%;
            padding: 0;
        }

        .no-print {
            display: none;
        }

        @page {
            margin: 0;
            size: auto;
        }

        /* Hilangkan margin browser */
    }
    </style>
</head>

<body>

    <div class="receipt">

        <div class="header text-center">
            <div class="logo">RUMAH SUNAT AZ-ZAINY</div>>
        </div>

        <div class="meta-info">
            <div class="meta-row">
                <span>No. Nota</span>
                <span>: <?= $header['kd_pembayaran'] ?></span>
            </div>
            <div class="meta-row">
                <span>Tanggal</span>
                <span>: <?= date('d/m/Y H:i', strtotime($header['tgl_pembayaran'])) ?></span>
            </div>
            <div class="meta-row">
                <span>Pasien</span>
                <span>: <?= substr($header['nm_pasien'], 0, 18) ?></span>
            </div>
            <div class="meta-row">
                <span>Kasir</span>
                <span>: <?= isset($_SESSION['user']) ? ucfirst($_SESSION['user']) : 'Admin' ?></span>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th width="55%">Item</th>
                    <th width="15%" class="text-center">Qty</th>
                    <th width="30%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $header['nm_paket'] ?></td>
                    <td class="text-center">1</td>
                    <td class="text-right"><?= rupiah($header['hrg_paket']) ?></td>
                </tr>

                <?php while ($t = $res_tindakan->fetch_assoc()): ?>
                <tr>
                    <td><?= $t['nm_tindakan'] ?></td>
                    <td class="text-center">1</td>
                    <td class="text-right"><?= rupiah($t['hrg_tindakan']) ?></td>
                </tr>
                <?php endwhile; ?>

                <?php while ($o = $res_obat->fetch_assoc()): ?>
                <tr>
                    <td><?= $o['nm_obat'] ?></td>
                    <td class="text-center"><?= $o['jumlah_obat'] ?></td>
                    <td class="text-right"><?= rupiah($o['subharga_obat']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-row grand-total">
                <span>TOTAL TAGIHAN</span>
                <span><?= rupiah($header['total_pembayaran']) ?></span>
            </div>
            <div class="total-row" style="margin-top: 5px;">
                <span>TUNAI</span>
                <span><?= rupiah($header['jumlah_bayar']) ?></span>
            </div>
            <div class="total-row">
                <span>KEMBALI</span>
                <span><?= rupiah($header['kembalian']) ?></span>
            </div>
        </div>

        <div class="footer">
            Terima Kasih atas kunjungan Anda.<br>
            Semoga Lekas Sembuh.
            <br><br>
            <small>*Struk ini bukti pembayaran sah</small>
        </div>

    </div>

    <div class="no-print">
        <button onclick="window.print()" class="btn">Cetak Struk</button>
        <a href="pembayaran.php" class="btn btn-back">Kembali</a>
    </div>

    <script>
    // Otomatis print saat halaman dibuka (opsional)
    window.onload = function() {
        window.print();
    }
    </script>

</body>

</html>
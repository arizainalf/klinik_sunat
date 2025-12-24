<?php
include "../koneksi.php";

// Set locale bahasa Indonesia (opsional, agar tanggal tampil dlm bhs Indo)
setlocale(LC_ALL, 'id_ID');

$tgl_awal = $_POST['tanggal_1'];
$tgl_akhir = $_POST['tanggal_2'];

// Validasi jika diakses langsung tanpa POST
if (empty($tgl_awal) || empty($tgl_akhir)) {
    header("Location: pembayaran.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Pembayaran Periode <?= date('d-m-Y', strtotime($tgl_awal)) ?> s/d
        <?= date('d-m-Y', strtotime($tgl_akhir)) ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
    body {
        font-family: 'Times New Roman', Times, serif;
        font-size: 14px;
        background: #fff;
    }

    /* Kop Surat Style */
    .header-report {
        border-bottom: 3px double #000;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .header-logo {
        width: 80px;
        height: auto;
    }

    .header-text h2 {
        margin: 0;
        font-weight: bold;
        font-size: 24px;
        text-transform: uppercase;
    }

    .header-text p {
        margin: 0;
        font-size: 13px;
    }

    /* Table Style */
    .table-report th {
        background-color: #f0f0f0 !important;
        border-bottom: 2px solid #000 !important;
        text-align: center;
        vertical-align: middle;
    }

    .table-report td {
        vertical-align: middle;
    }

    /* Signature Section */
    .signature-section {
        margin-top: 50px;
        text-align: right;
        page-break-inside: avoid;
        /* Jangan potong tanda tangan ke halaman baru */
    }

    .signature-box {
        display: inline-block;
        text-align: center;
        width: 200px;
    }

    .signature-space {
        height: 80px;
    }

    /* Print Settings */
    @media print {
        @page {
            size: A4;
            margin: 2cm;
        }

        body {
            -webkit-print-color-adjust: exact;
            margin: 0;
        }

        .no-print {
            display: none !important;
        }

        .header-report {
            margin-top: 0;
        }
    }
    </style>
</head>

<body>

    <div class="container-fluid p-4">

        <div class="no-print mb-4">
            <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print me-2"></i>Cetak
                Laporan</button>
            <button onclick="window.close()" class="btn btn-secondary"><i class="fas fa-times me-2"></i>Tutup</button>
        </div>

        <div class="row header-report align-items-center">
            <div class="col-2 text-center">
                <img src="../assets/img/logo.png" class="header-logo" alt="Logo Klinik"
                    onerror="this.style.display='none'">
            </div>
            <div class="col-10 header-text">
                <h2>POLIKLINIK RHEMA DELAPAN</h2>
                <p>Jl. Semeru No. 123, Kota Sehat, Jawa Barat</p>
                <p>Telp: (021) 9876543 | Email: support@rhemadelapan.com</p>
            </div>
        </div>

        <div class="text-center mb-4">
            <h4 class="fw-bold text-uppercase">Laporan Pembayaran</h4>
            <p class="mb-0">Periode: <strong><?= date('d/m/Y', strtotime($tgl_awal)) ?></strong> s/d
                <strong><?= date('d/m/Y', strtotime($tgl_akhir)) ?></strong></p>
        </div>

        <table class="table table-bordered table-sm table-report border-dark">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Kode Bayar</th>
                    <th>Nama Pasien</th>
                    <th>Tanggal</th>
                    <th>Tagihan</th>
                    <th>Bayar</th>
                    <th>Kembalian</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query JOIN untuk mendapatkan Nama Pasien
                // Pastikan struktur JOIN ini sesuai dengan database Anda
                $query = "SELECT tb_pembayaran.*, tb_pasien.nm_pasien 
                          FROM tb_pembayaran
                          JOIN tb_resep ON tb_pembayaran.id_resep = tb_resep.id_resep
                          JOIN tb_pemeriksaan ON tb_resep.id_pemeriksaan = tb_pemeriksaan.id_pemeriksaan
                          JOIN tb_pendaftaran ON tb_pemeriksaan.id_pendaftaran = tb_pendaftaran.id_pendaftaran
                          JOIN tb_pasien ON tb_pendaftaran.id_pasien = tb_pasien.id_pasien
                          WHERE (tgl_pembayaran BETWEEN ? AND ?)
                          ORDER BY tgl_pembayaran ASC";
                
                $stmt = $koneksi->prepare($query);
                $stmt->bind_param("ss", $tgl_awal, $tgl_akhir);
                $stmt->execute();
                $result = $stmt->get_result();

                $no = 1;
                $total_pendapatan = 0;

                if ($result->num_rows > 0) {
                    while ($data = $result->fetch_assoc()) { 
                        $total_pendapatan += $data['total_pembayaran'];
                ?>
                <tr>
                    <td class="text-center"><?= $no++; ?></td>
                    <td class="text-center fw-bold"><?= $data['kd_pembayaran']; ?></td>
                    <td><?= strtoupper($data['nm_pasien']); ?></td>
                    <td class="text-center"><?= date('d/m/Y', strtotime($data['tgl_pembayaran'])); ?></td>
                    <td class="text-end">Rp <?= number_format($data['total_pembayaran'], 0, ',', '.'); ?></td>
                    <td class="text-end">Rp <?= number_format($data['jumlah_bayar'], 0, ',', '.'); ?></td>
                    <td class="text-end">Rp <?= number_format($data['kembalian'], 0, ',', '.'); ?></td>
                </tr>
                <?php 
                    } 
                } else {
                    echo '<tr><td colspan="7" class="text-center py-3">Tidak ada data transaksi pada periode ini.</td></tr>';
                }
                ?>
            </tbody>
            <tfoot>
                <tr class="bg-light fw-bold">
                    <td colspan="4" class="text-center text-uppercase">Total Pendapatan Periode Ini</td>
                    <td class="text-end bg-warning bg-opacity-25" style="border-top: 2px solid black;">
                        Rp <?= number_format($total_pendapatan, 0, ',', '.'); ?>
                    </td>
                    <td colspan="2" style="background-color: #eaeaea;"></td>
                </tr>
            </tfoot>
        </table>

        <div class="signature-section">
            <div class="signature-box">
                <p>Kota Sehat, <?= date('d F Y') ?></p>
                <p class="mb-0">Mengetahui,</p>
                <p>Bagian Keuangan</p>

                <div class="signature-space"></div>

                <p class="fw-bold text-decoration-underline">
                    <?= isset($_SESSION['user']) ? ucfirst($_SESSION['user']) : 'Admin Keuangan' ?></p>
            </div>
        </div>

    </div>

    <script>
    window.onload = function() {
        window.print();
    }
    </script>

</body>

</html>
<?php
session_start();
include '../koneksi.php';

// Helper Function
if (!function_exists('formatRupiah')) {
    function formatRupiah($angka){
        return "Rp " . number_format($angka,0,',','.');
    }
}

// Cek Login
if (!isset($_SESSION["jabatan"])) {
    header("Location: ../login/index.php");
    exit();
}

$id_pemeriksaan = $_GET['id_pemeriksaan'];

// 1. AMBIL DATA UTAMA (Header)
// Join: Pemeriksaan -> Pendaftaran -> Pasien & Paket
$query_header = "SELECT 
                    a.kd_pemeriksaan, a.tgl_pemeriksaan, a.status_periksa, a.hrg_paket,
                    b.kd_pendaftaran, b.tgl_pendaftaran,
                    c.nm_pasien,
                    d.nm_paket
                 FROM tb_pemeriksaan a 
                 JOIN tb_pendaftaran b ON a.id_pendaftaran = b.id_pendaftaran
                 JOIN tb_pasien c ON b.id_pasien = c.id_pasien
                 JOIN tb_paket d ON b.id_paket = d.id_paket
                 WHERE a.id_pemeriksaan = ?";

$stmt = $koneksi->prepare($query_header);
$stmt->bind_param("i", $id_pemeriksaan);
$stmt->execute();
$header = $stmt->get_result()->fetch_assoc();

if (!$header) {
    echo "Data tidak ditemukan.";
    exit();
}

// Hitung Total Awal (Harga Paket)
$total_harga = $header['hrg_paket'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Pemeriksaan | Sistem Informasi Klinik</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
    body {
        font-family: 'Nunito', sans-serif;
        background-color: #f8f9fa;
        overflow-x: hidden;
    }

    /* Sidebar Styles (Sama seperti sebelumnya) */
    #wrapper {
        display: flex;
        width: 100%;
        align-items: stretch;
    }

    #sidebar-wrapper {
        min-height: 100vh;
        width: 250px;
        margin-left: -250px;
        background: #2c3e50;
        color: #fff;
        transition: margin 0.25s ease-out;
        position: fixed;
        z-index: 1000;
    }

    #sidebar-wrapper .sidebar-heading {
        padding: 1.5rem 1.25rem;
        font-size: 1.2rem;
        font-weight: bold;
        text-align: center;
        background: #1a252f;
    }

    #sidebar-wrapper .list-group-item {
        padding: 1rem 1.25rem;
        background-color: #2c3e50;
        color: #bdc3c7;
        border: none;
    }

    #sidebar-wrapper .list-group-item:hover {
        background-color: #34495e;
        color: #fff;
        text-decoration: none;
    }

    #sidebar-wrapper .list-group-item.active {
        background-color: #0d6efd;
        color: #fff;
    }

    #page-content-wrapper {
        width: 100%;
        margin-left: 0;
        transition: margin 0.25s ease-out;
    }

    body.sb-sidenav-toggled #sidebar-wrapper {
        margin-left: 0;
    }

    body.sb-sidenav-toggled #page-content-wrapper {
        margin-left: 250px;
    }

    @media (max-width: 768px) {
        #sidebar-wrapper {
            margin-left: -250px;
        }

        body.sb-sidenav-toggled #sidebar-wrapper {
            margin-left: 0;
        }

        body.sb-sidenav-toggled #page-content-wrapper {
            margin-left: 0;
        }
    }

    /* Readonly Input Style */
    .form-control[readonly] {
        background-color: #e9ecef;
        opacity: 1;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
        font-size: 0.9rem;
    }
    </style>
</head>

<body>

    <div class="d-flex" id="wrapper">
        <div class="border-end" id="sidebar-wrapper">
            <div class="sidebar-heading"><i class="fas fa-clinic-medical me-2"></i>RUMAH SUNAT AZ-ZAINY</div>
            <div class="list-group list-group-flush mt-3">
                <a href="../index.php" class="list-group-item list-group-item-action"><i
                        class="fas fa-tachometer-alt me-2"></i> Dashboard</a>

                <?php if ($_SESSION["jabatan"] == 'admin') : ?>
                <a href="#submenuMaster" class="list-group-item list-group-item-action" data-bs-toggle="collapse">
                    <i class="fas fa-database me-2"></i> Data Master <i class="fas fa-caret-down float-end"></i>
                </a>
                <div class="collapse bg-dark" id="submenuMaster">
                    <a href="../data-master/data-pasien/pasien.php"
                        class="list-group-item list-group-item-action bg-dark text-white ps-5 small">Data Pasien</a>
                </div>
                <a href="pemeriksaan.php" class="list-group-item list-group-item-action bg-primary text-white"><i
                        class="fas fa-stethoscope me-2"></i> Pemeriksaan</a>
                <?php elseif ($_SESSION["jabatan"] == 'pemeriksaan') : ?>
                <a href="pemeriksaan.php" class="list-group-item list-group-item-action bg-primary text-white"><i
                        class="fas fa-address-book me-2"></i> Data Pemeriksaan</a>
                <?php endif; ?>

                <a href="../login/logout.php" class="list-group-item list-group-item-action text-danger mt-4"><i
                        class="fas fa-sign-out-alt me-2"></i> Logout</a>
            </div>
        </div>

        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-white py-3 px-4 shadow-sm">
                <div class="d-flex align-items-center">
                    <button class="btn btn-outline-primary" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                    <h5 class="ms-3 mb-0 d-none d-md-block">Detail Pemeriksaan</h5>
                </div>
            </nav>

            <div class="container-fluid px-4 mt-4">

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item"><a href="pemeriksaan.php" class="text-decoration-none">Data
                                Pemeriksaan</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail</li>
                    </ol>
                </nav>

                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-info-circle me-2"></i>Info
                                Pemeriksaan: <?= $header['kd_pemeriksaan']; ?></h6>
                        </div>
                        <div>
                            <?php if ($header['status_periksa'] == 1): ?>
                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Selesai & Resep
                                Diterima</span>
                            <?php else: ?>
                            <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Belum Terima
                                Resep</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card-body p-4">

                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <h6 class="text-uppercase text-muted fw-bold small mb-3">Data Pasien</h6>
                                <div class="mb-2">
                                    <label class="form-label">Nama Pasien</label>
                                    <input type="text" class="form-control" value="<?= $header['nm_pasien']; ?>"
                                        readonly>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Kode Pendaftaran</label>
                                    <input type="text" class="form-control" value="<?= $header['kd_pendaftaran']; ?>"
                                        readonly>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Tanggal Daftar</label>
                                    <input type="text" class="form-control"
                                        value="<?= date('d F Y', strtotime($header['tgl_pendaftaran'])); ?>" readonly>
                                </div>
                            </div>

                            <div class="col-md-4 mb-4">
                                <h6 class="text-uppercase text-muted fw-bold small mb-3">Paket Layanan</h6>
                                <div class="mb-2">
                                    <label class="form-label">Nama Paket</label>
                                    <input type="text" class="form-control" value="<?= $header['nm_paket']; ?>"
                                        readonly>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Biaya Paket</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">Rp</span>
                                        <input type="text" class="form-control text-end fw-bold"
                                            value="<?= number_format($header['hrg_paket'], 0, ',', '.'); ?>" readonly>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Tanggal Periksa</label>
                                    <input type="text" class="form-control"
                                        value="<?= date('d F Y', strtotime($header['tgl_pemeriksaan'])); ?>" readonly>
                                </div>
                            </div>

                            <div class="col-md-4 mb-4">
                                <h6 class="text-uppercase text-muted fw-bold small mb-3">Tindakan Tambahan</h6>
                                <div class="table-responsive border rounded p-2 bg-light"
                                    style="max-height: 220px; overflow-y: auto;">
                                    <table class="table table-sm table-borderless mb-0">
                                        <thead class="text-muted small border-bottom">
                                            <tr>
                                                <th>Tindakan</th>
                                                <th class="text-end">Biaya</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            // Ambil Detail Tindakan
                                            $query_detail = "SELECT t.nm_tindakan, dp.hrg_tindakan 
                                                             FROM tb_detail_pemeriksaan dp
                                                             JOIN tb_tindakan_tambahan t ON dp.id_tindakan = t.id_tindakan
                                                             WHERE dp.id_pemeriksaan = ?";
                                            $stmt_detail = $koneksi->prepare($query_detail);
                                            $stmt_detail->bind_param("i", $id_pemeriksaan);
                                            $stmt_detail->execute();
                                            $result_detail = $stmt_detail->get_result();

                                            if ($result_detail->num_rows > 0) {
                                                while ($row = $result_detail->fetch_assoc()) {
                                                    $total_harga += $row['hrg_tindakan'];
                                            ?>
                                            <tr>
                                                <td><?= $row['nm_tindakan']; ?></td>
                                                <td class="text-end"><?= formatRupiah($row['hrg_tindakan']); ?></td>
                                            </tr>
                                            <?php 
                                                }
                                            } else {
                                                echo "<tr><td colspan='2' class='text-center text-muted small py-3'>Tidak ada tindakan tambahan</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <a href="pemeriksaan.php" class="btn btn-secondary px-4">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="d-inline-block bg-primary bg-opacity-10 p-3 rounded pe-5 ps-4 text-start">
                                    <small class="text-primary fw-bold text-uppercase d-block">Total Estimasi
                                        Biaya</small>
                                    <span class="fs-4 fw-bold text-primary"><?= formatRupiah($total_harga); ?></span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById("sidebarToggle").addEventListener("click", function() {
        document.body.classList.toggle("sb-sidenav-toggled");
    });
    </script>
</body>

</html>
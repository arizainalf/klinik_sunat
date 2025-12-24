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

$id_resep = $_GET['id_resep'];

// 1. AMBIL HEADER RESEP
$query_header = "SELECT 
                    a.kd_resep, a.tgl_resep, a.total, a.status_rsp, a.keterangan,
                    b.kd_pemeriksaan, 
                    c.kd_pendaftaran, 
                    d.nm_pasien, d.id_pasien
                 FROM tb_resep a
                 JOIN tb_pemeriksaan b ON a.id_pemeriksaan = b.id_pemeriksaan
                 JOIN tb_pendaftaran c ON b.id_pendaftaran = c.id_pendaftaran
                 JOIN tb_pasien d ON c.id_pasien = d.id_pasien
                 WHERE a.id_resep = ?";

$stmt = $koneksi->prepare($query_header);
$stmt->bind_param("i", $id_resep);
$stmt->execute();
$header = $stmt->get_result()->fetch_assoc();

if (!$header) {
    echo "Data resep tidak ditemukan.";
    exit();
}

// 2. AMBIL DETAIL OBAT
$query_detail = "SELECT 
                    dob.jumlah_obat, dob.subharga_obat,
                    obt.nm_obat, obt.harga_obat
                 FROM tb_detail_resep dob
                 JOIN tb_obat obt ON dob.id_obat = obt.id_obat
                 WHERE dob.id_resep = ?";

$stmt_detail = $koneksi->prepare($query_detail);
$stmt_detail->bind_param("i", $id_resep);
$stmt_detail->execute();
$details = $stmt_detail->get_result();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Resep | Sistem Informasi Klinik</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
    body {
        font-family: 'Nunito', sans-serif;
        background-color: #f8f9fa;
        overflow-x: hidden;
    }

    /* Sidebar Styles (Sama seperti modul lain) */
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

    /* Custom Styles */
    .form-control[readonly] {
        background-color: #e9ecef;
        opacity: 1;
    }

    .table-detail th {
        background-color: #f1f1f1;
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
                <a href="../index.php" class="list-group-item list-group-item-action"><i
                        class="fas fa-tachometer-alt me-2"></i> Dashboard</a>

                <?php if ($_SESSION["jabatan"] == 'admin') : ?>
                <a href="#submenuMaster" class="list-group-item list-group-item-action" data-bs-toggle="collapse">
                    <i class="fas fa-database me-2"></i> Data Master <i class="fas fa-caret-down float-end"></i>
                </a>
                <div class="collapse bg-dark" id="submenuMaster">
                    <a href="../data-master/data-pasien/pasien.php"
                        class="list-group-item list-group-item-action bg-dark text-white ps-5 small">Data Pasien</a>
                    <a href="../data-master/data-dokter/dokter.php"
                        class="list-group-item list-group-item-action bg-dark text-white ps-5 small">Data Dokter</a>
                    <a href="../data-master/data-obat/obat.php"
                        class="list-group-item list-group-item-action bg-dark text-white ps-5 small">Data Obat</a>
                    <a href="../data-master/data-paket/paket.php"
                        class="list-group-item list-group-item-action bg-dark text-white ps-5 small">Data Paket</a>
                    <a href="../data-master/data-tindakan/tindakan.php"
                        class="list-group-item list-group-item-action bg-dark text-white ps-5 small">Data Tindakan</a>
                </div>
                <a href="../data-pemeriksaan/pemeriksaan.php" class="list-group-item list-group-item-action"><i
                        class="fas fa-stethoscope me-2"></i> Pemeriksaan</a>
                <a href="resep.php" class="list-group-item list-group-item-action bg-primary text-white"><i
                        class="fas fa-prescription-bottle-alt me-2"></i> Resep Obat</a>
                <a href="../data-pembayaran/pembayaran.php" class="list-group-item list-group-item-action"><i
                        class="fas fa-cash-register me-2"></i> Kasir Pembayaran</a>
                <a href="../user.php" class="list-group-item list-group-item-action"><i
                        class="fas fa-users-cog me-2"></i> Data User</a>

                <?php elseif ($_SESSION["jabatan"] == 'pendaftaran') : ?>
                <a href="../data-master/data-pasien/pasien.php" class="list-group-item list-group-item-action"><i
                        class="fas fa-user-alt me-2"></i> Data Pasien</a>
                <a href="pendaftaran.php" class="list-group-item list-group-item-action bg-primary text-white"><i
                        class="fas fa-clipboard-list me-2"></i> Data Pendaftaran</a>
                <?php endif; ?>

                <a href="../login/logout.php" class="list-group-item list-group-item-action text-danger mt-4"><i
                        class="fas fa-sign-out-alt me-2"></i> Logout</a>
            </div>
        </div>

        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-white py-3 px-4 shadow-sm">
                <div class="d-flex align-items-center">
                    <button class="btn btn-outline-primary" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                    <h5 class="ms-3 mb-0 d-none d-md-block">Detail Resep Obat</h5>
                </div>
            </nav>

            <div class="container-fluid px-4 mt-4">

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item"><a href="resep.php" class="text-decoration-none">Data Resep</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail</li>
                    </ol>
                </nav>

                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="m-0 font-weight-bold text-primary"><i
                                    class="fas fa-file-prescription me-2"></i>Info Resep: <?= $header['kd_resep']; ?>
                            </h6>
                        </div>
                        <div>
                            <?php if ($header['status_rsp'] == 1): ?>
                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Lunas</span>
                            <?php else: ?>
                            <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Belum Bayar</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card-body p-4">

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <h6 class="text-uppercase text-muted fw-bold small mb-3">Data Pasien</h6>
                                <div class="mb-2">
                                    <label class="form-label small text-muted">Nama Pasien</label>
                                    <div class="fw-bold"><?= $header['nm_pasien']; ?></div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small text-muted">ID Pasien</label>
                                    <div><?= $header['id_pasien']; ?></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-uppercase text-muted fw-bold small mb-3">Data Referensi</h6>
                                <div class="mb-2">
                                    <label class="form-label small text-muted">Kode Pemeriksaan</label>
                                    <div class="fw-bold"><?= $header['kd_pemeriksaan']; ?></div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small text-muted">Kode Pendaftaran</label>
                                    <div><?= $header['kd_pendaftaran']; ?></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-uppercase text-muted fw-bold small mb-3">Info Resep</h6>
                                <div class="mb-2">
                                    <label class="form-label small text-muted">Tanggal Resep</label>
                                    <div><?= date('d F Y', strtotime($header['tgl_resep'])); ?></div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small text-muted">Keterangan / Dosis</label>
                                    <div class="fst-italic">
                                        <?= !empty($header['keterangan']) ? $header['keterangan'] : '-'; ?></div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h6 class="text-primary fw-bold mb-3"><i class="fas fa-pills me-2"></i>Rincian Obat</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-detail">
                                <thead>
                                    <tr>
                                        <th width="40%">Nama Obat</th>
                                        <th width="20%" class="text-end">Harga Satuan</th>
                                        <th width="15%" class="text-center">Jumlah</th>
                                        <th width="25%" class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $grand_total = 0;
                                    if ($details->num_rows > 0) {
                                        while ($d = $details->fetch_assoc()) {
                                            $grand_total += $d['subharga_obat'];
                                    ?>
                                    <tr>
                                        <td><?= $d['nm_obat']; ?></td>
                                        <td class="text-end"><?= formatRupiah($d['harga_obat']); ?></td>
                                        <td class="text-center"><?= $d['jumlah_obat']; ?></td>
                                        <td class="text-end fw-bold"><?= formatRupiah($d['subharga_obat']); ?></td>
                                    </tr>
                                    <?php 
                                        }
                                    } else {
                                        echo '<tr><td colspan="4" class="text-center text-muted">Tidak ada data obat</td></tr>';
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <td colspan="3" class="text-end fw-bold text-uppercase">Total Tagihan Obat</td>
                                        <td class="text-end fw-bold text-primary fs-5">
                                            <?= formatRupiah($grand_total); ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="mt-4">
                            <a href="resep.php" class="btn btn-secondary px-4">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <a href="struk_resep.php?kd_resep=<?= $header['kd_resep']; ?>" target="_blank"
                                class="btn btn-primary px-4 ms-2">
                                <i class="fas fa-print me-2"></i>Cetak Resep
                            </a>
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
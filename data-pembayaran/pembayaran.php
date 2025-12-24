<?php
session_start();
include '../koneksi.php';

// Helper Function Format Rupiah (Opsional jika functions.php belum ada)
if (!function_exists('formatRupiah')) {
    function formatRupiah($angka){
        return "Rp " . number_format($angka,0,',','.');
    }
}

if (!isset($_SESSION["jabatan"])) {
    header("Location: ../login/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kasir Pembayaran | Sistem Informasi Klinik</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

    <style>
    body {
        font-family: 'Nunito', sans-serif;
        background-color: #f8f9fa;
        overflow-x: hidden;
    }

    /* Sidebar & Layout Styles (Sama seperti sebelumnya) */
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
                    <a href="../data-master/data-dokter/dokter.php"
                        class="list-group-item list-group-item-action bg-dark text-white ps-5 small">Data Dokter</a>
                    <a href="../data-master/data-obat/obat.php"
                        class="list-group-item list-group-item-action bg-dark text-white ps-5 small">Data Obat</a>
                    <a href="../data-master/data-poli/poli.php"
                        class="list-group-item list-group-item-action bg-dark text-white ps-5 small">Data Poli</a>
                </div>
                <a href="../data-pendaftaran/pendaftaran.php" class="list-group-item list-group-item-action"><i
                        class="fas fa-clipboard-list me-2"></i> Pendaftaran</a>
                <a href="../data-pemeriksaan/pemeriksaan.php" class="list-group-item list-group-item-action"><i
                        class="fas fa-stethoscope me-2"></i> Pemeriksaan</a>
                <a href="../data-resep/resep.php" class="list-group-item list-group-item-action"><i
                        class="fas fa-prescription-bottle-alt me-2"></i> Resep Obat</a>
                <a href="pembayaran.php" class="list-group-item list-group-item-action bg-primary text-white"><i
                        class="fas fa-cash-register me-2"></i> Kasir Pembayaran</a>
                <a href="../user.php" class="list-group-item list-group-item-action"><i
                        class="fas fa-users-cog me-2"></i> Data User</a>

                <?php elseif ($_SESSION["jabatan"] == 'pembayaran') : ?>
                <a href="pembayaran.php" class="list-group-item list-group-item-action bg-primary text-white"><i
                        class="fas fa-cash-register me-2"></i> Kasir Pembayaran</a>
                <?php endif; ?>

                <a href="../login/logout.php" class="list-group-item list-group-item-action text-danger mt-4"><i
                        class="fas fa-sign-out-alt me-2"></i> Logout</a>
            </div>
        </div>

        <div id="page-content-wrapper">

            <nav class="navbar navbar-expand-lg navbar-light bg-white py-3 px-4 shadow-sm">
                <div class="d-flex align-items-center">
                    <button class="btn btn-outline-primary" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                    <h5 class="ms-3 mb-0 d-none d-md-block">Kasir Pembayaran</h5>
                </div>
            </nav>

            <div class="container-fluid px-4 mt-4">

                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-print me-2"></i>Cetak Laporan
                            Pembayaran</h6>
                    </div>
                    <div class="card-body">
                        <form action="cetak_pembayaran.php" method="POST" target="_blank">
                            <div class="row align-items-end">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <label class="form-label text-muted small fw-bold">DARI TANGGAL</label>
                                    <input type="date" class="form-control" name="tanggal_1" required>
                                </div>
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <label class="form-label text-muted small fw-bold">SAMPAI TANGGAL</label>
                                    <input type="date" class="form-control" name="tanggal_2" required>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-print me-2"></i>Cetak Laporan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history me-2"></i>Riwayat
                            Transaksi</h6>
                        <a href="pembayaran_tambah.php" class="btn btn-success btn-sm shadow-sm">
                            <i class="fas fa-plus-circle me-1"></i> Transaksi Baru
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle" id="dataTable" width="100%">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kode Bayar</th>
                                        <th>Nama Pasien</th>
                                        <th>Kode Resep</th>
                                        <th>Total Tagihan</th>
                                        <th>Jml Bayar</th>
                                        <th>Kembalian</th>
                                        <th>Tgl Bayar</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $ambil = $koneksi->query("SELECT * FROM tb_pembayaran a
                                        JOIN tb_resep b ON a.id_resep = b.id_resep
                                        JOIN tb_pemeriksaan c ON b.id_pemeriksaan = c.id_pemeriksaan
                                        JOIN tb_pendaftaran d ON c.id_pendaftaran = d.id_pendaftaran
                                        JOIN tb_pasien e ON d.id_pasien = e.id_pasien
                                        ORDER BY a.id_pembayaran DESC");
                                    
                                    while ($pecah = $ambil->fetch_assoc()) { 
                                    ?>
                                    <tr>
                                        <td class="fw-bold text-primary"><?= $pecah['kd_pembayaran']; ?></td>
                                        <td><?= $pecah['nm_pasien']; ?></td>
                                        <td><span
                                                class="badge bg-light text-dark border"><?= $pecah['kd_resep']; ?></span>
                                        </td>
                                        <td class="fw-bold"><?= formatRupiah($pecah['total_pembayaran']); ?></td>
                                        <td><?= formatRupiah($pecah['jumlah_bayar']); ?></td>
                                        <td class="text-success"><?= formatRupiah($pecah['kembalian']); ?></td>
                                        <td><?= date('d/m/Y', strtotime($pecah['tgl_pembayaran'])); ?></td>
                                        <td class="text-center">
                                            <?php if ($pecah['status_pembayaran'] == 1) { ?>
                                            <span class="badge bg-success rounded-pill"><i
                                                    class="fas fa-check-circle me-1"></i>Lunas</span>
                                            <?php } else { ?>
                                            <span class="badge bg-danger rounded-pill"><i
                                                    class="fas fa-times-circle me-1"></i>Belum</span>
                                            <?php } ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="cetak_struk.php?id=<?= $pecah['id_pembayaran']; ?>" target="_blank"
                                                class="btn btn-secondary btn-sm" title="Cetak Struk">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script>
    // Toggle Sidebar
    document.getElementById("sidebarToggle").addEventListener("click", function() {
        document.body.classList.toggle("sb-sidenav-toggled");
    });

    // DataTable Init
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "order": [
                [0, "desc"]
            ] // Urutkan berdasarkan Kode Bayar (terbaru)
        });
    });
    </script>
</body>

</html>
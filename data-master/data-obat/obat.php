<?php
session_start();
include '../../koneksi.php';

// Cek Login
if (!isset($_SESSION["jabatan"])) {
    header("Location: ../../login/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Obat | Sistem Informasi Klinik</title>

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

    /* Sidebar Styling (Sama seperti Dashboard) */
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
        color: #fff;
    }

    #sidebar-wrapper .list-group-item {
        padding: 1rem 1.25rem;
        background-color: #2c3e50;
        color: #bdc3c7;
        border: none;
        font-size: 0.95rem;
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

    /* Media Query Mobile */
    @media (max-width: 768px) {
        #sidebar-wrapper {
            margin-left: -250px;
        }

        body.sb-sidenav-toggled #sidebar-wrapper {
            margin-left: 0;
        }

        body.sb-sidenav-toggled #page-content-wrapper {
            margin-left: 0;
            position: relative;
        }

        body.sb-sidenav-toggled #page-content-wrapper::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
    }

    /* Table Styling */
    .table thead th {
        background-color: #0d6efd;
        color: white;
        border: none;
    }

    .card {
        border-radius: 10px;
        border: none;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
    }
    </style>
</head>

<body>

    <div class="d-flex" id="wrapper">

        <div class="border-end" id="sidebar-wrapper">
            <div class="sidebar-heading">
                <i class="fas fa-clinic-medical me-2"></i>RUMAH SUNAT AZ-ZAINY
            </div>
            <div class="list-group list-group-flush mt-3">
                <a href="../../index.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>

                <?php if ($_SESSION["jabatan"] == 'admin') : ?>
                <a href="#submenuMaster" class="list-group-item list-group-item-action" data-bs-toggle="collapse"
                    aria-expanded="true">
                    <i class="fas fa-database me-2"></i> Data Master <i class="fas fa-caret-down float-end"></i>
                </a>
                <div class="collapse show bg-dark" id="submenuMaster">
                    <a href="../data-pasien/pasien.php"
                        class="list-group-item list-group-item-action bg-dark text-white ps-5 small">Data Pasien</a>
                    <a href="../data-dokter/dokter.php"
                        class="list-group-item list-group-item-action bg-dark text-white ps-5 small">Data Dokter</a>
                    <a href="obat.php"
                        class="list-group-item list-group-item-action bg-primary text-white ps-5 small">Data Obat</a>
                    <a href="../data-paket/paket.php"
                        class="list-group-item list-group-item-action bg-dark text-white ps-5 small">Data Paket</a>
                    <a href="../data-tindakan/tindakan.php"
                        class="list-group-item list-group-item-action bg-dark text-white ps-5 small">Data Tindakan</a>
                </div>

                <a href="../../data-pendaftaran/pendaftaran.php" class="list-group-item list-group-item-action"><i
                        class="fas fa-clipboard-list me-2"></i> Pendaftaran</a>
                <a href="../../data-pemeriksaan/pemeriksaan.php" class="list-group-item list-group-item-action"><i
                        class="fas fa-stethoscope me-2"></i> Pemeriksaan</a>
                <a href="../../data-resep/resep.php" class="list-group-item list-group-item-action"><i
                        class="fas fa-prescription-bottle-alt me-2"></i> Resep Obat</a>
                <a href="../../data-pembayaran/pembayaran.php" class="list-group-item list-group-item-action"><i
                        class="fas fa-cash-register me-2"></i> Kasir</a>
                <a href="../../user.php" class="list-group-item list-group-item-action"><i
                        class="fas fa-users-cog me-2"></i> Data User</a>

                <?php endif; ?>

                <a href="../../login/logout.php" class="list-group-item list-group-item-action text-danger mt-4">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
            </div>
        </div>

        <div id="page-content-wrapper">

            <nav class="navbar navbar-expand-lg navbar-light bg-white py-3 px-4 shadow-sm">
                <div class="d-flex align-items-center">
                    <button class="btn btn-outline-primary" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                    <h5 class="ms-3 mb-0 d-none d-md-block">Data Obat</h5>
                </div>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown"
                                role="button" data-bs-toggle="dropdown">
                                <div class="bg-primary text-white rounded-circle me-2 d-flex justify-content-center align-items-center"
                                    style="width: 35px; height: 35px;">
                                    <i class="fas fa-user"></i>
                                </div>
                                <span class="fw-bold text-dark"><?= ucfirst($_SESSION["user"] ?? "User"); ?></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end shadow border-0">
                                <a class="dropdown-item text-danger" href="../../login/logout.php">Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="container-fluid px-4 mt-4">

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../../index.php" class="text-decoration-none">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item text-muted">Data Master</li>
                        <li class="breadcrumb-item active" aria-current="page">Data Obat</li>
                    </ol>
                </nav>

                <div class="card mb-4">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-pills me-2"></i>Daftar Obat
                            Klinik</h6>
                        <a href="obat_tambah.php" class="btn btn-primary btn-sm shadow-sm">
                            <i class="fas fa-plus me-1"></i> Tambah Obat Baru
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover w-100" id="dataTable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">No</th>
                                        <th>Kode</th>
                                        <th>Nama Obat</th>
                                        <th>Jenis</th>
                                        <th>Stok</th>
                                        <th>Harga (Rp)</th>
                                        <th>Exp. Date</th>
                                        <th width="15%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $nomor = 1; 
                                    $ambil = $koneksi->query("SELECT * FROM tb_obat ORDER BY id_obat DESC");
                                    while ($pecah = $ambil->fetch_assoc()) { 
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= $nomor; ?></td>
                                        <td><span
                                                class="badge bg-light text-dark border"><?= $pecah['kd_obat']; ?></span>
                                        </td>
                                        <td class="fw-bold"><?= $pecah['nm_obat']; ?></td>
                                        <td><?= $pecah['jenis_obat']; ?></td>
                                        <td>
                                            <?php if ($pecah['stok'] <= 5) { ?>
                                            <span class="badge bg-danger rounded-pill"><?= $pecah['stok']; ?>
                                                (Kritis)</span>
                                            <?php } else { ?>
                                            <span class="badge bg-success rounded-pill"><?= $pecah['stok']; ?></span>
                                            <?php } ?>
                                        </td>
                                        <td><?= number_format($pecah['harga_obat'], 0, ',', '.'); ?></td>
                                        <td><?= date('d M Y', strtotime($pecah['exp_obat'])); ?></td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="obat_view.php?id_obat=<?= $pecah['id_obat']; ?>"
                                                    class="btn btn-info btn-sm text-white" title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="obat_ubah.php?id_obat=<?= $pecah['id_obat']; ?>"
                                                    class="btn btn-warning btn-sm text-white" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="obat_hapus.php?id_obat=<?= $pecah['id_obat']; ?>"
                                                    class="btn btn-danger btn-sm" title="Hapus"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus obat <?= $pecah['nm_obat']; ?>?');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php $nomor++; } ?>
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

    // Initialize DataTables
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json"
            }
        });
    });
    </script>
</body>

</html>
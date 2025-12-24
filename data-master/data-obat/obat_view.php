<?php
session_start();
include '../../koneksi.php';

// Cek Login
if (!isset($_SESSION["jabatan"])) {
    echo "<script>location='../../login/index.php'</script>";
    exit();
}

// Mengambil ID dari URL
$id_obat = $_GET['id_obat'];

// Keamanan: Menggunakan Prepared Statement (Mencegah SQL Injection)
$stmt = $koneksi->prepare("SELECT * FROM tb_obat WHERE id_obat = ?");
$stmt->bind_param("s", $id_obat);
$stmt->execute();
$result = $stmt->get_result();
$pecah = $result->fetch_assoc();

// Jika data tidak ditemukan
if (!$pecah) {
    echo "<script>alert('Data tidak ditemukan'); location='obat.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Obat | Sistem Informasi Klinik</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
    body {
        font-family: 'Nunito', sans-serif;
        background-color: #f8f9fa;
        overflow-x: hidden;
    }

    /* Sidebar Styling */
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

    /* Custom Form View Style */
    .form-control[readonly] {
        background-color: #e9ecef;
        opacity: 1;
        cursor: not-allowed;
        border: 1px solid #ced4da;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
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
                    <h5 class="ms-3 mb-0 d-none d-md-block">Detail Data Obat</h5>
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
                        <li class="breadcrumb-item"><a href="obat.php" class="text-decoration-none">Data Obat</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail Obat</li>
                    </ol>
                </nav>

                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary"><i
                                    class="fas fa-info-circle me-2"></i>Informasi Obat: <?= $pecah['nm_obat']; ?></h6>
                            <span class="badge bg-secondary">Mode: Read Only</span>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <form>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">ID Obat (System)</label>
                                        <input type="text" class="form-control" value="<?= $pecah['id_obat']; ?>"
                                            readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Kode Obat</label>
                                        <input type="text" class="form-control" value="<?= $pecah['kd_obat']; ?>"
                                            readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Nama Obat</label>
                                        <input type="text" class="form-control" value="<?= $pecah['nm_obat']; ?>"
                                            readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Jenis Obat</label>
                                        <input type="text" class="form-control" value="<?= $pecah['jenis_obat']; ?>"
                                            readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Stok Tersedia</label>
                                        <input type="text"
                                            class="form-control fw-bold <?= $pecah['stok'] <= 5 ? 'text-danger' : 'text-success' ?>"
                                            value="<?= $pecah['stok']; ?> Unit" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Harga Satuan</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">Rp</span>
                                            <input type="text" class="form-control"
                                                value="<?= number_format($pecah['harga_obat'], 0, ',', '.'); ?>"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Kedaluwarsa (Expired)</label>
                                        <input type="date" class="form-control" value="<?= $pecah['exp_obat']; ?>"
                                            readonly>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-end gap-2">
                                <a href="obat.php" class="btn btn-secondary px-4">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                                <a href="obat_ubah.php?id_obat=<?= $pecah['id_obat']; ?>"
                                    class="btn btn-warning text-white px-4">
                                    <i class="fas fa-edit me-2"></i>Ubah Data
                                </a>
                            </div>
                        </form>
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
<?php
session_start();
include 'koneksi.php';

// Cek Login
if (!isset($_SESSION["jabatan"])) {
    header("Location: login/index.php");
    exit();
}

// FUNGSI HELPER UNTUK MENGHITUNG DATA (Lebih Ringan dari SELECT *)
function hitungData($koneksi, $table, $where = "") {
    $query = "SELECT COUNT(*) as total FROM $table $where";
    $result = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

// Mengambil Statistik Data
$jml_pasien     = hitungData($koneksi, "tb_pasien");
$jml_obat       = hitungData($koneksi, "tb_obat");
$jml_daftar     = hitungData($koneksi, "tb_pendaftaran", "WHERE status = '0'"); // Belum diperiksa
$jml_bayar      = hitungData($koneksi, "tb_resep", "WHERE status_rsp = '0'");   // Belum dibayar
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | Sistem Informasi Klinik</title>
    
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
        
        /* Toggled State */
        body.sb-sidenav-toggled #sidebar-wrapper {
            margin-left: 0;
        }
        body.sb-sidenav-toggled #page-content-wrapper {
            margin-left: 250px;
        }

        /* Navbar */
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }

        /* Cards Dashboard */
        .card-stats {
            border: none;
            border-radius: 15px;
            color: white;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .card-stats:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .card-stats .card-body {
            z-index: 2;
            position: relative;
        }
        .card-stats .icon-bg {
            position: absolute;
            right: 15px;
            bottom: 10px;
            font-size: 80px;
            opacity: 0.2;
            z-index: 1;
        }
        
        /* Warna Gradient Card */
        .bg-gradient-primary { background: linear-gradient(45deg, #4e73df, #224abe); }
        .bg-gradient-success { background: linear-gradient(45deg, #1cc88a, #13855c); }
        .bg-gradient-info    { background: linear-gradient(45deg, #36b9cc, #258391); }
        .bg-gradient-warning { background: linear-gradient(45deg, #f6c23e, #dda20a); }

        /* Media Query untuk Mobile */
        @media (max-width: 768px) {
            #sidebar-wrapper { margin-left: -250px; }
            body.sb-sidenav-toggled #sidebar-wrapper { margin-left: 0; }
            body.sb-sidenav-toggled #page-content-wrapper { margin-left: 0; position: relative; }
            body.sb-sidenav-toggled #page-content-wrapper::before {
                content: ""; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(0,0,0,0.5); z-index: 999;
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
                <a href="index.php" class="list-group-item list-group-item-action active">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>

                <?php if ($_SESSION["jabatan"] == 'admin') : ?>
                    <a href="#submenuMaster" class="list-group-item list-group-item-action" data-bs-toggle="collapse">
                        <i class="fas fa-database me-2"></i> Data Master <i class="fas fa-caret-down float-end"></i>
                    </a>
                    <div class="collapse bg-dark" id="submenuMaster">
                        <a href="data-master/data-pasien/pasien.php" class="list-group-item list-group-item-action bg-dark text-white ps-5 small">Data Pasien</a>
                        <a href="data-master/data-dokter/dokter.php" class="list-group-item list-group-item-action bg-dark text-white ps-5 small">Data Dokter</a>
                        <a href="data-master/data-obat/obat.php" class="list-group-item list-group-item-action bg-dark text-white ps-5 small">Data Obat</a>
                        <a href="data-master/data-poli/poli.php" class="list-group-item list-group-item-action bg-dark text-white ps-5 small">Data Poli</a>
                    </div>
                    <a href="data-pendaftaran/pendaftaran.php" class="list-group-item list-group-item-action"><i class="fas fa-clipboard-list me-2"></i> Pendaftaran</a>
                    <a href="data-pemeriksaan/pemeriksaan.php" class="list-group-item list-group-item-action"><i class="fas fa-stethoscope me-2"></i> Pemeriksaan</a>
                    <a href="data-resep/resep.php" class="list-group-item list-group-item-action"><i class="fas fa-prescription-bottle-alt me-2"></i> Resep Obat</a>
                    <a href="data-pembayaran/pembayaran.php" class="list-group-item list-group-item-action"><i class="fas fa-cash-register me-2"></i> Kasir</a>
                    <a href="user.php" class="list-group-item list-group-item-action"><i class="fas fa-users-cog me-2"></i> Data User</a>

                <?php elseif ($_SESSION["jabatan"] == 'pendaftaran') : ?>
                    <a href="data-master/data-pasien/pasien.php" class="list-group-item list-group-item-action"><i class="fas fa-user-injured me-2"></i> Data Pasien</a>
                    <a href="data-pendaftaran/pendaftaran.php" class="list-group-item list-group-item-action"><i class="fas fa-clipboard-list me-2"></i> Data Pendaftaran</a>

                <?php elseif ($_SESSION["jabatan"] == 'pemeriksaan') : ?>
                    <a href="data-pemeriksaan/pemeriksaan.php" class="list-group-item list-group-item-action"><i class="fas fa-stethoscope me-2"></i> Data Pemeriksaan</a>
                    <a href="data-resep/resep.php" class="list-group-item list-group-item-action"><i class="fas fa-prescription-bottle-alt me-2"></i> Resep Obat</a>

                <?php elseif ($_SESSION["jabatan"] == 'pembayaran') : ?>
                    <a href="data-pembayaran/pembayaran.php" class="list-group-item list-group-item-action"><i class="fas fa-cash-register me-2"></i> Kasir Pembayaran</a>
                <?php endif; ?>

                <a href="login/logout.php" class="list-group-item list-group-item-action text-danger mt-4">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
            </div>
        </div>

        <div id="page-content-wrapper">
            
            <nav class="navbar navbar-expand-lg navbar-light bg-white py-3 px-4">
                <div class="d-flex align-items-center">
                    <button class="btn btn-outline-primary" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                    <h5 class="ms-3 mb-0 d-none d-md-block">Sistem Informasi Manajemen Klinik</h5>
                </div>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <div class="bg-primary text-white rounded-circle me-2 d-flex justify-content-center align-items-center" style="width: 35px; height: 35px;">
                                    <i class="fas fa-user"></i>
                                </div>
                                <span class="fw-bold text-dark"><?= ucfirst($_SESSION["user"] ?? "User"); ?></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end shadow border-0 animate__animated animate__fadeIn">
                                <a class="dropdown-item" href="#">Profil</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="login/logout.php">Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="container-fluid px-4 mt-4">
                
                <div class="alert alert-primary border-0 shadow-sm mb-4" role="alert">
                    <h4 class="alert-heading"><i class="fas fa-smile-beam me-2"></i>Selamat Datang, <?= ucfirst($_SESSION["jabatan"]); ?>!</h4>
                    <p class="mb-0">Anda telah login ke dalam sistem. Silakan pilih menu di samping untuk mengelola data klinik.</p>
                </div>

                <div class="row">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card card-stats bg-gradient-primary h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="small text-white-50 text-uppercase fw-bold mb-1">Total Pasien</div>
                                        <div class="h2 mb-0 fw-bold"><?= $jml_pasien; ?></div>
                                    </div>
                                    <i class="fas fa-users icon-bg"></i>
                                </div>
                            </div>
                            <a href="data-master/data-pasien/pasien.php" class="card-footer d-flex align-items-center justify-content-between text-white text-decoration-none bg-dark bg-opacity-10 small">
                                <span>Lihat Detail</span>
                                <i class="fas fa-angle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card card-stats bg-gradient-success h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="small text-white-50 text-uppercase fw-bold mb-1">Data Obat</div>
                                        <div class="h2 mb-0 fw-bold"><?= $jml_obat; ?></div>
                                    </div>
                                    <i class="fas fa-pills icon-bg"></i>
                                </div>
                            </div>
                            <a href="data-master/data-obat/obat.php" class="card-footer d-flex align-items-center justify-content-between text-white text-decoration-none bg-dark bg-opacity-10 small">
                                <span>Lihat Detail</span>
                                <i class="fas fa-angle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card card-stats bg-gradient-info h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="small text-white-50 text-uppercase fw-bold mb-1">Antrian Daftar</div>
                                        <div class="h2 mb-0 fw-bold"><?= $jml_daftar; ?></div>
                                    </div>
                                    <i class="fas fa-user-clock icon-bg"></i>
                                </div>
                            </div>
                            <a href="data-pendaftaran/pendaftaran.php" class="card-footer d-flex align-items-center justify-content-between text-white text-decoration-none bg-dark bg-opacity-10 small">
                                <span>Lihat Detail</span>
                                <i class="fas fa-angle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card card-stats bg-gradient-warning h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="small text-white-50 text-uppercase fw-bold mb-1">Belum Bayar</div>
                                        <div class="h2 mb-0 fw-bold"><?= $jml_bayar; ?></div>
                                    </div>
                                    <i class="fas fa-file-invoice-dollar icon-bg"></i>
                                </div>
                            </div>
                            <a href="data-pembayaran/pembayaran.php" class="card-footer d-flex align-items-center justify-content-between text-white text-decoration-none bg-dark bg-opacity-10 small">
                                <span>Lihat Detail</span>
                                <i class="fas fa-angle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Sidebar Script
        document.getElementById("sidebarToggle").addEventListener("click", function () {
            document.body.classList.toggle("sb-sidenav-toggled");
        });
    </script>
</body>

</html>
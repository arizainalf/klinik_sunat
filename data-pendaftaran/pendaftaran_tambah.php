<?php
session_start();
include '../koneksi.php';

// Validasi Akses
if (!isset($_SESSION["jabatan"])) {
    header("Location: ../login/index.php");
    exit();
}

// 1. LOGIK SIMPAN DATA (Ditaruh di atas)
if (isset($_POST['save'])) {
    $kd_pendaftaran = $_POST['kd_pendaftaran'];
    $id_pasien      = $_POST['id_pasien'];
    $id_paket       = $_POST['id_paket'];
    $tgl_pendaftaran= $_POST['tgl_pendaftaran'];
    $status         = 0; // Default: Belum diperiksa

    // Validasi input
    if (empty($id_pasien) || empty($id_paket)) {
        echo "<script>alert('Harap pilih Pasien dan Paket!');</script>";
    } else {
        // Prepared Statement Insert
        $stmt = $koneksi->prepare("INSERT INTO tb_pendaftaran (kd_pendaftaran, id_pasien, id_paket, tgl_pendaftaran, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisi", $kd_pendaftaran, $id_pasien, $id_paket, $tgl_pendaftaran, $status);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Pendaftaran Berhasil Disimpan!');
                    location='pendaftaran.php';
                  </script>";
        } else {
            echo "<script>alert('Gagal menyimpan data!');</script>";
        }
    }
}

// 2. GENERATE KODE OTOMATIS
$ambil = mysqli_query($koneksi, "SELECT kd_pendaftaran FROM tb_pendaftaran ORDER BY id_pendaftaran DESC LIMIT 1");
$data = $ambil->fetch_assoc();

if (!$data) {
    $kode_baru = "DTF-0001";
} else {
    $angka = (int) substr($data['kd_pendaftaran'], 4);
    $angka_baru = $angka + 1;
    $kode_baru = "DTF-" . str_pad($angka_baru, 4, '0', STR_PAD_LEFT);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Pendaftaran | Sistem Informasi Klinik</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
    body {
        font-family: 'Nunito', sans-serif;
        background-color: #f8f9fa;
        overflow-x: hidden;
    }

    /* Layout Sidebar (Sama seperti modul lain) */
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
                </div>
                <a href="pendaftaran.php" class="list-group-item list-group-item-action bg-primary text-white"><i
                        class="fas fa-clipboard-list me-2"></i> Pendaftaran</a>
                <a href="../data-pemeriksaan/pemeriksaan.php" class="list-group-item list-group-item-action"><i
                        class="fas fa-stethoscope me-2"></i> Pemeriksaan</a>
                <a href="../data-resep/resep.php" class="list-group-item list-group-item-action"><i
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
                    <h5 class="ms-3 mb-0 d-none d-md-block">Pendaftaran Baru</h5>
                </div>
            </nav>

            <div class="container-fluid px-4 mt-4">

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item"><a href="pendaftaran.php" class="text-decoration-none">Data
                                Pendaftaran</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tambah Baru</li>
                    </ol>
                </nav>

                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-file-medical me-2"></i>Form
                            Pendaftaran Pasien</h6>
                        <span class="badge bg-primary fs-6"><?= $kode_baru ?></span>
                    </div>

                    <div class="card-body p-4">
                        <form method="post">

                            <input type="hidden" name="kd_pendaftaran" value="<?= $kode_baru; ?>">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Pendaftaran</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                            <input type="date" class="form-control" name="tgl_pendaftaran"
                                                value="<?= date('Y-m-d'); ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Nama Pasien</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <select class="form-select" name="id_pasien" required>
                                                <option value="" disabled selected>-- Pilih Pasien --</option>
                                                <?php
                                                    $q_pasien = $koneksi->query("SELECT * FROM tb_pasien ORDER BY nm_pasien ASC");
                                                    while ($pasien = $q_pasien->fetch_assoc()) {
                                                        echo "<option value='{$pasien['id_pasien']}'>{$pasien['nm_pasien']}</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-text">
                                            Pasien belum terdaftar? <a
                                                href="../data-master/data-pasien/pasien_tambah.php"
                                                class="text-decoration-none">Tambah Pasien Baru</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Paket Layanan</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-box-open"></i></span>
                                            <select class="form-select" name="id_paket" required>
                                                <option value="" disabled selected>-- Pilih Paket --</option>
                                                <?php
                                                    $q_paket = $koneksi->query("SELECT * FROM tb_paket ORDER BY nm_paket ASC");
                                                    while ($paket = $q_paket->fetch_assoc()) {
                                                        echo "<option value='{$paket['id_paket']}'>{$paket['nm_paket']}</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-end gap-2">
                                <a href="pendaftaran.php" class="btn btn-secondary px-4">
                                    <i class="fas fa-arrow-left me-2"></i>Batal
                                </a>
                                <button type="submit" name="save" class="btn btn-success px-4">
                                    <i class="fas fa-save me-2"></i>Simpan Pendaftaran
                                </button>
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
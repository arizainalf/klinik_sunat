<?php
session_start();
include '../../koneksi.php';

// Cek Login
if (!isset($_SESSION["jabatan"])) {
    echo "<script>location='../../login/index.php'</script>";
    exit();
}

$id_obat = $_GET['id_obat'];

// PROSES UPDATE DATA (Diletakkan di atas agar bisa redirect setelah update)
if (isset($_POST['ubah'])) {
    $nm_obat    = $_POST['nm_obat'];
    $jenis_obat = $_POST['jenis_obat'];
    $stok       = $_POST['stok'];
    $harga_obat = $_POST['harga_obat'];
    $exp_obat   = $_POST['exp_obat'];
    $id_obat_post = $_POST['id_obat']; // Ambil ID dari hidden input/readonly

    // Menggunakan Prepared Statement untuk Update (Aman)
    $stmt_update = $koneksi->prepare("UPDATE tb_obat SET nm_obat=?, jenis_obat=?, stok=?, harga_obat=?, exp_obat=? WHERE id_obat=?");
    $stmt_update->bind_param("ssiiss", $nm_obat, $jenis_obat, $stok, $harga_obat, $exp_obat, $id_obat_post);

    if ($stmt_update->execute()) {
        echo "<script>
                alert('Data Obat Berhasil Diubah!');
                location='obat.php';
              </script>";
    } else {
        echo "<script>alert('Gagal mengubah data');</script>";
    }
}

// AMBIL DATA LAMA
$stmt = $koneksi->prepare("SELECT * FROM tb_obat WHERE id_obat = ?");
$stmt->bind_param("s", $id_obat);
$stmt->execute();
$result = $stmt->get_result();
$pecah = $result->fetch_assoc();

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
    <title>Ubah Data Obat | Sistem Informasi Klinik</title>

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

    /* Custom Form Input */
    .input-group-text {
        background-color: #f1f3f5;
        border-right: none;
    }

    .form-control,
    .form-select {
        border-left: none;
    }

    .form-control:focus,
    .form-select:focus {
        box-shadow: none;
        border-color: #ced4da;
    }

    .input-group:focus-within {
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, .25);
        border-radius: 0.375rem;
    }

    .input-group:focus-within .input-group-text,
    .input-group:focus-within .form-control,
    .input-group:focus-within .form-select {
        border-color: #86b7fe;
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
                    <h5 class="ms-3 mb-0 d-none d-md-block">Ubah Data Obat</h5>
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
                        <li class="breadcrumb-item active" aria-current="page">Ubah Obat</li>
                    </ol>
                </nav>

                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-edit me-2"></i>Form Edit Obat:
                            <?= $pecah['nm_obat']; ?></h6>
                    </div>

                    <div class="card-body p-4">
                        <form method="POST" enctype="multipart/form-data">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">ID Obat (Readonly)</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                                            <input type="text" class="form-control" name="id_obat"
                                                value="<?= $pecah['id_obat']; ?>" readonly
                                                style="background-color: #e9ecef;">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Kode Obat (Readonly)</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                            <input type="text" class="form-control" name="kd_obat"
                                                value="<?= $pecah['kd_obat']; ?>" readonly
                                                style="background-color: #e9ecef;">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Nama Obat</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-capsules"></i></span>
                                            <input type="text" class="form-control" name="nm_obat"
                                                value="<?= $pecah['nm_obat']; ?>" required
                                                placeholder="Masukkan Nama Obat">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Jenis Obat</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-tags"></i></span>
                                            <select class="form-select" name="jenis_obat" required>
                                                <option value="">-- Pilih Jenis --</option>
                                                <?php 
                                                $jenis = ["Pil", "Tablet", "Sirup", "Salep", "Kaplet", "Injeksi", "Puyer"];
                                                foreach ($jenis as $j) {
                                                    $selected = ($pecah['jenis_obat'] == $j) ? 'selected' : '';
                                                    echo "<option value='$j' $selected>$j</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Stok Obat</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-boxes"></i></span>
                                            <input type="number" class="form-control" name="stok"
                                                value="<?= $pecah['stok']; ?>" required min="0">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Harga Satuan (Rp)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control" name="harga_obat"
                                                value="<?= $pecah['harga_obat']; ?>" required min="0">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Kedaluwarsa</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                            <input type="date" class="form-control" name="exp_obat"
                                                value="<?= $pecah['exp_obat']; ?>" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-end gap-2">
                                <a href="obat.php" class="btn btn-secondary px-4">
                                    <i class="fas fa-arrow-left me-2"></i>Batal
                                </a>
                                <button type="submit" name="ubah" class="btn btn-success px-4">
                                    <i class="fas fa-save me-2"></i>Simpan Perubahan
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
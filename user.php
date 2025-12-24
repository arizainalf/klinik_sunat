<?php
session_start();
include 'koneksi.php';

// Validasi Akses (Hanya Admin)
if (!isset($_SESSION["jabatan"]) || $_SESSION["jabatan"] != 'admin') {
    header("Location: login/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data User | Sistem Informasi Klinik</title>

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
    </style>
</head>

<body>

    <div class="d-flex" id="wrapper">
        <div class="border-end" id="sidebar-wrapper">
            <div class="sidebar-heading"><i class="fas fa-clinic-medical me-2"></i>RUMAH SUNAT AZ-ZAINY</div>
            <div class="list-group list-group-flush mt-3">
                <a href="index.php" class="list-group-item list-group-item-action"><i
                        class="fas fa-tachometer-alt me-2"></i> Dashboard</a>

                <a href="#submenuMaster" class="list-group-item list-group-item-action" data-bs-toggle="collapse">
                    <i class="fas fa-database me-2"></i> Data Master <i class="fas fa-caret-down float-end"></i>
                </a>
                <div class="collapse bg-dark" id="submenuMaster">
                    <a href="data-master/data-pasien/pasien.php"
                        class="list-group-item list-group-item-action bg-dark text-white ps-5 small">Data Pasien</a>
                    <a href="data-master/data-dokter/dokter.php"
                        class="list-group-item list-group-item-action bg-dark text-white ps-5 small">Data Dokter</a>
                    <a href="data-master/data-obat/obat.php"
                        class="list-group-item list-group-item-action bg-dark text-white ps-5 small">Data Obat</a>
                    <a href="data-master/data-paket/paket.php"
                        class="list-group-item list-group-item-action bg-dark text-white ps-5 small">Data Paket</a>
                    <a href="data-master/data-tindakan/tindakan.php"
                        class="list-group-item list-group-item-action bg-dark text-white ps-5 small">Data Tindakan</a>
                </div>

                <a href="data-pendaftaran/pendaftaran.php" class="list-group-item list-group-item-action"><i
                        class="fas fa-clipboard-list me-2"></i> Pendaftaran</a>
                <a href="data-pemeriksaan/pemeriksaan.php" class="list-group-item list-group-item-action"><i
                        class="fas fa-stethoscope me-2"></i> Pemeriksaan</a>
                <a href="data-resep/resep.php" class="list-group-item list-group-item-action"><i
                        class="fas fa-prescription-bottle-alt me-2"></i> Resep Obat</a>
                <a href="data-pembayaran/pembayaran.php" class="list-group-item list-group-item-action"><i
                        class="fas fa-cash-register me-2"></i> Kasir Pembayaran</a>

                <a href="user.php" class="list-group-item list-group-item-action bg-primary text-white"><i
                        class="fas fa-users-cog me-2"></i> Data User</a>

                <a href="login/logout.php" class="list-group-item list-group-item-action text-danger mt-4"><i
                        class="fas fa-sign-out-alt me-2"></i> Logout</a>
            </div>
        </div>

        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-white py-3 px-4 shadow-sm">
                <div class="d-flex align-items-center">
                    <button class="btn btn-outline-primary" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                    <h5 class="ms-3 mb-0 d-none d-md-block">Data Pengguna Sistem</h5>
                </div>
            </nav>

            <div class="container-fluid px-4 mt-4">

                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-shield me-2"></i>Daftar User
                        </h6>
                        <a href="user_tambah.php" class="btn btn-success btn-sm shadow-sm">
                            <i class="fas fa-plus me-1"></i> Tambah User
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle" id="dataTable" width="100%">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%" class="text-center">No</th>
                                        <th>Username</th>
                                        <th>Jabatan (Role)</th>
                                        <th width="15%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $nomor = 1;
                                    $ambil = $koneksi->query("SELECT * FROM tb_user ORDER BY id_user DESC");
                                    while ($pecah = $ambil->fetch_assoc()) { 
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= $nomor++; ?></td>
                                        <td class="fw-bold text-primary"><?= $pecah['username']; ?></td>
                                        <td>
                                            <?php 
                                                $role = strtolower($pecah['jabatan']);
                                                $badge = 'secondary';
                                                if($role == 'admin') $badge = 'danger';
                                                elseif($role == 'pemeriksaan') $badge = 'info text-dark';
                                                elseif($role == 'pendaftaran') $badge = 'warning text-dark';
                                                elseif($role == 'pembayaran') $badge = 'success';
                                            ?>
                                            <span
                                                class="badge bg-<?= $badge ?> rounded-pill text-uppercase"><?= $pecah['jabatan']; ?></span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-danger btn-sm btn-hapus"
                                                data-id="<?= $pecah['id_user']; ?>"
                                                data-nama="<?= $pecah['username']; ?>" title="Hapus User">
                                                <i class="fas fa-trash"></i>
                                            </button>
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

    <div class="modal fade" id="modalHapus" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <i class="fas fa-exclamation-triangle text-danger fa-4x mb-3"></i>
                    <h5 class="mb-2">Hapus User?</h5>
                    <p class="text-muted small">Akun <strong id="hapusNama"></strong> akan dihapus permanen.</p>
                    <form action="user_hapus.php" method="GET">
                        <input type="hidden" name="id_user" id="hapusId">
                        <div class="d-flex justify-content-center gap-2 mt-4">
                            <button type="button" class="btn btn-secondary btn-sm"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger btn-sm">Ya, Hapus</button>
                        </div>
                    </form>
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
        $('#dataTable').DataTable();
    });

    // Modal Hapus Handler
    $(document).on('click', '.btn-hapus', function() {
        const id = $(this).data('id');
        const nama = $(this).data('nama');
        $('#hapusId').val(id);
        $('#hapusNama').text(nama);

        var myModal = new bootstrap.Modal(document.getElementById('modalHapus'));
        myModal.show();
    });
    </script>
</body>

</html>
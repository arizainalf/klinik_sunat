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
    <title>Data Pasien | Sistem Informasi Klinik</title>

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
                <a href="../../index.php" class="list-group-item list-group-item-action"><i
                        class="fas fa-tachometer-alt me-2"></i> Dashboard</a>

                <?php if ($_SESSION["jabatan"] == 'admin') : ?>
                <a href="#submenuMaster" class="list-group-item list-group-item-action" data-bs-toggle="collapse">
                    <i class="fas fa-database me-2"></i> Data Master <i class="fas fa-caret-down float-end"></i>
                </a>
                <div class="collapse show bg-dark" id="submenuMaster">
                    <a href="pasien.php"
                        class="list-group-item list-group-item-action bg-primary text-white ps-5 small">Data Pasien</a>
                    <a href="../data-obat/obat.php"
                        class="list-group-item list-group-item-action bg-dark text-white ps-5 small">Data Obat</a>
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
                        class="fas fa-cash-register me-2"></i> Kasir Pembayaran</a>
                <a href="../../user.php" class="list-group-item list-group-item-action"><i
                        class="fas fa-users-cog me-2"></i> Data User</a>

                <?php elseif ($_SESSION["jabatan"] == 'pendaftaran') : ?>
                <a href="../data-master/data-pasien/pasien.php" class="list-group-item list-group-item-action"><i
                        class="fas fa-user-alt me-2"></i> Data Pasien</a>
                <a href="pendaftaran.php" class="list-group-item list-group-item-action bg-primary text-white"><i
                        class="fas fa-clipboard-list me-2"></i> Data Pendaftaran</a>
                <?php endif; ?>

                <a href="../../login/logout.php" class="list-group-item list-group-item-action text-danger mt-4"><i
                        class="fas fa-sign-out-alt me-2"></i> Logout</a>
            </div>
        </div>

        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-white py-3 px-4 shadow-sm">
                <div class="d-flex align-items-center">
                    <button class="btn btn-outline-primary" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                    <h5 class="ms-3 mb-0 d-none d-md-block">Data Pasien</h5>
                </div>
            </nav>

            <div class="container-fluid px-4 mt-4">

                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-users me-2"></i>Daftar Pasien
                        </h6>
                        <button class="btn btn-success btn-sm shadow-sm" data-bs-toggle="modal"
                            data-bs-target="#modalTambah">
                            <i class="fas fa-plus me-1"></i> Tambah Pasien
                        </button>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle" id="dataTable" width="100%">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%" class="text-center">No</th>
                                        <th>Nama Pasien</th>
                                        <th>Ortu</th>
                                        <th>Tgl Lahir</th>
                                        <th>No. Telp</th>
                                        <th>Alamat</th>
                                        <th width="15%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $nomor = 1;
                                    $ambil = $koneksi->query("SELECT * FROM tb_pasien ORDER BY id_pasien DESC");
                                    while ($pecah = $ambil->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= $nomor++; ?></td>
                                        <td class="fw-bold"><?= $pecah['nm_pasien']; ?></td>
                                        <td><?= $pecah['nm_orangtua']; ?></td>
                                        <td><?= date('d-m-Y', strtotime($pecah['tgl_lahir'])); ?></td>
                                        <td><?= $pecah['no_telp']; ?></td>
                                        <td><?= $pecah['alamat']; ?></td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-warning btn-sm text-white btn-edit"
                                                    data-id="<?= $pecah['id_pasien']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <?php if ($_SESSION["jabatan"] == 'admin'): ?>
                                                <button class="btn btn-danger btn-sm btn-hapus"
                                                    data-id="<?= $pecah['id_pasien']; ?>"
                                                    data-nama="<?= $pecah['nm_pasien']; ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
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

    <div class="modal fade" id="modalTambah" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formTambah">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Tambah Pasien Baru</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Pasien</label>
                                <input type="text" name="nm_pasien" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Orang Tua</label>
                                <input type="text" name="nm_orangtua" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" name="tgl_lahir" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. Telepon</label>
                                <input type="number" name="no_telp" class="form-control" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Alamat Lengkap</label>
                                <textarea name="alamat" class="form-control" rows="2" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEdit" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formEdit">
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title"><i class="fas fa-user-edit me-2"></i>Edit Data Pasien</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_pasien" id="edit_id_pasien">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Pasien</label>
                                <input type="text" name="nm_pasien" id="edit_nm_pasien" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Orang Tua</label>
                                <input type="text" name="nm_orangtua" id="edit_nm_orangtua" class="form-control"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" name="tgl_lahir" id="edit_tgl_lahir" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. Telepon</label>
                                <input type="number" name="no_telp" id="edit_no_telp" class="form-control" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Alamat Lengkap</label>
                                <textarea name="alamat" id="edit_alamat" class="form-control" rows="2"
                                    required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning text-white">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalHapus" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <i class="fas fa-exclamation-triangle text-danger fa-4x mb-3"></i>
                    <h5 class="mb-2">Hapus Pasien?</h5>
                    <p class="text-muted small">Data: <strong id="hapusNama"></strong> akan dihapus permanen.</p>
                    <div class="d-flex justify-content-center gap-2 mt-4">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-danger btn-sm" id="btnHapusConfirm">Ya, Hapus</button>
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
        $('#dataTable').DataTable();
    });

    // ==========================
    // 1. AJAX TAMBAH DATA
    // ==========================
    $('#formTambah').submit(function(e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'pasien_tambah.php', // File Backend
            data: $(this).serialize(),
            success: function(res) {
                if (res.trim() === 'success') {
                    $('#modalTambah').modal('hide'); // Tutup Modal
                    alert('Pasien berhasil ditambahkan!');
                    location.reload(); // Refresh halaman
                } else {
                    alert('Gagal: ' + res);
                }
            }
        });
    });

    // ==========================
    // 2. AJAX GET DATA (UNTUK EDIT)
    // ==========================
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');

        // Ambil data dari server
        $.ajax({
            type: 'POST',
            url: 'pasien_get.php',
            data: {
                id_pasien: id
            },
            dataType: 'json',
            success: function(data) {
                // Isi form modal edit dengan data yang didapat
                $('#edit_id_pasien').val(data.id_pasien);
                $('#edit_nm_pasien').val(data.nm_pasien);
                $('#edit_nm_orangtua').val(data.nm_orangtua);
                $('#edit_tgl_lahir').val(data.tgl_lahir);
                $('#edit_no_telp').val(data.no_telp);
                $('#edit_alamat').val(data.alamat);

                // Tampilkan modal
                $('#modalEdit').modal('show');
            }
        });
    });

    // ==========================
    // 3. AJAX UPDATE DATA
    // ==========================
    $('#formEdit').submit(function(e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'pasien_update.php',
            data: $(this).serialize(),
            success: function(res) {
                if (res.trim() === 'success') {
                    $('#modalEdit').modal('hide');
                    alert('Data Pasien berhasil diupdate!');
                    location.reload();
                } else {
                    alert('Gagal update: ' + res);
                }
            }
        });
    });

    // ==========================
    // 4. AJAX HAPUS DATA
    // ==========================
    let idHapus = null;
    $(document).on('click', '.btn-hapus', function() {
        idHapus = $(this).data('id');
        $('#hapusNama').text($(this).data('nama'));
        $('#modalHapus').modal('show');
    });

    $('#btnHapusConfirm').click(function() {
        $.ajax({
            type: 'POST',
            url: 'pasien_hapus.php',
            data: {
                id_pasien: idHapus
            },
            success: function(res) {
                if (res.trim() === 'success') {
                    alert('Data berhasil dihapus');
                    location.reload();
                } else {
                    alert('Gagal: ' + res);
                }
            }
        });
    });
    </script>
</body>

</html>
<?php
session_start();
include '../koneksi.php';

// Validasi Akses
if (!isset($_SESSION["jabatan"])) {
    header("Location: ../login/index.php");
    exit();
}

// Generate Kode Pemeriksaan
$ambil = mysqli_query($koneksi, "SELECT kd_pemeriksaan FROM tb_pemeriksaan ORDER BY id_pemeriksaan DESC LIMIT 1");
$data = $ambil->fetch_assoc();

if (!$data) {
    $kode_baru = "PRK-0001";
} else {
    $angka = (int) substr($data['kd_pemeriksaan'], 4);
    $angka_baru = $angka + 1;
    $kode_baru = "PRK-" . str_pad($angka_baru, 4, '0', STR_PAD_LEFT);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Pemeriksaan | Sistem Informasi Klinik</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
    body {
        font-family: 'Nunito', sans-serif;
        background-color: #f8f9fa;
        overflow-x: hidden;
    }

    /* Layout Sidebar */
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
                <a href="pemeriksaan.php" class="list-group-item list-group-item-action bg-primary text-white"><i
                        class="fas fa-stethoscope me-2"></i> Pemeriksaan</a>
                <?php endif; ?>

                <a href="../login/logout.php" class="list-group-item list-group-item-action text-danger mt-4"><i
                        class="fas fa-sign-out-alt me-2"></i> Logout</a>
            </div>
        </div>

        <div id="page-content-wrapper">

            <nav class="navbar navbar-expand-lg navbar-light bg-white py-3 px-4 shadow-sm">
                <div class="d-flex align-items-center">
                    <button class="btn btn-outline-primary" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                    <h5 class="ms-3 mb-0 d-none d-md-block">Form Pemeriksaan</h5>
                </div>
            </nav>

            <div class="container-fluid px-4 mt-4">

                <form id="savePemeriksaan" method="post" action="pemeriksaan_store.php">
                    <input type="hidden" name="tgl_pemeriksaan" value="<?= date('Y-m-d'); ?>">

                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-md me-2"></i>Pemeriksaan
                                Baru</h6>
                            <span class="badge bg-primary fs-6"><?= $kode_baru ?></span>
                            <input type="hidden" name="kd_pemeriksaan" value="<?= $kode_baru ?>">
                        </div>

                        <div class="card-body p-4">

                            <h6 class="text-uppercase text-muted fw-bold small mb-3">Data Pendaftaran</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Pilih Pasien (Antrian)</label>
                                    <select class="form-select" name="id_pendaftaran" id="selectPasien" required>
                                        <option value="" disabled selected>-- Pilih Pasien --</option>
                                        <?php
                                            $q_pasien = $koneksi->query("
                                                SELECT a.*, b.nm_pasien, p.nm_paket, p.hrg_min, p.hrg_max 
                                                FROM tb_pendaftaran a 
                                                JOIN tb_pasien b ON a.id_pasien = b.id_pasien 
                                                JOIN tb_paket p ON a.id_paket = p.id_paket 
                                                WHERE a.status = '0' 
                                                ORDER BY a.id_pendaftaran ASC
                                            ");
                                            
                                            // Array PHP -> JS
                                            $data_js = array();

                                            while ($row = $q_pasien->fetch_assoc()) {
                                                $data_js[$row['id_pendaftaran']] = $row;
                                                echo "<option value='{$row['id_pendaftaran']}'>{$row['nm_pasien']} ({$row['kd_pendaftaran']})</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Paket Layanan</label>
                                    <input type="text" class="form-control bg-light" id="view_nm_paket" readonly>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Harga Paket Final</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">Rp</span>
                                        <input type="text" class="form-control" id="view_hrg_paket" autocomplete="off"
                                            required>
                                    </div>
                                    <input type="hidden" name="hrg_paket" id="real_hrg_paket">
                                    <small id="infoRange" class="text-muted fst-italic"></small>
                                    <div id="errorHarga" class="invalid-feedback d-block"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Daftar</label>
                                    <input type="text" class="form-control bg-light" id="view_tgl_daftar" readonly>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-uppercase text-muted fw-bold small mb-0">Tindakan Tambahan (Opsional)
                                </h6>
                                <button type="button" id="btnTambahTindakan" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-plus me-1"></i> Tambah Tindakan
                                </button>
                            </div>

                            <div id="containerTindakan">
                            </div>

                            <div id="templateTindakan" class="d-none">
                                <div class="row item-tindakan mb-3 border rounded p-2 bg-light mx-0 position-relative">
                                    <button type="button"
                                        class="btn-close position-absolute top-0 end-0 m-2 btn-hapus-tindakan"
                                        aria-label="Close"></button>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label small">Nama Tindakan</label>
                                        <select class="form-select select-tindakan" name="id_tindakan[]" required>
                                            <option value="" disabled selected>-- Pilih --</option>
                                            <?php
                                                $q_tindakan = $koneksi->query("SELECT * FROM tb_tindakan_tambahan");
                                                while ($t = $q_tindakan->fetch_assoc()) {
                                                    echo "<option value='{$t['id_tindakan']}' data-min='{$t['hrg_min']}' data-max='{$t['hrg_max']}'>{$t['nm_tindakan']}</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label small">Biaya</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control view-hrg-tindakan"
                                                autocomplete="off">
                                        </div>
                                        <input type="hidden" name="hrg_tindakan[]" class="real-hrg-tindakan">
                                        <small class="text-muted fst-italic info-range-tindakan"></small>
                                        <div class="invalid-feedback d-block error-tindakan"></div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-end gap-2">
                                <a href="pemeriksaan.php" class="btn btn-secondary px-4">Batal</a>
                                <button type="submit" name="save" class="btn btn-success px-4">
                                    <i class="fas fa-save me-2"></i>Simpan Pemeriksaan
                                </button>
                            </div>

                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    // Data Pasien dari PHP
    const dataPasien = <?php echo json_encode($data_js); ?>;

    // Format Rupiah Helper
    function formatRupiah(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    $(document).ready(function() {

        // 1. Logic Pilih Pasien
        $('#selectPasien').on('change', function() {
            const id = $(this).val();
            const data = dataPasien[id];

            if (data) {
                $('#view_nm_paket').val(data.nm_paket);
                $('#view_tgl_daftar').val(data.tgl_pendaftaran);

                const min = parseInt(data.hrg_min);
                const max = data.hrg_max ? parseInt(data.hrg_max) : null;

                // Set Data Min/Max ke element input agar bisa dibaca nanti
                $('#view_hrg_paket').data('min', min).data('max', max);

                // Auto isi harga jika fixed (max null)
                if (max === null || max === 0) {
                    $('#view_hrg_paket').val(formatRupiah(min));
                    $('#real_hrg_paket').val(min);
                    $('#infoRange').text('Harga paket tetap.');
                    $('#view_hrg_paket').prop('readonly', true).addClass('bg-light');
                } else {
                    $('#view_hrg_paket').val('');
                    $('#real_hrg_paket').val('');
                    $('#infoRange').text(`Range: Rp ${formatRupiah(min)} - Rp ${formatRupiah(max)}`);
                    $('#view_hrg_paket').prop('readonly', false).removeClass('bg-light').focus();
                }
            }
        });

        // 2. Validasi Input Harga Paket
        $('#view_hrg_paket').on('input', function() {
            let val = $(this).val().replace(/\./g, '');
            if (!isNaN(val) && val !== '') {
                let num = parseInt(val);
                $(this).val(formatRupiah(num));
                $('#real_hrg_paket').val(num);

                const min = $(this).data('min');
                const max = $(this).data('max');

                if (num < min) {
                    $('#errorHarga').text(`Minimal Rp ${formatRupiah(min)}`);
                } else if (max && num > max) {
                    $('#errorHarga').text(`Maksimal Rp ${formatRupiah(max)}`);
                } else {
                    $('#errorHarga').text('');
                }
            } else {
                $(this).val('');
                $('#real_hrg_paket').val('');
            }
        });

        // 3. Tambah Tindakan Dinamis
        $('#btnTambahTindakan').on('click', function() {
            // Clone template
            let content = $('#templateTindakan').html();
            $('#containerTindakan').append(content);
        });

        // 4. Hapus Baris Tindakan
        $(document).on('click', '.btn-hapus-tindakan', function() {
            $(this).closest('.item-tindakan').remove();
        });

        // 5. Logic Pilih Tindakan (Dropdown)
        $(document).on('change', '.select-tindakan', function() {
            const opt = $(this).find(':selected');
            const min = parseInt(opt.data('min'));
            const max = opt.data('max') ? parseInt(opt.data('max')) : null;
            const row = $(this).closest('.item-tindakan');
            const inputView = row.find('.view-hrg-tindakan');
            const inputReal = row.find('.real-hrg-tindakan');
            const info = row.find('.info-range-tindakan');

            // Simpan data min/max di input
            inputView.data('min', min).data('max', max);

            if (max === null || max === 0) {
                inputView.val(formatRupiah(min)).prop('readonly', true).addClass('bg-light');
                inputReal.val(min);
                info.text('Harga tetap.');
            } else {
                inputView.val('').prop('readonly', false).removeClass('bg-light').focus();
                inputReal.val('');
                info.text(`Range: Rp ${formatRupiah(min)} - Rp ${formatRupiah(max)}`);
            }
        });

        // 6. Validasi Input Harga Tindakan
        $(document).on('input', '.view-hrg-tindakan', function() {
            let val = $(this).val().replace(/\./g, '');
            const row = $(this).closest('.item-tindakan');
            const errorDiv = row.find('.error-tindakan');
            const inputReal = row.find('.real-hrg-tindakan');

            if (!isNaN(val) && val !== '') {
                let num = parseInt(val);
                $(this).val(formatRupiah(num));
                inputReal.val(num);

                const min = $(this).data('min');
                const max = $(this).data('max');

                if (num < min) {
                    errorDiv.text(`Minimal Rp ${formatRupiah(min)}`);
                } else if (max && num > max) {
                    errorDiv.text(`Maksimal Rp ${formatRupiah(max)}`);
                } else {
                    errorDiv.text('');
                }
            } else {
                $(this).val('');
                inputReal.val('');
            }
        });

        // 7. Validasi Submit Form
        $('#savePemeriksaan').on('submit', function(e) {
            // Cek error harga paket
            if ($('#errorHarga').text() !== '') {
                alert('Harga paket tidak valid!');
                e.preventDefault();
                return false;
            }

            // Cek error harga tindakan
            let validTindakan = true;
            $('.error-tindakan').each(function() {
                if ($(this).text() !== '') validTindakan = false;
            });

            if (!validTindakan) {
                alert('Salah satu harga tindakan tidak valid!');
                e.preventDefault();
                return false;
            }
        });

    });
    </script>

</body>

</html>
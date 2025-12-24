<?php
session_start();
include '../koneksi.php';

// Validasi Akses
if (!isset($_SESSION["jabatan"])) {
    header("Location: ../login/index.php");
    exit();
}

// ===============================
// 1. LOGIKA SIMPAN DATA (BACKEND)
// ===============================
if (isset($_POST['save'])) {
    mysqli_begin_transaction($koneksi);

    try {
        // INSERT HEADER RESEP
        $stmt = mysqli_prepare($koneksi, "INSERT INTO tb_resep (kd_resep, id_pemeriksaan, keterangan, total, status_rsp, tgl_resep) VALUES (?, ?, ?, 0, '0', ?)");
        mysqli_stmt_bind_param($stmt, "siss", $_POST['kd_resep'], $_POST['id_pemeriksaan'], $_POST['keterangan'], $_POST['tgl_resep']);
        mysqli_stmt_execute($stmt);
        $id_resep = mysqli_insert_id($koneksi);

        // INSERT DETAIL OBAT
        $total_obat = 0;
        $stmt2 = mysqli_prepare($koneksi, "INSERT INTO tb_detail_resep (id_resep, id_obat, jumlah_obat, subharga_obat) VALUES (?, ?, ?, ?)");
        $stmt3 = mysqli_prepare($koneksi, "UPDATE tb_obat SET stok = stok - ? WHERE id_obat = ?");

        foreach ($_POST['id_obat'] as $i => $id_obat) {
            $jumlah = (int) $_POST['jumlah_obat'][$i];
            $harga  = (int) str_replace('.', '', $_POST['harga_asli'][$i]); // Hapus titik format rupiah
            $sub    = $jumlah * $harga;
            $total_obat += $sub;

            // Simpan Detail
            mysqli_stmt_bind_param($stmt2, "iiii", $id_resep, $id_obat, $jumlah, $sub);
            mysqli_stmt_execute($stmt2);

            // Kurangi Stok Obat
            mysqli_stmt_bind_param($stmt3, "ii", $jumlah, $id_obat);
            mysqli_stmt_execute($stmt3);
        }

        // UPDATE TOTAL HARGA RESEP
        mysqli_query($koneksi, "UPDATE tb_resep SET total = $total_obat WHERE id_resep = $id_resep");

        // UPDATE STATUS PEMERIKSAAN (Sudah diresepkan)
        mysqli_query($koneksi, "UPDATE tb_pemeriksaan SET status_periksa = '1' WHERE id_pemeriksaan = $_POST[id_pemeriksaan]");

        mysqli_commit($koneksi);

        echo "<script>
                alert('Resep berhasil disimpan! Stok obat telah diperbarui.');
                location='resep.php'; 
              </script>"; // Bisa ganti location ke struk jika mau

    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        echo "<script>alert('Gagal menyimpan resep: " . $e->getMessage() . "');</script>";
    }
}

// GENERATE KODE RESEP
$ambil = mysqli_query($koneksi, "SELECT kd_resep FROM tb_resep ORDER BY id_resep DESC LIMIT 1");
$data = $ambil->fetch_assoc();
if (!$data) {
    $kode_baru = "RSP-0001";
} else {
    $angka = (int) substr($data['kd_resep'], 4);
    $angka_baru = $angka + 1;
    $kode_baru = "RSP-" . str_pad($angka_baru, 4, '0', STR_PAD_LEFT);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Resep | Sistem Informasi Klinik</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
    body {
        font-family: 'Nunito', sans-serif;
        background-color: #f8f9fa;
        overflow-x: hidden;
    }

    /* Sidebar Layout */
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
                    <h5 class="ms-3 mb-0 d-none d-md-block">Resep Obat</h5>
                </div>
            </nav>

            <div class="container-fluid px-4 mt-4">

                <form method="POST" id="formResep">
                    <input type="hidden" name="tgl_resep" value="<?= date('Y-m-d'); ?>">

                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary"><i
                                    class="fas fa-file-prescription me-2"></i>Form Resep Baru</h6>
                            <span class="badge bg-primary fs-6"><?= $kode_baru ?></span>
                            <input type="hidden" name="kd_resep" value="<?= $kode_baru ?>">
                        </div>

                        <div class="card-body p-4">

                            <h6 class="text-uppercase text-muted fw-bold small mb-3">Data Pemeriksaan</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Pilih Pasien (Belum Diresepkan)</label>
                                    <select class="form-select" name="id_pemeriksaan" id="selectPasien" required>
                                        <option value="" disabled selected>-- Pilih Pasien --</option>
                                        <?php
                                            $q_pasien = $koneksi->query("
                                                SELECT a.*, b.tgl_pendaftaran, c.nm_pasien 
                                                FROM tb_pemeriksaan a
                                                JOIN tb_pendaftaran b ON a.id_pendaftaran = b.id_pendaftaran
                                                JOIN tb_pasien c ON b.id_pasien = c.id_pasien
                                                WHERE a.status_periksa = '0'
                                            ");
                                            
                                            // Array JS
                                            $data_js = array();

                                            while ($row = $q_pasien->fetch_assoc()) {
                                                $data_js[$row['id_pemeriksaan']] = $row;
                                                echo "<option value='{$row['id_pemeriksaan']}'>{$row['nm_pasien']} (Kode: {$row['kd_pemeriksaan']})</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Tanggal Daftar</label>
                                    <input type="text" class="form-control bg-light" id="view_tgl_daftar" readonly>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Tanggal Periksa</label>
                                    <input type="text" class="form-control bg-light" id="view_tgl_periksa" readonly>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-uppercase text-muted fw-bold small mb-0">Rincian Obat</h6>
                                <button type="button" id="btnTambahObat" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-plus me-1"></i> Tambah Obat
                                </button>
                            </div>

                            <div id="containerObat">
                            </div>

                            <div id="templateObat" class="d-none">
                                <div class="row item-obat mb-3 border rounded p-2 bg-light mx-0 position-relative">
                                    <button type="button"
                                        class="btn-close position-absolute top-0 end-0 m-2 btn-hapus-obat"
                                        aria-label="Close"></button>

                                    <div class="col-md-4 mb-2">
                                        <label class="form-label small">Nama Obat</label>
                                        <select class="form-select select-obat" name="id_obat[]">
                                            <option value="" disabled selected>-- Pilih --</option>
                                            <?php
                                                $q_obat = $koneksi->query("SELECT * FROM tb_obat WHERE stok > 0");
                                                while ($o = $q_obat->fetch_assoc()) {
                                                    echo "<option value='{$o['id_obat']}' data-harga='{$o['harga_obat']}' data-stok='{$o['stok']}'>{$o['nm_obat']} (Stok: {$o['stok']})</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label small">Harga Satuan</label>
                                        <input type="text" class="form-control view-harga bg-light" readonly>
                                        <input type="hidden" name="harga_asli[]" class="real-harga">
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label class="form-label small">Qty</label>
                                        <input type="number" class="form-control input-qty" name="jumlah_obat[]" min="1"
                                            placeholder="0">
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label small">Subtotal</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text fw-bold">Rp</span>
                                            <input type="text"
                                                class="form-control view-subtotal bg-light fw-bold text-end" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <label class="form-label">Keterangan / Dosis</label>
                                <textarea name="keterangan" class="form-control" rows="3"
                                    placeholder="Contoh: 3x1 Sesudah Makan" required></textarea>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-end gap-2">
                                <a href="resep.php" class="btn btn-secondary px-4">Batal</a>
                                <button type="submit" name="save" class="btn btn-success px-4">
                                    <i class="fas fa-save me-2"></i>Simpan Resep
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

    function formatRupiah(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    $(document).ready(function() {

        // Toggle Sidebar
        document.getElementById("sidebarToggle").addEventListener("click", function() {
            document.body.classList.toggle("sb-sidenav-toggled");
        });

        // 1. Pilih Pasien
        $('#selectPasien').on('change', function() {
            const id = $(this).val();
            if (dataPasien[id]) {
                $('#view_tgl_daftar').val(dataPasien[id].tgl_pendaftaran);
                $('#view_tgl_periksa').val(dataPasien[id].tgl_pemeriksaan);
            }
        });

        // 2. Tambah Baris Obat
        $('#btnTambahObat').on('click', function() {
            let content = $('#templateObat').html();
            $('#containerObat').append(content);
        });

        // 3. Hapus Baris Obat
        $(document).on('click', '.btn-hapus-obat', function() {
            $(this).closest('.item-obat').remove();
        });

        // 4. Pilih Obat (Set Harga)
        $(document).on('change', '.select-obat', function() {
            const row = $(this).closest('.item-obat');
            const opt = $(this).find(':selected');
            const harga = parseInt(opt.data('harga'));
            const stok = parseInt(opt.data('stok'));

            row.find('.view-harga').val(formatRupiah(harga));
            row.find('.real-harga').val(harga);
            row.find('.input-qty').attr('max', stok).val('').focus(); // Reset qty & set max stok
            row.find('.view-subtotal').val('');
        });

        // 5. Hitung Subtotal
        $(document).on('input', '.input-qty', function() {
            const row = $(this).closest('.item-obat');
            const qty = parseInt($(this).val());
            const harga = parseInt(row.find('.real-harga').val());
            const maxStok = parseInt($(this).attr('max'));

            if (qty > maxStok) {
                alert('Stok tidak mencukupi! Sisa stok: ' + maxStok);
                $(this).val(maxStok);
                return;
            }

            if (!isNaN(qty) && !isNaN(harga)) {
                const sub = qty * harga;
                row.find('.view-subtotal').val(formatRupiah(sub));
            } else {
                row.find('.view-subtotal').val('');
            }
        });

        // Trigger Tambah Obat Pertama Kali
        $('#btnTambahObat').click();
    });
    </script>

</body>

</html>
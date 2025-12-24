<?php
session_start();
include '../koneksi.php';

// Validasi Login
if (!isset($_SESSION["jabatan"])) {
    header("Location: ../login/index.php");
    exit();
}

// Generate Kode Pembayaran Otomatis
$ambil = mysqli_query($koneksi, "SELECT kd_pembayaran FROM tb_pembayaran ORDER BY id_pembayaran DESC LIMIT 1");
$data = $ambil->fetch_assoc();

if (!$data) {
    $kode_baru = "TRA-0001";
} else {
    $angka_terakhir = (int) substr($data['kd_pembayaran'], 4);
    $angka_baru = $angka_terakhir + 1;
    $kode_baru = "TRA-" . str_pad($angka_baru, 4, '0', STR_PAD_LEFT);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transaksi Pembayaran | Sistem Informasi Klinik</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
    body {
        font-family: 'Nunito', sans-serif;
        background-color: #f8f9fa;
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

    /* Invoice Style */
    .bg-invoice {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
    }

    .invoice-header {
        border-bottom: 2px dashed #eee;
        padding-bottom: 15px;
        margin-bottom: 15px;
    }

    .invoice-total {
        background-color: #e8f4fd;
        padding: 15px;
        border-radius: 8px;
        color: #0d6efd;
        font-weight: bold;
        font-size: 1.2rem;
        text-align: right;
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
                <a href="pembayaran.php" class="list-group-item list-group-item-action bg-primary text-white"><i
                        class="fas fa-cash-register me-2"></i> Kasir Pembayaran</a>
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
                    <h5 class="ms-3 mb-0 d-none d-md-block">Transaksi Pembayaran</h5>
                </div>
            </nav>

            <div class="container-fluid px-4 mt-4">

                <form id="formBayar" method="POST" action="pembayaran_store.php">

                    <div class="row">
                        <div class="col-lg-5 mb-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white py-3">
                                    <h6 class="m-0 font-weight-bold text-primary"><i
                                            class="fas fa-money-bill-wave me-2"></i>Input Pembayaran</h6>
                                </div>
                                <div class="card-body">

                                    <div class="mb-3">
                                        <label class="form-label text-muted small fw-bold">KODE TRANSAKSI</label>
                                        <input type="text" class="form-control fw-bold bg-light" name="kd_pembayaran"
                                            value="<?= $kode_baru ?>" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label text-muted small fw-bold">PILIH PASIEN / RESEP</label>
                                        <select class="form-select" name="id_resep" id="pilihResep"
                                            onchange="dataResep(this.value)" required>
                                            <option value="" disabled selected>-- Cari Nama Pasien --</option>
                                            <?php
                                                // Query Resep yang Belum Dibayar (status_rsp = '0')
                                                $q = $koneksi->query("
                                                    SELECT 
                                                        a.id_resep, a.kd_resep, b.kd_pemeriksaan, b.id_pemeriksaan, d.nm_pasien,
                                                        (b.hrg_paket + IFNULL(SUM(dp.hrg_tindakan),0) + IFNULL(SUM(dr.subharga_obat),0)) AS total_bayar
                                                    FROM tb_resep a
                                                    JOIN tb_pemeriksaan b ON a.id_pemeriksaan = b.id_pemeriksaan
                                                    JOIN tb_pendaftaran c ON b.id_pendaftaran = c.id_pendaftaran
                                                    JOIN tb_pasien d ON c.id_pasien = d.id_pasien
                                                    LEFT JOIN tb_detail_pemeriksaan dp ON b.id_pemeriksaan = dp.id_pemeriksaan
                                                    LEFT JOIN tb_detail_resep dr ON a.id_resep = dr.id_resep
                                                    WHERE a.status_rsp = '0' 
                                                    GROUP BY a.id_resep
                                                ");

                                                // Siapkan Data JSON untuk JavaScript
                                                $js_array = array(); 
                                                
                                                while ($r = $q->fetch_assoc()) {
                                                    $js_array[$r['id_resep']] = $r;
                                            ?>
                                            <option value="<?= $r['id_resep']; ?>">
                                                <?= $r['nm_pasien']; ?> (<?= $r['kd_resep']; ?>) - Rp
                                                <?= number_format($r['total_bayar'],0,',','.'); ?>
                                            </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <input type="hidden" name="id_pemeriksaan" id="id_pemeriksaan">
                                    <input type="hidden" id="kd_resep_hidden">
                                    <input type="hidden" id="kd_pemeriksaan_hidden">

                                    <hr>

                                    <div class="mb-3">
                                        <label class="form-label text-muted small fw-bold">TOTAL TAGIHAN</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light fw-bold">Rp</span>
                                            <input type="number"
                                                class="form-control form-control-lg fw-bold text-end text-primary"
                                                name="total_pembayaran" id="total" readonly value="0">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label text-muted small fw-bold">UANG DIBAYAR</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white">Rp</span>
                                            <input type="number" class="form-control form-control-lg text-end"
                                                name="jumlah_bayar" id="bayar" required placeholder="0">
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label text-muted small fw-bold">KEMBALIAN</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">Rp</span>
                                            <input type="text"
                                                class="form-control form-control-lg fw-bold text-end text-success"
                                                name="kembalian" id="kembalian" readonly value="0">
                                        </div>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="submit" name="save" class="btn btn-success btn-lg shadow-sm">
                                            <i class="fas fa-save me-2"></i>PROSES PEMBAYARAN
                                        </button>
                                        <a href="pembayaran.php" class="btn btn-light text-muted">Batal</a>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="col-lg-7">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white py-3">
                                    <h6 class="m-0 font-weight-bold text-secondary"><i
                                            class="fas fa-file-invoice me-2"></i>Rincian Tagihan</h6>
                                </div>
                                <div class="card-body">

                                    <div id="loading" class="text-center py-5 d-none">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2 text-muted">Mengambil data tagihan...</p>
                                    </div>

                                    <div id="invoiceContent" class="d-none">
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <small class="text-muted text-uppercase">Paket Layanan</small>
                                                <h6 class="fw-bold" id="nm_paket">-</h6>
                                            </div>
                                            <div class="col-6 text-end">
                                                <small class="text-muted">Biaya Paket</small>
                                                <h6 class="fw-bold" id="hrg_paket">Rp 0</h6>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-8">
                                                <small class="text-muted text-uppercase">Tindakan Tambahan</small>
                                                <h6 class="fw-bold small" id="nm_tindakan">-</h6>
                                            </div>
                                            <div class="col-4 text-end">
                                                <small class="text-muted">Biaya Tindakan</small>
                                                <h6 class="fw-bold" id="hrg_tindakan">Rp 0</h6>
                                            </div>
                                        </div>

                                        <hr class="border-dashed">

                                        <h6 class="small fw-bold text-uppercase text-muted mb-3">Rincian Obat</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-borderless table-striped">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th>Nama Obat</th>
                                                        <th class="text-center">Qty</th>
                                                        <th class="text-end">Harga</th>
                                                        <th class="text-end">Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="listObat">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div id="emptyState" class="text-center py-5 text-muted">
                                        <i class="fas fa-receipt fa-3x mb-3 text-light-gray"></i>
                                        <p>Silakan pilih pasien untuk melihat rincian tagihan.</p>
                                    </div>

                                </div>
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
    // Data Resep dari PHP ke JS
    const resepMap = <?php echo json_encode($js_array); ?>;

    function dataResep(id) {
        // Tampilkan Loading
        $('#emptyState').addClass('d-none');
        $('#invoiceContent').addClass('d-none');
        $('#loading').removeClass('d-none');

        // Set Value Form
        if (resepMap[id]) {
            $('#id_pemeriksaan').val(resepMap[id].id_pemeriksaan);
            $('#kd_resep_hidden').val(resepMap[id].kd_resep);
            $('#kd_pemeriksaan_hidden').val(resepMap[id].kd_pemeriksaan);
            $('#total').val(resepMap[id].total_bayar);

            // Reset Bayar & Kembalian
            $('#bayar').val('');
            $('#kembalian').val('0');

            // AJAX Fetch Detail
            fetch('ajax_get_obat.php?kd_pemeriksaan=' + resepMap[id].kd_pemeriksaan)
                .then(res => res.json())
                .then(res => {
                    // Hilangkan Loading
                    $('#loading').addClass('d-none');
                    $('#invoiceContent').removeClass('d-none');

                    // Isi Header Paket
                    document.getElementById('nm_paket').innerText = res.header?.nm_paket ?? '-';
                    document.getElementById('hrg_paket').innerText = 'Rp ' + Number(res.header?.hrg_paket ?? 0)
                        .toLocaleString('id-ID');

                    // Isi Header Tindakan
                    let totalTindakan = 0;
                    let namaTindakanArr = [];

                    if (res.tindakan && res.tindakan.length > 0) {
                        res.tindakan.forEach(t => {
                            totalTindakan += Number(t.hrg_tindakan);
                            namaTindakanArr.push(t.nm_tindakan);
                        });
                        document.getElementById('nm_tindakan').innerText = namaTindakanArr.join(', ');
                    } else {
                        document.getElementById('nm_tindakan').innerText = '-';
                    }

                    document.getElementById('hrg_tindakan').innerText = 'Rp ' + totalTindakan.toLocaleString(
                        'id-ID');

                    // Isi Tabel Obat
                    let html = '';
                    if (res.obat && res.obat.length > 0) {
                        res.obat.forEach(o => {
                            html += `
                                    <tr>
                                        <td>${o.nm_obat}</td>
                                        <td class="text-center">${o.jumlah_obat}</td>
                                        <td class="text-end">Rp ${Number(o.harga_obat).toLocaleString('id-ID')}</td>
                                        <td class="text-end fw-bold">Rp ${Number(o.subharga_obat).toLocaleString('id-ID')}</td>
                                    </tr>
                                `;
                        });
                    } else {
                        html = '<tr><td colspan="4" class="text-center text-muted">Tidak ada resep obat</td></tr>';
                    }
                    document.getElementById('listObat').innerHTML = html;
                })
                .catch(err => {
                    console.error(err);
                    alert("Gagal mengambil data detail.");
                    $('#loading').addClass('d-none');
                });
        }
    }

    // Hitung Kembalian Otomatis
    $('#bayar').on('input', function() {
        let total = parseInt($('#total').val() || 0);
        let bayar = parseInt($(this).val() || 0);
        let kembalian = bayar - total;

        if (kembalian < 0) {
            $('#kembalian').val("Kurang Rp " + Math.abs(kembalian).toLocaleString('id-ID'));
            $('#kembalian').removeClass('text-success').addClass('text-danger');
        } else {
            $('#kembalian').val("Rp " + kembalian.toLocaleString('id-ID'));
            $('#kembalian').removeClass('text-danger').addClass('text-success');
        }
    });

    // Validasi Submit
    $('#formBayar').on('submit', function(e) {
        let total = parseInt($('#total').val() || 0);
        let bayar = parseInt($('#bayar').val() || 0);

        if (bayar < total) {
            alert('Uang pembayaran kurang! Mohon cek kembali.');
            e.preventDefault();
        }
    });

    // Sidebar Toggle
    document.getElementById("sidebarToggle").addEventListener("click", function() {
        document.body.classList.toggle("sb-sidenav-toggled");
    });
    </script>

</body>

</html>
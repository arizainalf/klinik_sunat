<?php
    // Pastikan koneksi.php ada dan benar
    include("koneksi.php");

    // Mengambil data paket
    $pakets = mysqli_query($koneksi, "SELECT * FROM tb_paket ORDER BY id_paket DESC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pendaftaran Pasien</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
    body {
        background-color: #f0f2f5;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .card {
        border: none;
        border-radius: 15px;
    }

    .card-header {
        background: linear-gradient(45deg, #0d6efd, #0dcaf0);
        color: white;
        border-radius: 15px 15px 0 0 !important;
        padding: 20px;
    }

    .form-label {
        font-weight: 500;
        color: #555;
    }

    .input-group-text {
        background-color: #f8f9fa;
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
        background-color: #fff;
    }

    /* Efek fokus pada grup input */
    .input-group:focus-within {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        border-radius: 0.375rem;
    }

    .input-group:focus-within .form-control,
    .input-group:focus-within .input-group-text,
    .input-group:focus-within .form-select {
        border-color: #86b7fe;
    }
    </style>
</head>

<body>

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg">
                    <div class="card-header text-center">
                        <h3 class="mb-0"><i class="fas fa-hospital-user me-2"></i>RUMAH SUNAT AZ-ZAINY</h4>
                            <h4 class="mb-0"></i>Form Pendaftaran Pasien</h4>
                            <small>Silakan isi data diri dengan lengkap dan benar</small>
                    </div>

                    <div class="card-body p-4">
                        <form action="proses_pendaftaran.php" method="POST">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Pasien</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" name="nm_pasien" class="form-control"
                                            placeholder="Nama Lengkap Pasien" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Orang Tua</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user-friends"></i></span>
                                        <input type="text" name="nm_orangtua" class="form-control"
                                            placeholder="Nama Ayah/Ibu" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Lahir</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        <input type="date" name="tgl_lahir" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">No. Telepon / WA</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                                        <input type="number" name="no_telp" class="form-control"
                                            placeholder="08xxxxxxxxxx" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Alamat Lengkap</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    <textarea name="alamat" class="form-control" rows="2"
                                        placeholder="Jalan, RT/RW, Kelurahan, Kecamatan..." required></textarea>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Pilih Paket Layanan</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-notes-medical"></i></span>
                                        <select name="id_paket" class="form-select" required>
                                            <option value="" selected disabled>-- Pilih Paket --</option>
                                            <?php while($paket = mysqli_fetch_array($pakets)) { ?>
                                            <option value="<?= $paket['id_paket'] ?>">
                                                <?= $paket['nm_paket'] ?>
                                            </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Pendaftaran</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-calendar-check"></i></span>
                                        <input type="date" name="tgl_pendaftaran" class="form-control"
                                            value="<?= date('Y-m-d') ?>" readonly>
                                    </div>
                                    <small class="text-muted">*Otomatis terisi hari ini</small>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <button type="reset" class="btn btn-outline-secondary me-md-2">
                                    <i class="fas fa-undo me-1"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-1"></i> Simpan Pendaftaran
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

                <div class="text-center mt-3 text-muted">
                    <small>RUMAH SUNAT AZ-ZAINY</small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
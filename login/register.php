<?php
include "../koneksi.php";

$error_msg = "";
$success_msg = "";

if (isset($_POST['register'])) {
    $nama     = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $jabatan  = $_POST['jabatan'];

    // Cek apakah username sudah ada
    $cek = $koneksi->prepare("SELECT username FROM tb_user WHERE username = ?");
    $cek->bind_param("s", $username);
    $cek->execute();
    $result = $cek->get_result();

    if ($result->num_rows > 0) {
        $error_msg = "Username sudah digunakan, silakan pilih yang lain.";
    } else {
        // Insert Data User Baru
        $stmt = $koneksi->prepare("INSERT INTO tb_user (nama_user, username, password, jabatan) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nama, $username, $password, $jabatan);

        if ($stmt->execute()) {
            $success_msg = "Registrasi berhasil! Silakan login.";
        } else {
            $error_msg = "Terjadi kesalahan saat mendaftar.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun | Sistem Informasi Klinik</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
    body {
        background: linear-gradient(135deg, #0061f2 0%, #6900f2 100%);
        /* Ganti height jadi min-height agar bisa discroll */
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        /* Tambahkan padding vertical agar tidak mepet atas-bawah saat layar kecil */
        padding: 40px 0;
    }

    .card-register {
        border: none;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        width: 100%;
        max-width: 500px;
        /* Pastikan card tidak menempel ke tepi layar di HP */
        margin: 0 15px;
    }

    .card-header {
        background: #fff;
        border-bottom: none;
        text-align: center;
        padding-top: 30px;
    }

    .logo-icon {
        font-size: 40px;
        color: #0061f2;
        margin-bottom: 10px;
    }

    .card-body {
        padding: 40px;
        background: #fff;
    }

    .form-control,
    .form-select {
        border-radius: 10px;
        padding: 12px;
        border: 1px solid #e0e0e0;
        background-color: #f8f9fa;
    }

    .form-control:focus,
    .form-select:focus {
        box-shadow: none;
        border-color: #0061f2;
        background-color: #fff;
    }

    .btn-register {
        background: linear-gradient(to right, #0061f2, #6900f2);
        border: none;
        border-radius: 10px;
        padding: 12px;
        color: white;
        font-weight: 600;
        width: 100%;
        transition: all 0.3s;
    }

    .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 97, 242, 0.4);
        color: white;
    }

    .footer-link {
        text-align: center;
        margin-top: 20px;
        font-size: 14px;
    }

    .footer-link a {
        color: #0061f2;
        text-decoration: none;
        font-weight: 600;
    }

    .input-group-text {
        background: none;
        border: none;
        position: absolute;
        right: 15px;
        top: 12px;
        z-index: 10;
        cursor: pointer;
        color: #aaa;
    }

    .form-group {
        position: relative;
        margin-bottom: 20px;
    }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card card-register">
                    <div class="card-header">
                        <div class="logo-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h4>Pendaftaran Akun</h4>
                        <p class="text-muted small">Lengkapi data untuk membuat akun baru</p>
                    </div>

                    <div class="card-body pt-0">

                        <?php if(!empty($error_msg)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> <?= $error_msg; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        <?php if(!empty($success_msg)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i> <?= $success_msg; ?>
                            <br><small><a href="index.php" class="alert-link">Klik di sini untuk Login</a></small>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        <form method="POST" action="">

                            <div class="form-group mb-3">
                                <label class="form-label text-muted small">Nama Lengkap</label>
                                <input type="text" class="form-control" name="nama" required
                                    placeholder="Contoh: Budi Santoso" autofocus>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label text-muted small">Username</label>
                                <input type="text" class="form-control" name="username" required
                                    placeholder="Username untuk login">
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label text-muted small">Password</label>
                                <div style="position: relative;">
                                    <input id="password" type="password" class="form-control" name="password" required
                                        placeholder="Buat password yang kuat">
                                    <span class="input-group-text" onclick="togglePassword()">
                                        <i class="fas fa-eye" id="toggleIcon"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group mb-4">
                                <label class="form-label text-muted small">Jabatan / Role</label>
                                <select class="form-select" name="jabatan" required>
                                    <option value="" disabled selected>-- Pilih Jabatan --</option>
                                    <option value="admin">Administrator</option>
                                    <option value="pendaftaran">Bagian Pendaftaran</option>
                                    <option value="pemeriksaan">Bagian Pemeriksaan (Dokter/Perawat)</option>
                                    <option value="pembayaran">Bagian Kasir / Pembayaran</option>
                                </select>
                            </div>

                            <div class="form-check mb-4">
                                <input type="checkbox" name="agree" id="agree" class="form-check-input" required>
                                <label for="agree" class="form-check-label small text-muted">
                                    Saya setuju dengan <a href="#">Syarat dan Ketentuan</a>
                                </label>
                            </div>

                            <button type="submit" name="register" class="btn btn-register mb-3">
                                DAFTAR SEKARANG
                            </button>

                            <div class="footer-link">
                                Sudah punya akun? <a href="index.php">Login di sini</a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function togglePassword() {
        var passwordInput = document.getElementById("password");
        var icon = document.getElementById("toggleIcon");
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            passwordInput.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }
    </script>
</body>

</html>
<?php
session_start();
include "../koneksi.php";

$error_msg = "";

if (isset($_POST['submit'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // MENGGUNAKAN PREPARED STATEMENT (Agar aman dari SQL Injection)
    $stmt = $koneksi->prepare("SELECT * FROM tb_user WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $user, $pass);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Set Session
        $_SESSION["jabatan"] = $row["jabatan"];
        $_SESSION["user"] = $row["username"];
        $_SESSION["login_status"] = true;

        // Redirect langsung menggunakan PHP
        header("Location: ../index.php");
        exit;
    } else {
        $error_msg = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Sistem Informasi Klinik</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
    body {
        background: linear-gradient(135deg, #0061f2 0%, #6900f2 100%);
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .card-login {
        border: none;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        width: 100%;
        max-width: 400px;
    }

    .card-header {
        background: #fff;
        border-bottom: none;
        text-align: center;
        padding-top: 40px;
    }

    .logo-icon {
        font-size: 50px;
        color: #0061f2;
        margin-bottom: 10px;
    }

    .card-body {
        padding: 40px;
        background: #fff;
    }

    .form-control {
        border-radius: 10px;
        padding: 12px;
        border: 1px solid #e0e0e0;
        background-color: #f8f9fa;
    }

    .form-control:focus {
        box-shadow: none;
        border-color: #0061f2;
        background-color: #fff;
    }

    .btn-login {
        background: linear-gradient(to right, #0061f2, #6900f2);
        border: none;
        border-radius: 10px;
        padding: 12px;
        color: white;
        font-weight: 600;
        width: 100%;
        transition: all 0.3s;
    }

    .btn-login:hover {
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
            <div class="col-md-5">
                <div class="card card-login">
                    <div class="card-header">
                        <div class="logo-icon">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <h4>Selamat Datang</h4>
                        <p class="text-muted">Silakan login ke akun Anda</p>
                    </div>

                    <div class="card-body">

                        <?php if(!empty($error_msg)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> <?= $error_msg; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="form-group mb-3">
                                <label class="form-label text-muted small">Username</label>
                                <input id="username" type="text" class="form-control" name="username" required autofocus
                                    placeholder="Masukkan username">
                            </div>

                            <div class="form-group mb-4">
                                <label class="form-label text-muted small">Password</label>
                                <div style="position: relative;">
                                    <input id="password" type="password" class="form-control" name="password" required
                                        placeholder="Masukkan password">
                                    <span class="input-group-text" onclick="togglePassword()">
                                        <i class="fas fa-eye" id="toggleIcon"></i>
                                    </span>
                                </div>
                            </div>

                            <button type="submit" name="submit" class="btn btn-login mb-3">
                                MASUK SEKARANG
                            </button>
                            <a href="../pendaftaran.php" name="submit" class="btn btn-login mb-3">
                                DAFTAR SUNAT
                            </a>

                            <div class="footer-link">
                                Belum punya akun? <a href="register.php">Daftar di sini</a>
                            </div>

                            <div class="text-center mt-4">
                                <small class="text-muted" style="font-size: 11px;">
                                    <div class="clinic-name"><i class="fas fa-clinic-medical me-2"></i>RUMAH SUNAT
                                        AZ-ZAINY</div>

                                </small>
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
<?php
session_start();
include '../koneksi.php';

if (! isset($_SESSION['jabatan'])) {
    header("Location: ../login/index.php");
    exit;
}

if (isset($_POST['save'])) {

    $kd_pemeriksaan = $_POST['kd_pemeriksaan'];
    $id_pendaftaran = $_POST['id_pendaftaran'];
    $hrg_paket      = (int) $_POST['hrg_paket'];
    $id_paket       = (int) $_POST['id_paket'];
    $tgl            = $_POST['tgl_pemeriksaan'];

    $id_tindakan  = $_POST['id_tindakan'];  // ARRAY
    $hrg_tindakan = $_POST['hrg_tindakan']; // ARRAY

    mysqli_begin_transaction($koneksi);

    try {

        // 1️⃣ Insert pemeriksaan
        $stmt = mysqli_prepare($koneksi, "
            INSERT INTO tb_pemeriksaan
            (kd_pemeriksaan, id_pendaftaran, hrg_paket, tgl_pemeriksaan, id_paket)
            VALUES (?, ?, ?, ?, ?)
        ");
        mysqli_stmt_bind_param($stmt, "siisi",
            $kd_pemeriksaan,
            $id_pendaftaran,
            $hrg_paket,
            $tgl,
            $id_paket
        );
        mysqli_stmt_execute($stmt);

        $id_pemeriksaan = mysqli_insert_id($koneksi);

        // 2️⃣ Insert semua tindakan
        $total_tindakan = 0;

        $stmt2 = mysqli_prepare($koneksi, "
            INSERT INTO tb_detail_pemeriksaan
            (id_pemeriksaan, id_tindakan, hrg_tindakan)
            VALUES (?, ?, ?)
        ");

        foreach ($id_tindakan as $i => $tindakan) {
            $harga = (int) $hrg_tindakan[$i];
            $total_tindakan += $harga;

            mysqli_stmt_bind_param($stmt2, "iii",
                $id_pemeriksaan,
                $tindakan,
                $harga
            );
            mysqli_stmt_execute($stmt2);
        }
        // 4️⃣ Update status pendaftaran
        mysqli_query($koneksi, "
            UPDATE tb_pendaftaran SET status = '1'
            WHERE id_pendaftaran = $id_pendaftaran
        ");

        mysqli_commit($koneksi);

        echo "<script>alert('Pemeriksaan berhasil disimpan');location='pemeriksaan.php';</script>";

    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        echo $e->getMessage();
        // echo "<script>alert('Gagal menyimpan data,". $e->getMessage() ."');history.back();</script>";
    }
}
<?php
session_start();
include '../../koneksi.php';

// Validasi Akses
if (!isset($_SESSION["jabatan"])) { echo "Akses ditolak"; exit(); }

if (isset($_POST['nm_pasien'])) {
    $nm_pasien   = $_POST['nm_pasien'];
    $nm_orangtua = $_POST['nm_orangtua'];
    $tgl_lahir   = $_POST['tgl_lahir'];
    $no_telp     = $_POST['no_telp'];
    $alamat      = $_POST['alamat'];

    // Prepared Statement
    $stmt = $koneksi->prepare("INSERT INTO tb_pasien (nm_pasien, nm_orangtua, tgl_lahir, no_telp, alamat) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nm_pasien, $nm_orangtua, $tgl_lahir, $no_telp, $alamat);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
<?php
session_start();
include '../../koneksi.php';

if (!isset($_SESSION["jabatan"])) { echo "Akses ditolak"; exit(); }

if (isset($_POST['id_pasien'])) {
    $id_pasien   = $_POST['id_pasien'];
    $nm_pasien   = $_POST['nm_pasien'];
    $nm_orangtua = $_POST['nm_orangtua'];
    $tgl_lahir   = $_POST['tgl_lahir'];
    $no_telp     = $_POST['no_telp'];
    $alamat      = $_POST['alamat'];

    // Prepared Statement Update
    $stmt = $koneksi->prepare("UPDATE tb_pasien SET nm_pasien=?, nm_orangtua=?, tgl_lahir=?, no_telp=?, alamat=? WHERE id_pasien=?");
    $stmt->bind_param("sssssi", $nm_pasien, $nm_orangtua, $tgl_lahir, $no_telp, $alamat, $id_pasien);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
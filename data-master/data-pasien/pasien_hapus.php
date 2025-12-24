<?php
session_start();
include '../../koneksi.php';

// Validasi Akses (Hanya Admin yang boleh menghapus)
if (!isset($_SESSION["jabatan"]) || $_SESSION["jabatan"] != 'admin') {
    echo "Akses ditolak";
    exit();
}

if (isset($_POST['id_pasien'])) {
    $id_pasien = $_POST['id_pasien'];

    // Prepared Statement
    $stmt = $koneksi->prepare("DELETE FROM tb_pasien WHERE id_pasien = ?");
    $stmt->bind_param("i", $id_pasien);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
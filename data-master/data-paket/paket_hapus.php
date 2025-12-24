<?php
session_start();
include '../../koneksi.php';

// Validasi Akses
if (!isset($_SESSION["jabatan"])) {
    echo "Akses ditolak";
    exit();
}

if (isset($_POST['id_paket'])) {
    $id_paket = $_POST['id_paket'];

    // Prepared Statement
    $stmt = $koneksi->prepare("DELETE FROM tb_paket WHERE id_paket = ?");
    $stmt->bind_param("i", $id_paket);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
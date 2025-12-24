<?php
session_start();
include '../../koneksi.php';

// Validasi Akses
if (!isset($_SESSION["jabatan"])) {
    echo "Akses ditolak";
    exit();
}

if (isset($_POST['id_tindakan'])) {
    $id_tindakan = $_POST['id_tindakan'];

    // Prepared Statement
    $stmt = $koneksi->prepare("DELETE FROM tb_tindakan_tambahan WHERE id_tindakan = ?");
    $stmt->bind_param("i", $id_tindakan);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
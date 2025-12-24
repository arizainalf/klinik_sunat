<?php
session_start();
include '../../koneksi.php';

// Validasi Akses
if (!isset($_SESSION["jabatan"])) {
    echo "Akses ditolak";
    exit();
}

if (isset($_POST['nm_paket'])) {
    $nm_paket = $_POST['nm_paket'];
    $hrg_min  = $_POST['hrg_min'];
    $hrg_max  = $_POST['hrg_max'];

    // Jika harga max kosong, set ke NULL atau 0
    $hrg_max = ($hrg_max === '') ? 0 : $hrg_max;

    // Prepared Statement
    $stmt = $koneksi->prepare("INSERT INTO tb_paket (nm_paket, hrg_min, hrg_max) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $nm_paket, $hrg_min, $hrg_max);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
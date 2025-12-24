<?php
session_start();
include '../../koneksi.php';

if (isset($_POST['id_pasien'])) {
    $id = $_POST['id_pasien'];
    
    // Ambil 1 data pasien
    $stmt = $koneksi->prepare("SELECT * FROM tb_pasien WHERE id_pasien = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    // Kirim balik dalam format JSON agar bisa dibaca JavaScript
    echo json_encode($data);
}
?>
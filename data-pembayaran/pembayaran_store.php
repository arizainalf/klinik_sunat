<?php
include '../koneksi.php';

$kd_pembayaran = $_POST['kd_pembayaran'];
$id_resep     = $_POST['id_resep'];
$id_pemeriksaan     = $_POST['id_pemeriksaan'];
$total_pembayaran     = $_POST['total_pembayaran'];
$jumlah_bayar     = $_POST['jumlah_bayar'];
$kembalian = $_POST['kembalian'];
$tgl_pembayaran = date('Y-m-d H:i:s');
$status_pembayaran = 1;

$updateResep = $koneksi->prepare("
    UPDATE tb_resep SET status_rsp = '1' WHERE id_resep = ?
");

$stmt = $koneksi->prepare(
    "INSERT INTO tb_pembayaran (kd_pembayaran, id_resep, id_pemeriksaan, total_pembayaran, jumlah_bayar, kembalian, tgl_pembayaran, status_pembayaran)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param("siiiiiss", $kd_pembayaran, $id_resep, $id_pemeriksaan, $total_pembayaran, $jumlah_bayar, $kembalian, $tgl_pembayaran, $status_pembayaran);

if ($stmt->execute()) {

    $updateResep->bind_param("i", $id_resep);
    $updateResep->execute();
    
    echo "<script>alert('Berhasil melakukan pembayaran');</script>";
    echo "<script>location='pembayaran.php';</script>";
} else {
    echo "<script>alert('Gagal melakukan pembayaran');</script>";
}
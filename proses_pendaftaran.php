<?php
include "koneksi.php";

// ambil data form
$nm_pasien      = $_POST['nm_pasien'];
$nm_orangtua    = $_POST['nm_orangtua'];
$tgl_lahir      = $_POST['tgl_lahir'];
$no_telp        = $_POST['no_telp'];
$alamat         = $_POST['alamat'];
$id_paket       = $_POST['id_paket'];
$tgl_daftar     = $_POST['tgl_pendaftaran'];


    $ambil     = mysqli_query($koneksi, "SELECT * FROM tb_pendaftaran ORDER BY id_pendaftaran DESC LIMIT 1");
    $data       = $ambil->fetch_assoc();
    if(!$data){
        $angka_baru = 1;
    } else {
        $angka      = explode('-', $data['kd_pendaftaran']);
        $angka_baru = (int) $angka[1] + 1;
    }
    $kode  = "DTF-" . str_pad($angka_baru, 4, '0', STR_PAD_LEFT);


// insert pasien
$insertPasien = $koneksi->query("
    INSERT INTO tb_pasien 
    (nm_pasien, nm_orangtua, tgl_lahir, no_telp, alamat)
    VALUES
    ('$nm_pasien','$nm_orangtua','$tgl_lahir','$no_telp','$alamat')
");

$id_pasien = $koneksi->insert_id;

// insert pendaftaran
$insertDaftar = $koneksi->query("
    INSERT INTO tb_pendaftaran
    (kd_pendaftaran, id_pasien, id_paket, status, tgl_pendaftaran)
    VALUES
    ('$kode','$id_pasien','$id_paket','0','$tgl_daftar')
");

if ($insertDaftar) {
    header("Location: struk_pendaftaran.php?kode=$kode");
} else {
    echo "Gagal menyimpan data";
}
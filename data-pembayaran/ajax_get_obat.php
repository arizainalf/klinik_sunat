<?php
include '../koneksi.php';

if (!isset($_GET['kd_pemeriksaan'])) {
    echo json_encode([]);
    exit;
}

$kd = $koneksi->real_escape_string($_GET['kd_pemeriksaan']);

/* ===============================
   1. HEADER + TINDAKAN
================================ */
$q = $koneksi->query("
    SELECT
        p.id_pemeriksaan,
        p.kd_pemeriksaan,
        pk.nm_paket,
        p.hrg_paket,
        dp.id_tindakan,
        dp.hrg_tindakan,
        t.nm_tindakan
    FROM tb_pemeriksaan p
    JOIN tb_paket pk ON p.id_paket = pk.id_paket
    LEFT JOIN tb_detail_pemeriksaan dp 
        ON p.id_pemeriksaan = dp.id_pemeriksaan
    LEFT JOIN tb_tindakan_tambahan t ON dp.id_tindakan = t.id_tindakan
    WHERE p.kd_pemeriksaan = '$kd'
");

$header   = null;
$tindakan = [];
$id_pemeriksaan = null;

while ($r = $q->fetch_assoc()) {
    if ($header === null) {
        $header = [
            'nm_paket'  => $r['nm_paket'],
            'hrg_paket' => $r['hrg_paket'],
        ];
        $id_pemeriksaan = $r['id_pemeriksaan'];
    }

    if ($r['id_tindakan']) {
        $tindakan[] = [
            'id_tindakan'  => $r['id_tindakan'],
            'nm_tindakan'  => $r['nm_tindakan'],
            'hrg_tindakan' => $r['hrg_tindakan'],
        ];
    }
}

/* ===============================
   2. OBAT (INI YANG KAMU TANYA)
   DISIMPAN KE VARIABEL $obat
================================ */
$obat = [];

if ($id_pemeriksaan) {
    $qo = $koneksi->query("
        SELECT 
            o.nm_obat,
            o.harga_obat,
            dr.jumlah_obat,
            dr.subharga_obat
        FROM tb_resep r
        JOIN tb_detail_resep dr ON r.id_resep = dr.id_resep
        JOIN tb_obat o ON dr.id_obat = o.id_obat
        WHERE r.id_pemeriksaan = '$id_pemeriksaan'
    ");

    while ($o = $qo->fetch_assoc()) {
        $obat[] = $o;
    }
}

/* ===============================
   3. RESPONSE JSON
================================ */
echo json_encode([
    'header'   => $header,
    'tindakan' => $tindakan,
    'obat'     => $obat   // 👈 OBAT DISIMPAN DI SINI
]);
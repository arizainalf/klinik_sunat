<?php
include '../koneksi.php';
header('Content-Type: application/json');

// ===============================
// 1. VALIDASI PARAMETER
// ===============================
if (!isset($_GET['id_pemeriksaan'])) {
    echo json_encode([
        'status' => false,
        'message' => 'id_pemeriksaan tidak dikirim'
    ]);
    exit;
}

$id_pemeriksaan = (int) $_GET['id_pemeriksaan'];


// ===============================
// 2. HEADER PEMERIKSAAN
// ===============================
$qHeader = $koneksi->query("
    SELECT 
        p.id_pemeriksaan,
        p.kd_pemeriksaan,
        pk.nm_paket,
        p.hrg_paket
    FROM tb_pemeriksaan p
    JOIN tb_paket pk 
        ON p.id_paket = pk.id_paket
    WHERE p.id_pemeriksaan = $id_pemeriksaan
");

$header = $qHeader->fetch_assoc();

if (!$header) {
    echo json_encode([
        'status' => false,
        'message' => 'Data pemeriksaan tidak ditemukan'
    ]);
    exit;
}


// ===============================
// 3. TINDAKAN TAMBAHAN
// ===============================
$tindakan = [];

$qTindakan = $koneksi->query("
    SELECT 
        dp.id_tindakan,
        t.nm_tindakan,
        dp.hrg_tindakan
    FROM tb_detail_pemeriksaan dp
    JOIN tb_tindakan_tambahan t 
        ON dp.id_tindakan = t.id_tindakan
    WHERE dp.id_pemeriksaan = $id_pemeriksaan
");

while ($row = $qTindakan->fetch_assoc()) {
    $tindakan[] = [
        'id_tindakan'  => $row['id_tindakan'],
        'nm_tindakan'  => $row['nm_tindakan'],
        'hrg_tindakan' => (int)$row['hrg_tindakan']
    ];
}


// ===============================
// 4. OBAT / RESEP
// ===============================
$obat = [];

$qObat = $koneksi->query("
    SELECT 
        o.nm_obat,
        o.harga_obat,
        dr.jumlah_obat,
        dr.subharga_obat
    FROM tb_resep r
    JOIN tb_detail_resep dr 
        ON r.id_resep = dr.id_resep
    JOIN tb_obat o 
        ON dr.id_obat = o.id_obat
    WHERE r.id_pemeriksaan = $id_pemeriksaan
");

while ($row = $qObat->fetch_assoc()) {
    $obat[] = [
        'nm_obat'        => $row['nm_obat'],
        'harga_obat'     => (int)$row['harga_obat'],
        'jumlah_obat'    => (int)$row['jumlah_obat'],
        'subharga_obat'  => (int)$row['subharga_obat']
    ];
}


// ===============================
// 5. RESPONSE JSON
// ===============================
echo json_encode([
    'status'   => true,
    'header'   => $header,
    'tindakan' => $tindakan,
    'obat'     => $obat
]);
<?php
include '../../koneksi.php';

$nm_tindakan = $_POST['nm_tindakan'];
$hrg_min     = $_POST['hrg_min'];
$hrg_max     = $_POST['hrg_max'];

$hrg_max = ($hrg_max === '') ? null : $hrg_max;

$stmt = $koneksi->prepare(
    "INSERT INTO tb_tindakan_tambahan (nm_tindakan, hrg_min, hrg_max)
     VALUES (?, ?, ?)"
);
$stmt->bind_param("sii", $nm_tindakan, $hrg_min, $hrg_max);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "error";
}
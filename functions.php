<?php
function formatRupiah($angka)
{
    // number_format(angka, jumlah_desimal, pemisah_desimal, pemisah_ribuan)
    $hasil = "Rp " . number_format($angka, 0, ',', '.');
    return $hasil;
}

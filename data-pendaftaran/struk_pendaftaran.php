<?php
    include "../koneksi.php";

    /* ===============================
   VALIDASI & QUERY AMAN
================================ */
    if (! isset($_GET['kd_pendaftaran'])) {
        die('Kode pendaftaran tidak ditemukan');
    }

    $kode = $_GET['kd_pendaftaran'];

    $stmt = $koneksi->prepare("
    SELECT tb_pendaftaran.kd_pendaftaran,
           tb_pendaftaran.tgl_pendaftaran,
           tb_paket.nm_paket
    FROM tb_pendaftaran
    JOIN tb_paket ON tb_pendaftaran.id_paket = tb_paket.id_paket
    WHERE tb_pendaftaran.kd_pendaftaran = ?
");

    $stmt->bind_param("s", $kode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die('Data pendaftaran tidak ditemukan');
    }

    $data = $result->fetch_assoc();

    /* ===============================
   FORMAT TANGGAL INDONESIA
================================ */
    function tgl_indo($tanggal)
    {
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ];

        $pecah = explode('-', $tanggal);
        return $pecah[2] . ' ' . $bulan[(int) $pecah[1]] . ' ' . $pecah[0];
    }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pendaftaran</title>
    <style>
        body {
            width: 250px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
        }
        img {
            width: 60px;
            display: block;
            margin: 0 auto 5px;
        }
        .center {
            text-align: center;
        }
        .title {
            font-size: 15px;
            font-weight: bold;
        }
        .line {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        .kode {
            font-size: 40px;
            font-weight: bold;
            margin: 5px 0;
        }
        .paket {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .footer {
            font-size: 10px;
            margin-top: 10px;
        }
    </style>
</head>

<body onload="window.print()">

    <img src="../assets/img/logo.png" alt="Logo">

    <div class="center">
        <div class="title">RUMAH SUNAT AZ-ZAINY</div>
    </div>

    <div class="line"></div>

    <div class="center">
        <div class="kode"><?php echo htmlspecialchars($data['kd_pendaftaran']); ?></div>
        <div class="paket"><?php echo htmlspecialchars($data['nm_paket']); ?></div>
        <div><?php echo tgl_indo($data['tgl_pendaftaran']); ?></div>
    </div>

    <div class="line"></div>

    <div class="center footer">
        Simpan struk ini<br>
        sebagai bukti pendaftaran
    </div>

</body>
</html>

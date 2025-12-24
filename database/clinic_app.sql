-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 24, 2025 at 01:04 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `clinic_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_detail_pemeriksaan`
--

CREATE TABLE `tb_detail_pemeriksaan` (
  `id_detail_pemeriksaan` int NOT NULL,
  `id_pemeriksaan` int NOT NULL,
  `id_tindakan` int NOT NULL,
  `hrg_tindakan` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tb_detail_pemeriksaan`
--

INSERT INTO `tb_detail_pemeriksaan` (`id_detail_pemeriksaan`, `id_pemeriksaan`, `id_tindakan`, `hrg_tindakan`) VALUES
(1, 2, 1, 750000),
(5, 8, 1, 666000);

-- --------------------------------------------------------

--
-- Table structure for table `tb_detail_resep`
--

CREATE TABLE `tb_detail_resep` (
  `id_detail_resep` int NOT NULL,
  `id_resep` int NOT NULL,
  `id_obat` int NOT NULL,
  `jumlah_obat` int UNSIGNED NOT NULL,
  `subharga_obat` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tb_detail_resep`
--

INSERT INTO `tb_detail_resep` (`id_detail_resep`, `id_resep`, `id_obat`, `jumlah_obat`, `subharga_obat`) VALUES
(3, 9, 3, 1, 230000),
(4, 9, 1, 2, 20000),
(5, 9, 2, 3, 30000);

-- --------------------------------------------------------

--
-- Table structure for table `tb_obat`
--

CREATE TABLE `tb_obat` (
  `id_obat` int NOT NULL,
  `kd_obat` varchar(10) NOT NULL,
  `nm_obat` varchar(30) NOT NULL,
  `jenis_obat` varchar(25) NOT NULL,
  `stok` int NOT NULL,
  `harga_obat` int NOT NULL,
  `exp_obat` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_obat`
--

INSERT INTO `tb_obat` (`id_obat`, `kd_obat`, `nm_obat`, `jenis_obat`, `stok`, `harga_obat`, `exp_obat`) VALUES
(1, 'OBT-1', 'Hemaviton+', 'Pil', 100, 10000, '2021-11-04'),
(2, 'OBT-2', 'Vitamin C', 'Tablet', 100, 10000, '2025-11-12'),
(3, 'OBT-3', 'Sulfasomidin', 'Kapsul', 93, 230000, '2024-10-22');

-- --------------------------------------------------------

--
-- Table structure for table `tb_paket`
--

CREATE TABLE `tb_paket` (
  `id_paket` int NOT NULL,
  `nm_paket` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hrg_min` int UNSIGNED NOT NULL,
  `hrg_max` int UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tb_paket`
--

INSERT INTO `tb_paket` (`id_paket`, `nm_paket`, `hrg_min`, `hrg_max`) VALUES
(1, 'Khitan Bayi (40 Hari - 1 Tahun)', 600000, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tb_pasien`
--

CREATE TABLE `tb_pasien` (
  `id_pasien` int NOT NULL,
  `nm_pasien` varchar(30) NOT NULL,
  `nm_orangtua` varchar(255) DEFAULT NULL,
  `tgl_lahir` date NOT NULL,
  `no_telp` varchar(15) NOT NULL,
  `alamat` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_pasien`
--

INSERT INTO `tb_pasien` (`id_pasien`, `nm_pasien`, `nm_orangtua`, `tgl_lahir`, `no_telp`, `alamat`) VALUES
(1, 'Dinda', NULL, '2021-11-04', '', 'Jl.Jalam'),
(2, 'Evina', NULL, '2021-11-19', '', 'Jl. Mawar'),
(3, 'Syawalia', NULL, '2021-11-19', '', 'Jl. Mawar M'),
(5, 'Daftar1', NULL, '2021-12-03', '', 'Daftar'),
(7, 'Pasien', NULL, '2021-12-23', '', 'Jl suhat'),
(9, 'Ria Sukma', 'j;lkgfjhg', '2021-12-24', '09065745354', 'adda'),
(12, 'fasdflksjdf', 'lkjfa;lksjdlfkj', '2002-02-20', '098798784', 'flaksjdlfjalksjdfasdf\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `tb_pembayaran`
--

CREATE TABLE `tb_pembayaran` (
  `id_pembayaran` int NOT NULL,
  `kd_pembayaran` varchar(10) NOT NULL,
  `id_resep` int NOT NULL,
  `id_pemeriksaan` int DEFAULT NULL,
  `total_pembayaran` int NOT NULL,
  `jumlah_bayar` int NOT NULL,
  `kembalian` int NOT NULL,
  `tgl_pembayaran` date NOT NULL,
  `status_pembayaran` enum('0','1') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_pembayaran`
--

INSERT INTO `tb_pembayaran` (`id_pembayaran`, `kd_pembayaran`, `id_resep`, `id_pemeriksaan`, `total_pembayaran`, `jumlah_bayar`, `kembalian`, `tgl_pembayaran`, `status_pembayaran`) VALUES
(9, 'TRA-0001', 9, 8, 2878000, 2900000, 22000, '2025-12-23', '1'),
(10, 'TRA-0002', 9, 8, 2878000, 9000000, 6122000, '2025-12-23', '1'),
(11, 'TRA-0003', 9, 8, 2878000, 10000000, 7122000, '2025-12-23', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tb_pemeriksaan`
--

CREATE TABLE `tb_pemeriksaan` (
  `id_pemeriksaan` int NOT NULL,
  `kd_pemeriksaan` varchar(10) NOT NULL,
  `id_pendaftaran` int NOT NULL,
  `id_paket` int DEFAULT NULL,
  `hrg_paket` int NOT NULL,
  `status_periksa` enum('0','1') NOT NULL,
  `tgl_pemeriksaan` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_pemeriksaan`
--

INSERT INTO `tb_pemeriksaan` (`id_pemeriksaan`, `kd_pemeriksaan`, `id_pendaftaran`, `id_paket`, `hrg_paket`, `status_periksa`, `tgl_pemeriksaan`) VALUES
(1, 'PRK-01', 1, 1, 0, '1', '2021-11-19'),
(2, 'PRK-02', 3, 1, 800000, '1', '2021-12-03'),
(3, 'PRK-03', 7, 1, 0, '1', '2021-12-23'),
(4, 'PRK-04', 9, 1, 0, '1', '2021-12-24'),
(8, 'PRK-0005', 10, 1, 600000, '1', '2025-12-23');

-- --------------------------------------------------------

--
-- Table structure for table `tb_pendaftaran`
--

CREATE TABLE `tb_pendaftaran` (
  `id_pendaftaran` int NOT NULL,
  `kd_pendaftaran` varchar(10) NOT NULL,
  `id_pasien` int NOT NULL,
  `id_paket` int DEFAULT NULL,
  `status` enum('0','1','2') NOT NULL,
  `tgl_pendaftaran` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_pendaftaran`
--

INSERT INTO `tb_pendaftaran` (`id_pendaftaran`, `kd_pendaftaran`, `id_pasien`, `id_paket`, `status`, `tgl_pendaftaran`) VALUES
(1, 'DTF-01', 1, 1, '1', '2021-11-19'),
(2, 'DTF-02', 2, 1, '2', '2021-11-19'),
(3, 'DTF-03', 5, 1, '1', '2021-12-03'),
(4, 'DTF-04', 2, 1, '2', '2021-12-03'),
(5, 'DTF-05', 3, 1, '2', '2021-12-22'),
(6, 'DTF-06', 3, 1, '2', '2021-12-23'),
(7, 'DTF-07', 7, 1, '1', '2021-12-23'),
(8, 'DTF-08', 9, 1, '2', '2021-12-24'),
(9, 'DTF-09', 9, 1, '1', '2021-12-24'),
(10, 'DTF-11', 1, 1, '1', '2025-12-23');

-- --------------------------------------------------------

--
-- Table structure for table `tb_resep`
--

CREATE TABLE `tb_resep` (
  `id_resep` int NOT NULL,
  `kd_resep` varchar(10) NOT NULL,
  `id_pemeriksaan` int NOT NULL,
  `keterangan` text NOT NULL,
  `total` int NOT NULL,
  `status_rsp` enum('0','1') NOT NULL,
  `tgl_resep` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_resep`
--

INSERT INTO `tb_resep` (`id_resep`, `kd_resep`, `id_pemeriksaan`, `keterangan`, `total`, `status_rsp`, `tgl_resep`) VALUES
(9, 'RSP-0001', 8, 'dfasdf', 280000, '1', '2025-12-23');

-- --------------------------------------------------------

--
-- Table structure for table `tb_tindakan_tambahan`
--

CREATE TABLE `tb_tindakan_tambahan` (
  `id_tindakan` int NOT NULL,
  `nm_tindakan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hrg_min` int NOT NULL,
  `hrg_max` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tb_tindakan_tambahan`
--

INSERT INTO `tb_tindakan_tambahan` (`id_tindakan`, `nm_tindakan`, `hrg_min`, `hrg_max`) VALUES
(1, 'Reposisi', 600000, 1000000),
(2, 'Cyste', 600000, 1000000),
(5, 'fasdf', 23, 4234);

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `id_user` int NOT NULL,
  `username` varchar(25) NOT NULL,
  `password` varchar(20) NOT NULL,
  `jabatan` enum('admin','pembayaran','pendaftaran','pemeriksaan') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`id_user`, `username`, `password`, `jabatan`) VALUES
(1, 'admin', 'admin', 'admin'),
(2, 'kasir', 'kasir', 'pembayaran'),
(3, 'pendaftaran', 'pendaftaran', 'pendaftaran'),
(4, 'pemeriksaan', 'pemeriksaan', 'pemeriksaan'),
(5, 'evina', 'evina', 'pembayaran');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_detail_pemeriksaan`
--
ALTER TABLE `tb_detail_pemeriksaan`
  ADD PRIMARY KEY (`id_detail_pemeriksaan`),
  ADD KEY `id_tindakan` (`id_tindakan`),
  ADD KEY `id_pemeriksaan` (`id_pemeriksaan`);

--
-- Indexes for table `tb_detail_resep`
--
ALTER TABLE `tb_detail_resep`
  ADD PRIMARY KEY (`id_detail_resep`),
  ADD KEY `id_resep` (`id_resep`),
  ADD KEY `id_obat` (`id_obat`);

--
-- Indexes for table `tb_obat`
--
ALTER TABLE `tb_obat`
  ADD PRIMARY KEY (`id_obat`);

--
-- Indexes for table `tb_paket`
--
ALTER TABLE `tb_paket`
  ADD PRIMARY KEY (`id_paket`);

--
-- Indexes for table `tb_pasien`
--
ALTER TABLE `tb_pasien`
  ADD PRIMARY KEY (`id_pasien`);

--
-- Indexes for table `tb_pembayaran`
--
ALTER TABLE `tb_pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_resep` (`id_resep`),
  ADD KEY `id_pemeriksaan` (`id_pemeriksaan`);

--
-- Indexes for table `tb_pemeriksaan`
--
ALTER TABLE `tb_pemeriksaan`
  ADD PRIMARY KEY (`id_pemeriksaan`),
  ADD KEY `id_pendaftaran` (`id_pendaftaran`),
  ADD KEY `id_paket` (`id_paket`);

--
-- Indexes for table `tb_pendaftaran`
--
ALTER TABLE `tb_pendaftaran`
  ADD PRIMARY KEY (`id_pendaftaran`),
  ADD KEY `id_pasien` (`id_pasien`),
  ADD KEY `tb_pendaftaran_ibfk_3` (`id_paket`);

--
-- Indexes for table `tb_resep`
--
ALTER TABLE `tb_resep`
  ADD PRIMARY KEY (`id_resep`),
  ADD KEY `id_pemeriksaan` (`id_pemeriksaan`);

--
-- Indexes for table `tb_tindakan_tambahan`
--
ALTER TABLE `tb_tindakan_tambahan`
  ADD PRIMARY KEY (`id_tindakan`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_detail_pemeriksaan`
--
ALTER TABLE `tb_detail_pemeriksaan`
  MODIFY `id_detail_pemeriksaan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tb_detail_resep`
--
ALTER TABLE `tb_detail_resep`
  MODIFY `id_detail_resep` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tb_obat`
--
ALTER TABLE `tb_obat`
  MODIFY `id_obat` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tb_paket`
--
ALTER TABLE `tb_paket`
  MODIFY `id_paket` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tb_pasien`
--
ALTER TABLE `tb_pasien`
  MODIFY `id_pasien` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tb_pembayaran`
--
ALTER TABLE `tb_pembayaran`
  MODIFY `id_pembayaran` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tb_pemeriksaan`
--
ALTER TABLE `tb_pemeriksaan`
  MODIFY `id_pemeriksaan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tb_pendaftaran`
--
ALTER TABLE `tb_pendaftaran`
  MODIFY `id_pendaftaran` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tb_resep`
--
ALTER TABLE `tb_resep`
  MODIFY `id_resep` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tb_tindakan_tambahan`
--
ALTER TABLE `tb_tindakan_tambahan`
  MODIFY `id_tindakan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_detail_pemeriksaan`
--
ALTER TABLE `tb_detail_pemeriksaan`
  ADD CONSTRAINT `tb_detail_pemeriksaan_ibfk_2` FOREIGN KEY (`id_tindakan`) REFERENCES `tb_tindakan_tambahan` (`id_tindakan`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `tb_detail_pemeriksaan_ibfk_3` FOREIGN KEY (`id_pemeriksaan`) REFERENCES `tb_pemeriksaan` (`id_pemeriksaan`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `tb_detail_resep`
--
ALTER TABLE `tb_detail_resep`
  ADD CONSTRAINT `tb_detail_resep_ibfk_1` FOREIGN KEY (`id_obat`) REFERENCES `tb_obat` (`id_obat`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `tb_detail_resep_ibfk_2` FOREIGN KEY (`id_resep`) REFERENCES `tb_resep` (`id_resep`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `tb_pembayaran`
--
ALTER TABLE `tb_pembayaran`
  ADD CONSTRAINT `tb_pembayaran_ibfk_1` FOREIGN KEY (`id_resep`) REFERENCES `tb_resep` (`id_resep`),
  ADD CONSTRAINT `tb_pembayaran_ibfk_2` FOREIGN KEY (`id_pemeriksaan`) REFERENCES `tb_pemeriksaan` (`id_pemeriksaan`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `tb_pemeriksaan`
--
ALTER TABLE `tb_pemeriksaan`
  ADD CONSTRAINT `tb_pemeriksaan_ibfk_1` FOREIGN KEY (`id_pendaftaran`) REFERENCES `tb_pendaftaran` (`id_pendaftaran`),
  ADD CONSTRAINT `tb_pemeriksaan_ibfk_2` FOREIGN KEY (`id_paket`) REFERENCES `tb_paket` (`id_paket`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `tb_pendaftaran`
--
ALTER TABLE `tb_pendaftaran`
  ADD CONSTRAINT `tb_pendaftaran_ibfk_1` FOREIGN KEY (`id_pasien`) REFERENCES `tb_pasien` (`id_pasien`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tb_pendaftaran_ibfk_2` FOREIGN KEY (`id_pasien`) REFERENCES `tb_pasien` (`id_pasien`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tb_pendaftaran_ibfk_3` FOREIGN KEY (`id_paket`) REFERENCES `tb_paket` (`id_paket`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_resep`
--
ALTER TABLE `tb_resep`
  ADD CONSTRAINT `tb_resep_ibfk_1` FOREIGN KEY (`id_pemeriksaan`) REFERENCES `tb_pemeriksaan` (`id_pemeriksaan`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 21 Bulan Mei 2025 pada 19.56
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_penyewaan_kamera`
--

DELIMITER $$
--
-- Prosedur
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cek_denda_keterlambatan` ()   BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_detail_sewa_id INT;
    DECLARE v_tanggal_kembali DATE;
    DECLARE v_hari_telat INT;
    DECLARE v_total_denda INT;

    -- Cursor ambil data dari view yang statusnya 'disewa'
    DECLARE cur CURSOR FOR 
        SELECT detail_sewa_id, tanggal_kembali 
        FROM view_kamera_sedang_disewa;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN cur;

    read_loop: LOOP
        FETCH cur INTO v_detail_sewa_id, v_tanggal_kembali;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Hitung keterlambatan
        SET v_hari_telat = DATEDIFF(CURDATE(), v_tanggal_kembali);

        -- Jika telat (lebih dari 0 hari)
        IF v_hari_telat > 0 THEN
            SET v_total_denda = v_hari_telat * 10000;

            -- Cek apakah sudah ada denda untuk detail_sewa_id ini
            IF NOT EXISTS (
                SELECT 1 FROM denda WHERE detail_sewa_id = v_detail_sewa_id
            ) THEN
                -- Insert denda baru
                INSERT INTO denda (detail_sewa_id, jenis_denda, jumlah_denda, keterangan)
                VALUES (v_detail_sewa_id, 'Keterlambatan', v_total_denda, CONCAT('Terlambat ', v_hari_telat, ' hari'));
            END IF;
        END IF;

    END LOOP;

    CLOSE cur;
    SELECT * FROM view_kamera_sedang_disewa;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_transaksi_by_user` (IN `in_penyewa_id` INT)   BEGIN
    SELECT 
        transaksi.*,
        detail_sewa.*,
        penyewa.*,
        kamera.*,
        denda.denda_id, denda.jenis_denda, denda.jumlah_denda, denda.keterangan
    FROM transaksi
    JOIN detail_sewa ON detail_sewa.detail_sewa_id = transaksi.detail_sewa_id
    JOIN penyewa ON penyewa.penyewa_id = detail_sewa.penyewa_id
    JOIN kamera ON kamera.kamera_id = detail_sewa.kamera_id
    LEFT JOIN denda ON detail_sewa.detail_sewa_id = denda.detail_sewa_id
    WHERE detail_sewa.penyewa_id = in_penyewa_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_search_kamera` (IN `in_keyword` VARCHAR(255))   BEGIN
    SELECT * FROM 
    kamera 
    LEFT JOIN spesifikasi ON kamera.kamera_id = spesifikasi.kamera_id
    WHERE
    merk LIKE CONCAT('%', in_keyword, '%') OR        
    harga_sewa_perhari LIKE CONCAT('%', in_keyword, '%') OR        
    stok LIKE CONCAT('%', in_keyword, '%') OR        
    resolusi LIKE CONCAT('%', in_keyword, '%') OR        
    sensor LIKE CONCAT('%', in_keyword, '%') OR        
    iso_max LIKE CONCAT('%', in_keyword, '%');
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_simpan_transaksi` (IN `p_penyewa_id` INT, IN `p_kamera_id` INT, IN `p_pinjam` DATE, IN `p_kembali` DATE, IN `p_jumlah` INT, IN `p_total` INT, IN `p_metode` VARCHAR(50))   BEGIN
    INSERT INTO detail_sewa (penyewa_id, kamera_id, tanggal_pinjam, tanggal_kembali, jumlah, status)
    VALUES (p_penyewa_id, p_kamera_id, p_pinjam, p_kembali, p_jumlah, 'disewa');

    SET @last_id = LAST_INSERT_ID();

    INSERT INTO transaksi (detail_sewa_id, total, metode_pembayaran)
    VALUES (@last_id, p_total, p_metode);
    
    IF (SELECT stok FROM kamera WHERE kamera_id = p_kamera_id) >= p_jumlah THEN
        UPDATE kamera 
        SET stok = stok - p_jumlah
        WHERE kamera_id = p_kamera_id;
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stok tidak mencukupi';
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_kamera` (IN `in_kamera_id` VARCHAR(255), IN `in_merk` VARCHAR(255), IN `in_harga_sewa_perhari` VARCHAR(255), IN `in_stok` VARCHAR(255), IN `in_gambar` VARCHAR(255), IN `in_resolusi` VARCHAR(255), IN `in_sensor` VARCHAR(255), IN `in_iso_max` VARCHAR(255))   BEGIN
    UPDATE kamera SET 
    merk = in_merk, 
    harga_sewa_perhari = in_harga_sewa_perhari, 
    stok = in_stok,
    gambar = in_gambar 
    WHERE kamera_id = in_kamera_id;

    UPDATE spesifikasi SET 
    resolusi = in_resolusi, 
    sensor = in_sensor, 
    iso_max = in_iso_max 
    WHERE kamera_id = in_kamera_id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `denda`
--

CREATE TABLE `denda` (
  `denda_id` int(11) NOT NULL,
  `detail_sewa_id` int(11) DEFAULT NULL,
  `jenis_denda` enum('terlambat','kerusakan') DEFAULT NULL,
  `jumlah_denda` decimal(10,2) DEFAULT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `denda`
--

INSERT INTO `denda` (`denda_id`, `detail_sewa_id`, `jenis_denda`, `jumlah_denda`, `keterangan`) VALUES
(1, 1, 'terlambat', 50000.00, 'Terlambat 1 hari'),
(2, 2, 'kerusakan', 100000.00, 'Lensa retak'),
(3, 3, 'terlambat', 30000.00, 'Terlambat beberapa jam'),
(4, 4, 'kerusakan', 75000.00, 'LCD rusak'),
(5, 5, 'terlambat', 40000.00, 'Terlambat 1 hari'),
(6, 6, 'terlambat', 120000.00, 'Bodi kamera lecet'),
(10, 8, 'terlambat', 10000.00, 'Terlambat 1 hari');

--
-- Trigger `denda`
--
DELIMITER $$
CREATE TRIGGER `trg_log_denda_update` AFTER UPDATE ON `denda` FOR EACH ROW BEGIN
    INSERT INTO log_denda_update VALUES 
    ('', OLD.denda_id, OLD.detail_sewa_id, OLD.jenis_denda, OLD.jumlah_denda, OLD.keterangan, NEW.jenis_denda, NEW.jumlah_denda, NEW.keterangan);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_sewa`
--

CREATE TABLE `detail_sewa` (
  `detail_sewa_id` int(11) NOT NULL,
  `penyewa_id` int(11) DEFAULT NULL,
  `kamera_id` int(11) DEFAULT NULL,
  `tanggal_pinjam` date DEFAULT NULL,
  `tanggal_kembali` date DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `status` enum('disewa','selesai') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `detail_sewa`
--

INSERT INTO `detail_sewa` (`detail_sewa_id`, `penyewa_id`, `kamera_id`, `tanggal_pinjam`, `tanggal_kembali`, `jumlah`, `status`) VALUES
(1, 1, 1, '2025-04-10', '2025-04-12', 1, 'selesai'),
(2, 2, 2, '2025-04-12', '2025-04-15', 1, 'selesai'),
(3, 3, 3, '2025-04-13', '2025-04-14', 2, 'selesai'),
(4, 4, 4, '2025-04-14', '2025-04-16', 1, 'selesai'),
(5, 5, 5, '2025-04-15', '2025-04-17', 1, 'selesai'),
(6, 6, 6, '2025-04-16', '2025-04-18', 1, 'selesai'),
(8, 10, 4, '2025-05-19', '2025-05-20', 1, 'disewa'),
(12, 10, 2, '2025-05-22', '2025-05-24', 1, 'selesai');

--
-- Trigger `detail_sewa`
--
DELIMITER $$
CREATE TRIGGER `trg_insert_transaksi` AFTER INSERT ON `detail_sewa` FOR EACH ROW BEGIN
    DECLARE v_kamera_id INT;
    DECLARE v_jumlah INT;

    SELECT kamera_id, jumlah 
    INTO v_kamera_id, v_jumlah
    FROM detail_sewa 
    WHERE detail_sewa_id = NEW.detail_sewa_id;

    UPDATE kamera 
    SET stok = stok - v_jumlah
    WHERE kamera_id = v_kamera_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_status_selesai` AFTER UPDATE ON `detail_sewa` FOR EACH ROW BEGIN
    DECLARE v_kamera_id INT;
    DECLARE v_jumlah INT;

    SELECT kamera_id, jumlah 
    INTO v_kamera_id, v_jumlah
    FROM detail_sewa 
    WHERE detail_sewa_id = NEW.detail_sewa_id;

    IF NEW.status = 'selesai' THEN
        UPDATE kamera 
        SET stok = stok + v_jumlah
        WHERE kamera_id = v_kamera_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kamera`
--

CREATE TABLE `kamera` (
  `kamera_id` int(11) NOT NULL,
  `merk` varchar(100) DEFAULT NULL,
  `harga_sewa_perhari` decimal(10,2) DEFAULT NULL,
  `stok` int(11) DEFAULT NULL,
  `gambar` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kamera`
--

INSERT INTO `kamera` (`kamera_id`, `merk`, `harga_sewa_perhari`, `stok`, `gambar`) VALUES
(1, 'Canon EOS 1500D', 150000.00, 3, 'Canon EOS 1500D.jpg'),
(2, 'Nikon D3500', 160000.00, 7, 'Nikon D3500.jpg'),
(3, 'Sony Alpha A6000', 180000.00, 3, 'Sony Alpha A6000.jpg'),
(4, 'Fujifilm X-T200', 175000.00, 3, 'Fujifilm X-T200.jpg'),
(5, 'Panasonic Lumix G7', 170000.00, 3, 'Panasonic Lumix G7.jpg'),
(6, 'Olympus OM-D E-M10', 165000.00, 4, 'Olympus OM-D E-M10.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `log_denda_update`
--

CREATE TABLE `log_denda_update` (
  `ldu_id` int(11) NOT NULL,
  `denda_id` int(11) NOT NULL,
  `detail_sewa_id` int(11) NOT NULL,
  `jenis_denda_old` enum('terlambat','kerusakan') NOT NULL,
  `jumlah_denda_old` decimal(10,2) NOT NULL,
  `keterangan_old` text NOT NULL,
  `jenis_denda_new` enum('terlambat','kerusakan') NOT NULL,
  `jumlah_denda_new` decimal(10,2) NOT NULL,
  `keterangan_new` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `log_denda_update`
--

INSERT INTO `log_denda_update` (`ldu_id`, `denda_id`, `detail_sewa_id`, `jenis_denda_old`, `jumlah_denda_old`, `keterangan_old`, `jenis_denda_new`, `jumlah_denda_new`, `keterangan_new`) VALUES
(0, 6, 6, 'terlambat', 120000.00, 'Bodi kamera lecet', 'kerusakan', 120000.00, 'Bodi kamera lecet'),
(0, 6, 6, 'kerusakan', 120000.00, 'Bodi kamera lecet', 'kerusakan', 120000.00, 'Bodi kamera lecet'),
(0, 6, 6, 'kerusakan', 120000.00, 'Bodi kamera lecet', 'kerusakan', 120000.00, 'Bodi kamera lecet'),
(0, 6, 6, 'kerusakan', 120000.00, 'Bodi kamera lecet', 'terlambat', 120000.00, 'Bodi kamera lecet'),
(0, 10, 8, '', 10000.00, 'Terlambat 1 hari', 'terlambat', 10000.00, 'Terlambat 1 hari'),
(0, 11, 12, '', 0.00, '', '', 0.00, ''),
(0, 11, 12, '', 0.00, '', '', 0.00, ''),
(0, 11, 12, '', 0.00, '', '', 0.00, '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `log_kamera_delete`
--

CREATE TABLE `log_kamera_delete` (
  `lkd_id` int(11) NOT NULL,
  `merk` varchar(255) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `stok` int(11) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `resolusi` varchar(50) NOT NULL,
  `sensor` varchar(50) NOT NULL,
  `iso_max` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `log_kamera_delete`
--

INSERT INTO `log_kamera_delete` (`lkd_id`, `merk`, `harga`, `stok`, `gambar`, `resolusi`, `sensor`, `iso_max`) VALUES
(2, 'assdd', 200.00, 12, '694886780_2.jpg', '123', '123', '123'),
(3, 'asd', 123.00, 123, '44535904_2.jpg', '123', '123', '123');

-- --------------------------------------------------------

--
-- Struktur dari tabel `penyewa`
--

CREATE TABLE `penyewa` (
  `penyewa_id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `no_tlp` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `upload_identitas` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `penyewa`
--

INSERT INTO `penyewa` (`penyewa_id`, `nama`, `no_tlp`, `email`, `password`, `upload_identitas`) VALUES
(1, 'Diah Ayu', '081234567890', 'diah@gmail.com', '123', 'ktp.webp'),
(2, 'Siti Mala', '082345678901', 'siti@gmail.com', '123', 'ktp.webp'),
(3, 'Ayu Nur', '083456789012', 'budi@gmail.com', '123', 'ktp.webp'),
(4, 'Nur Lestari', '084567890123', 'dewi@gmail.com', '123', 'ktp.webp'),
(5, 'Lia Amalia', '085678901234', 'agus@gmail.com', '123', 'ktp.webp'),
(6, 'Indah Permata', '086789012345', 'indah@gmail.com', '123', 'ktp.webp'),
(10, 'Nur Muhammad', '081234567890', 'nurm@gmail.com', '123', 'ktp.webp'),
(11, 'Saya', '081234567890', 'saya@gmail.com', '123', '1033905650_ktp.webp');

-- --------------------------------------------------------

--
-- Struktur dari tabel `spesifikasi`
--

CREATE TABLE `spesifikasi` (
  `spesifikasi_id` int(11) NOT NULL,
  `kamera_id` int(11) DEFAULT NULL,
  `resolusi` varchar(50) DEFAULT NULL,
  `sensor` varchar(50) DEFAULT NULL,
  `iso_max` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `spesifikasi`
--

INSERT INTO `spesifikasi` (`spesifikasi_id`, `kamera_id`, `resolusi`, `sensor`, `iso_max`) VALUES
(1, 1, '24.1MP', 'CMOS', '6400'),
(2, 2, '24.2MP', 'CMOS', '25600'),
(3, 3, '24.3MP', 'APS-C', '25600'),
(4, 4, '24.2MP', 'APS-C', '12800'),
(5, 5, '16MP', 'Live MOS', '25600'),
(6, 6, '16.1MP', 'Live MOS', '25600');

--
-- Trigger `spesifikasi`
--
DELIMITER $$
CREATE TRIGGER `trg_log_kamera_delete` AFTER DELETE ON `spesifikasi` FOR EACH ROW BEGIN
    DECLARE v_merk VARCHAR(100);
    DECLARE v_harga_sewa_perhari VARCHAR(100);
    DECLARE v_stok VARCHAR(100);
    DECLARE v_gambar VARCHAR(100);

    SELECT merk, harga_sewa_perhari, stok, gambar INTO v_merk, v_harga_sewa_perhari, v_stok, v_gambar FROM kamera WHERE kamera_id = OLD.kamera_id;
    DELETE FROM kamera WHERE kamera_id = OLD.kamera_id;

    INSERT INTO log_kamera_delete (merk, harga, stok, gambar, resolusi, sensor, iso_max)
    VALUES (v_merk, v_harga_sewa_perhari, v_stok, v_gambar, OLD.resolusi, OLD.sensor, OLD.iso_max);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `transaksi_id` int(11) NOT NULL,
  `detail_sewa_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `metode_pembayaran` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi`
--

INSERT INTO `transaksi` (`transaksi_id`, `detail_sewa_id`, `total`, `metode_pembayaran`) VALUES
(1, 1, 300000.00, 'Transfer'),
(2, 2, 480000.00, 'Cash'),
(3, 3, 360000.00, 'QRIS'),
(4, 4, 350000.00, 'Transfer'),
(5, 5, 340000.00, 'Cash'),
(6, 6, 330000.00, 'QRIS'),
(8, 8, 175000.00, 'COD'),
(11, 12, 320000.00, 'Transfer');

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `view_all_data_kamera`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `view_all_data_kamera` (
`kamera_id` int(11)
,`merk` varchar(100)
,`harga_sewa_perhari` decimal(10,2)
,`stok` int(11)
,`gambar` varchar(255)
,`resolusi` varchar(50)
,`sensor` varchar(50)
,`iso_max` varchar(50)
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `view_all_data_transaksi`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `view_all_data_transaksi` (
`transaksi_id` int(11)
,`total` decimal(10,2)
,`metode_pembayaran` varchar(50)
,`detail_sewa_id` int(11)
,`tanggal_pinjam` date
,`tanggal_kembali` date
,`jumlah` int(11)
,`status` enum('disewa','selesai')
,`penyewa_id` int(11)
,`nama` varchar(100)
,`no_tlp` varchar(20)
,`email` varchar(100)
,`password` varchar(255)
,`upload_identitas` varchar(255)
,`kamera_id` int(11)
,`merk` varchar(100)
,`harga_sewa_perhari` decimal(10,2)
,`stok` int(11)
,`gambar` varchar(255)
,`denda_id` int(11)
,`jenis_denda` enum('terlambat','kerusakan')
,`jumlah_denda` decimal(10,2)
,`keterangan` text
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `view_kamera_sedang_disewa`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `view_kamera_sedang_disewa` (
`transaksi_id` int(11)
,`total` decimal(10,2)
,`metode_pembayaran` varchar(50)
,`detail_sewa_id` int(11)
,`tanggal_pinjam` date
,`tanggal_kembali` date
,`jumlah` int(11)
,`status` enum('disewa','selesai')
,`penyewa_id` int(11)
,`nama` varchar(100)
,`no_tlp` varchar(20)
,`email` varchar(100)
,`password` varchar(255)
,`upload_identitas` varchar(255)
,`kamera_id` int(11)
,`merk` varchar(100)
,`harga_sewa_perhari` decimal(10,2)
,`stok` int(11)
,`gambar` varchar(255)
,`denda_id` int(11)
,`jenis_denda` enum('terlambat','kerusakan')
,`jumlah_denda` decimal(10,2)
,`keterangan` text
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `view_pendapatan_per_bulan`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `view_pendapatan_per_bulan` (
`bulan` varchar(7)
,`total_pendapatan` decimal(32,2)
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `view_total_sewa_per_kamera`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `view_total_sewa_per_kamera` (
`jumlah_sewa` bigint(21)
,`detail_sewa_id` int(11)
,`penyewa_id` int(11)
,`kamera_id` int(11)
,`tanggal_pinjam` date
,`tanggal_kembali` date
,`jumlah` int(11)
,`status` enum('disewa','selesai')
,`merk` varchar(100)
,`harga_sewa_perhari` decimal(10,2)
,`stok` int(11)
,`gambar` varchar(255)
);

-- --------------------------------------------------------

--
-- Struktur untuk view `view_all_data_kamera`
--
DROP TABLE IF EXISTS `view_all_data_kamera`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_all_data_kamera`  AS SELECT `k`.`kamera_id` AS `kamera_id`, `k`.`merk` AS `merk`, `k`.`harga_sewa_perhari` AS `harga_sewa_perhari`, `k`.`stok` AS `stok`, `k`.`gambar` AS `gambar`, `s`.`resolusi` AS `resolusi`, `s`.`sensor` AS `sensor`, `s`.`iso_max` AS `iso_max` FROM (`kamera` `k` left join `spesifikasi` `s` on(`k`.`kamera_id` = `s`.`kamera_id`)) ;

-- --------------------------------------------------------

--
-- Struktur untuk view `view_all_data_transaksi`
--
DROP TABLE IF EXISTS `view_all_data_transaksi`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_all_data_transaksi`  AS SELECT `t`.`transaksi_id` AS `transaksi_id`, `t`.`total` AS `total`, `t`.`metode_pembayaran` AS `metode_pembayaran`, `ds`.`detail_sewa_id` AS `detail_sewa_id`, `ds`.`tanggal_pinjam` AS `tanggal_pinjam`, `ds`.`tanggal_kembali` AS `tanggal_kembali`, `ds`.`jumlah` AS `jumlah`, `ds`.`status` AS `status`, `p`.`penyewa_id` AS `penyewa_id`, `p`.`nama` AS `nama`, `p`.`no_tlp` AS `no_tlp`, `p`.`email` AS `email`, `p`.`password` AS `password`, `p`.`upload_identitas` AS `upload_identitas`, `kamera`.`kamera_id` AS `kamera_id`, `kamera`.`merk` AS `merk`, `kamera`.`harga_sewa_perhari` AS `harga_sewa_perhari`, `kamera`.`stok` AS `stok`, `kamera`.`gambar` AS `gambar`, `denda`.`denda_id` AS `denda_id`, `denda`.`jenis_denda` AS `jenis_denda`, `denda`.`jumlah_denda` AS `jumlah_denda`, `denda`.`keterangan` AS `keterangan` FROM ((((`transaksi` `t` join `detail_sewa` `ds` on(`ds`.`detail_sewa_id` = `t`.`detail_sewa_id`)) join `penyewa` `p` on(`p`.`penyewa_id` = `ds`.`penyewa_id`)) join `kamera` on(`kamera`.`kamera_id` = `ds`.`kamera_id`)) left join `denda` on(`ds`.`detail_sewa_id` = `denda`.`detail_sewa_id`)) ;

-- --------------------------------------------------------

--
-- Struktur untuk view `view_kamera_sedang_disewa`
--
DROP TABLE IF EXISTS `view_kamera_sedang_disewa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_kamera_sedang_disewa`  AS SELECT `t`.`transaksi_id` AS `transaksi_id`, `t`.`total` AS `total`, `t`.`metode_pembayaran` AS `metode_pembayaran`, `ds`.`detail_sewa_id` AS `detail_sewa_id`, `ds`.`tanggal_pinjam` AS `tanggal_pinjam`, `ds`.`tanggal_kembali` AS `tanggal_kembali`, `ds`.`jumlah` AS `jumlah`, `ds`.`status` AS `status`, `p`.`penyewa_id` AS `penyewa_id`, `p`.`nama` AS `nama`, `p`.`no_tlp` AS `no_tlp`, `p`.`email` AS `email`, `p`.`password` AS `password`, `p`.`upload_identitas` AS `upload_identitas`, `kamera`.`kamera_id` AS `kamera_id`, `kamera`.`merk` AS `merk`, `kamera`.`harga_sewa_perhari` AS `harga_sewa_perhari`, `kamera`.`stok` AS `stok`, `kamera`.`gambar` AS `gambar`, `denda`.`denda_id` AS `denda_id`, `denda`.`jenis_denda` AS `jenis_denda`, `denda`.`jumlah_denda` AS `jumlah_denda`, `denda`.`keterangan` AS `keterangan` FROM ((((`transaksi` `t` join `detail_sewa` `ds` on(`ds`.`detail_sewa_id` = `t`.`detail_sewa_id`)) join `penyewa` `p` on(`p`.`penyewa_id` = `ds`.`penyewa_id`)) join `kamera` on(`kamera`.`kamera_id` = `ds`.`kamera_id`)) left join `denda` on(`ds`.`detail_sewa_id` = `denda`.`detail_sewa_id`)) WHERE `ds`.`status` = 'disewa' ;

-- --------------------------------------------------------

--
-- Struktur untuk view `view_pendapatan_per_bulan`
--
DROP TABLE IF EXISTS `view_pendapatan_per_bulan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_pendapatan_per_bulan`  AS SELECT date_format(`ds`.`tanggal_pinjam`,'%Y-%m') AS `bulan`, sum(`t`.`total`) AS `total_pendapatan` FROM (`transaksi` `t` join `detail_sewa` `ds` on(`ds`.`detail_sewa_id` = `t`.`detail_sewa_id`)) GROUP BY date_format(`ds`.`tanggal_pinjam`,'%Y-%m') ORDER BY date_format(`ds`.`tanggal_pinjam`,'%Y-%m') DESC ;

-- --------------------------------------------------------

--
-- Struktur untuk view `view_total_sewa_per_kamera`
--
DROP TABLE IF EXISTS `view_total_sewa_per_kamera`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_total_sewa_per_kamera`  AS SELECT count(`ds`.`kamera_id`) AS `jumlah_sewa`, `ds`.`detail_sewa_id` AS `detail_sewa_id`, `ds`.`penyewa_id` AS `penyewa_id`, `ds`.`kamera_id` AS `kamera_id`, `ds`.`tanggal_pinjam` AS `tanggal_pinjam`, `ds`.`tanggal_kembali` AS `tanggal_kembali`, `ds`.`jumlah` AS `jumlah`, `ds`.`status` AS `status`, `k`.`merk` AS `merk`, `k`.`harga_sewa_perhari` AS `harga_sewa_perhari`, `k`.`stok` AS `stok`, `k`.`gambar` AS `gambar` FROM (`detail_sewa` `ds` join `kamera` `k` on(`ds`.`kamera_id` = `k`.`kamera_id`)) GROUP BY `ds`.`kamera_id` ORDER BY count(`ds`.`kamera_id`) DESC ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `denda`
--
ALTER TABLE `denda`
  ADD PRIMARY KEY (`denda_id`),
  ADD KEY `detail_sewa_id` (`detail_sewa_id`);

--
-- Indeks untuk tabel `detail_sewa`
--
ALTER TABLE `detail_sewa`
  ADD PRIMARY KEY (`detail_sewa_id`),
  ADD KEY `penyewa_id` (`penyewa_id`),
  ADD KEY `kamera_id` (`kamera_id`);

--
-- Indeks untuk tabel `kamera`
--
ALTER TABLE `kamera`
  ADD PRIMARY KEY (`kamera_id`);

--
-- Indeks untuk tabel `log_kamera_delete`
--
ALTER TABLE `log_kamera_delete`
  ADD PRIMARY KEY (`lkd_id`);

--
-- Indeks untuk tabel `penyewa`
--
ALTER TABLE `penyewa`
  ADD PRIMARY KEY (`penyewa_id`);

--
-- Indeks untuk tabel `spesifikasi`
--
ALTER TABLE `spesifikasi`
  ADD PRIMARY KEY (`spesifikasi_id`),
  ADD KEY `kamera_id` (`kamera_id`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`transaksi_id`),
  ADD KEY `detail_sewa_id` (`detail_sewa_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `denda`
--
ALTER TABLE `denda`
  MODIFY `denda_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `detail_sewa`
--
ALTER TABLE `detail_sewa`
  MODIFY `detail_sewa_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `kamera`
--
ALTER TABLE `kamera`
  MODIFY `kamera_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `log_kamera_delete`
--
ALTER TABLE `log_kamera_delete`
  MODIFY `lkd_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `penyewa`
--
ALTER TABLE `penyewa`
  MODIFY `penyewa_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `spesifikasi`
--
ALTER TABLE `spesifikasi`
  MODIFY `spesifikasi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `transaksi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `denda`
--
ALTER TABLE `denda`
  ADD CONSTRAINT `denda_ibfk_1` FOREIGN KEY (`detail_sewa_id`) REFERENCES `detail_sewa` (`detail_sewa_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `detail_sewa`
--
ALTER TABLE `detail_sewa`
  ADD CONSTRAINT `detail_sewa_ibfk_1` FOREIGN KEY (`penyewa_id`) REFERENCES `penyewa` (`penyewa_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_sewa_ibfk_2` FOREIGN KEY (`kamera_id`) REFERENCES `kamera` (`kamera_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `spesifikasi`
--
ALTER TABLE `spesifikasi`
  ADD CONSTRAINT `spesifikasi_ibfk_1` FOREIGN KEY (`kamera_id`) REFERENCES `kamera` (`kamera_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`detail_sewa_id`) REFERENCES `detail_sewa` (`detail_sewa_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

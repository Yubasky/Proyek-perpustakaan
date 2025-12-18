-- phpMyAdmin SQL Dump
-- Fixed for Import
--
-- Host: localhost:3306
-- Generation Time: Dec 18, 2025 at 01:43 PM

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Disable foreign key checks to prevent errors during drop/create
SET FOREIGN_KEY_CHECKS = 0;

--
-- Database: `simbad_db`
--
CREATE DATABASE IF NOT EXISTS `simbad_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `simbad_db`;

-- --------------------------------------------------------

--
-- Drop existing tables to avoid "Table already exists" errors
--
DROP TABLE IF EXISTS `peminjaman`;
DROP TABLE IF EXISTS `buku`;
DROP TABLE IF EXISTS `pengguna`;
DROP TABLE IF EXISTS `admin`;
DROP TABLE IF EXISTS `kategori`;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_pengguna` varchar(50) NOT NULL,
  `kata_sandi` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `dibuat_pada` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama_pengguna` (`nama_pengguna`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `nama_pengguna`, `kata_sandi`, `nama_lengkap`, `dibuat_pada`) VALUES
(1, 'admin', 'admin123', 'Administrator Utama', '2025-12-18 11:58:46');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id`, `nama_kategori`) VALUES
(1, 'Fiksi'),
(2, 'Sains & Teknologi'),
(3, 'Sejarah'),
(4, 'Bisnis & Ekonomi'),
(5, 'Pengembangan Diri'),
(6, 'Komik');

-- --------------------------------------------------------

--
-- Table structure for table `buku`
--

CREATE TABLE `buku` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) NOT NULL,
  `penulis` varchar(100) NOT NULL,
  `penerbit` varchar(100) DEFAULT NULL,
  `tahun_terbit` year(4) DEFAULT NULL,
  `kategori_id` int(11) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `gambar_sampul` varchar(255) DEFAULT NULL,
  `dibuat_pada` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `kategori_id` (`kategori_id`),
  CONSTRAINT `fk_buku_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `buku`
--

INSERT INTO `buku` (`id`, `judul`, `penulis`, `penerbit`, `tahun_terbit`, `kategori_id`, `stok`, `gambar_sampul`, `dibuat_pada`) VALUES
(1, 'Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2005, 1, 5, '6943f6abbd29f.jpg', '2025-12-18 11:58:46'),
(2, 'Bumi Manusia', 'Pramoedya Ananta Toer', 'Hasta Mitra', 1980, 3, 3, '6943f5c98feba.jpg', '2025-12-18 11:58:46'),
(3, 'Filosofi Teras', 'Henry Manampiring', 'Kompas', 2018, 5, 10, '6943f59f8268f.jpg', '2025-12-18 11:58:46'),
(4, 'Atomic Habits', 'James Clear', 'Gramedia', 2019, 5, 8, '6943f5378242d.png', '2025-12-18 11:58:46'),
(5, 'Jujutsu Kaisen Vol.2', 'Gege Akutami', 'Shueisha', 2017, 6, 4, '6943f2ae0ecb3.jpg', '2025-12-18 12:25:18'),
(6, 'Sapiens Riwayat Singkat Umat Manusia', 'Yuval Noah Harari', 'KPG', 2017, 2, 10, '6943f9d26f888.jpg', '2025-12-18 12:55:46'),
(7, 'Psychology of Money', 'Morgan Housel', 'Bentang Pustaka', 2021, 4, 7, '6943fa5611a13.webp', '2025-12-18 12:57:58');

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_pengguna` varchar(50) NOT NULL,
  `kata_sandi` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `dibuat_pada` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama_pengguna` (`nama_pengguna`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id`, `nama_pengguna`, `kata_sandi`, `nama_lengkap`, `dibuat_pada`) VALUES
(1, 'budi', '$2y$10$bv1WMAaJVoLT3CX/tkrOxOQ7HgjIPc7Emv5jX94WHu56fdQ30qouG', 'Budi Santoso', '2025-12-18 11:58:46'),
(2, 'siti', '$2y$10$S.5CurD8H.1hJoZfHl8hAuy.U.10Ux7k7YHeppk1CyktMrnYBjY2.', 'Siti Aminah', '2025-12-18 11:58:46'),
(3, 'Yuba', '$2y$10$bg7W3gPkSpMlPMwCkWLFMuOt1oD9Tnlg9P2jFUXsQvXfGOlrz1lt6', 'Bayu Prayoga', '2025-12-18 12:01:07');

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pengguna_id` int(11) NOT NULL,
  `buku_id` int(11) NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali` date DEFAULT NULL,
  `status` enum('dipinjam','dikembalikan') NOT NULL DEFAULT 'dipinjam',
  PRIMARY KEY (`id`),
  KEY `pengguna_id` (`pengguna_id`),
  KEY `buku_id` (`buku_id`),
  CONSTRAINT `fk_peminjaman_pengguna` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_peminjaman_buku` FOREIGN KEY (`buku_id`) REFERENCES `buku` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`id`, `pengguna_id`, `buku_id`, `tanggal_pinjam`, `tanggal_kembali`, `status`) VALUES
(1, 3, 1, '2025-12-18', '2025-12-18', 'dikembalikan'),
(2, 1, 4, '2025-12-18', '2025-12-18', 'dikembalikan'),
(3, 3, 2, '2025-12-18', '2025-12-18', 'dikembalikan'),
(4, 2, 5, '2025-12-18', '2025-12-18', 'dikembalikan'),
(5, 3, 5, '2025-12-18', '2025-12-18', 'dikembalikan');

-- Enable foreign key checks back
SET FOREIGN_KEY_CHECKS = 1;

COMMIT;

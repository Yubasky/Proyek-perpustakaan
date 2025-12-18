-- Database for Simbad Library System (Separated Admin)
CREATE DATABASE IF NOT EXISTS `simbad_db`;
USE `simbad_db`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `kategori` (`id`, `nama_kategori`) VALUES
(1, 'Fiksi'),
(2, 'Sains & Teknologi'),
(3, 'Sejarah'),
(4, 'Bisnis & Ekonomi'),
(5, 'Pengembangan Diri'),
(6, 'Komik');

-- --------------------------------------------------------

--
-- Table structure for table `admin` (New Table)
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_pengguna` varchar(50) NOT NULL,
  `kata_sandi` varchar(255) NOT NULL, -- Plain text storage
  `nama_lengkap` varchar(100) NOT NULL,
  `dibuat_pada` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama_pengguna` (`nama_pengguna`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin`
-- Password is PLAIN TEXT 'admin123'
INSERT INTO `admin` (`nama_pengguna`, `kata_sandi`, `nama_lengkap`) VALUES
('admin', 'admin123', 'Administrator Utama');

-- --------------------------------------------------------

--
-- Table structure for table `pengguna` (Members Only)
--

CREATE TABLE `pengguna` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_pengguna` varchar(50) NOT NULL,
  `kata_sandi` varchar(255) NOT NULL, -- Still hashed for safety
  `nama_lengkap` varchar(100) NOT NULL,
  `dibuat_pada` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama_pengguna` (`nama_pengguna`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pengguna`
-- Password hashed for 'password123'
INSERT INTO `pengguna` (`nama_pengguna`, `kata_sandi`, `nama_lengkap`) VALUES
('budi', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Budi Santoso'),
('siti', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Siti Aminah');

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
  `dibuat_pada` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `kategori_id` (`kategori_id`),
  CONSTRAINT `fk_buku_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `buku` (`id`, `judul`, `penulis`, `kategori_id`, `stok`, `penerbit`, `tahun_terbit`, `gambar_sampul`) VALUES
(1, 'Laskar Pelangi', 'Andrea Hirata', 1, 5, 'Bentang Pustaka', 2005, NULL),
(2, 'Bumi Manusia', 'Pramoedya Ananta Toer', 3, 3, 'Hasta Mitra', 1980, NULL),
(3, 'Filosofi Teras', 'Henry Manampiring', 5, 10, 'Kompas', 2018, NULL),
(4, 'Atomic Habits', 'James Clear', 5, 8, 'Gramedia', 2019, NULL);

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

COMMIT;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 18, 2024 at 03:37 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_moora`
--

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('tr7SaK13jFasGO5sJLHZxsxzaaNQIep7yHEG1Eqa', 'U001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiYWtoZXBDMkp4U2tlVWRDMlozUHJZRmZRbEc5ZXpqUjZ2dlMwemlHTiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL0Rhc2hib2FyZCI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjMwOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvS3JpdGVyaWEiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7czo0OiJVMDAxIjt9', 1734493036);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_angkatan`
--

CREATE TABLE `tbl_angkatan` (
  `angkatan_id` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun_angkatan` year NOT NULL,
  `jurusan` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_angkatan`
--

INSERT INTO `tbl_angkatan` (`angkatan_id`, `tahun_angkatan`, `jurusan`) VALUES
('A001', '2018', 'Teknik Informatika'),
('A002', '2019', 'Teknik Informatika');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_dosen`
--

CREATE TABLE `tbl_dosen` (
  `dosen_id` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_dosen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_dosen`
--

INSERT INTO `tbl_dosen` (`dosen_id`, `nama_dosen`, `user_id`) VALUES
('D001', 'Evanita V Manullang', 'U001');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_hasil`
--

CREATE TABLE `tbl_hasil` (
  `hasil_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nilai_akhir` double NOT NULL,
  `peringkat` int NOT NULL,
  `mahasiswa_id` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_kriteria`
--

CREATE TABLE `tbl_kriteria` (
  `kriteria_id` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_kriteria` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bobot` int NOT NULL,
  `type` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_kriteria`
--

INSERT INTO `tbl_kriteria` (`kriteria_id`, `nama_kriteria`, `bobot`, `type`) VALUES
('K01', 'SKS Total', 20, 'Cost'),
('K02', 'SKS Tempuh', 20, 'Benefit'),
('K03', 'SKS Sisa', 10, 'Cost'),
('K04', 'Studi Total', 20, 'Cost'),
('K05', 'Studi Tempuh', 20, 'Benefit'),
('K06', 'Studi Sisa', 10, 'Benefit');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_mahasiswa`
--

CREATE TABLE `tbl_mahasiswa` (
  `mahasiswa_id` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_mahasiswa` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `npm` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sks_total` int NOT NULL,
  `sks_tempuh` int NOT NULL,
  `sks_sisa` int NOT NULL,
  `studi_total` int NOT NULL,
  `studi_sisa` int NOT NULL,
  `studi_tempuh` int NOT NULL,
  `dosen_id` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `angkatan_id` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_mahasiswa`
--

INSERT INTO `tbl_mahasiswa` (`mahasiswa_id`, `nama_mahasiswa`, `npm`, `sks_total`, `sks_tempuh`, `sks_sisa`, `studi_total`, `studi_sisa`, `studi_tempuh`, `dosen_id`, `angkatan_id`) VALUES
('MHS0001', 'Henry William Wambrauw', '18411003', 144, 138, 6, 14, 3, 11, 'D001', 'A001'),
('MHS0002', 'Yonathan Freddy Salim Lim', '18411004', 144, 138, 6, 14, 3, 11, 'D001', 'A001'),
('MHS0003', 'Petrus Alexander Kameubun', '18411005', 144, 128, 16, 14, 3, 11, 'D001', 'A001'),
('MHS0004', 'Naufal Fikri Rusli', '18411012', 144, 138, 6, 14, 3, 11, 'D001', 'A001'),
('MHS0005', 'Andi Syamsul Arif', '18411014', 144, 138, 6, 14, 3, 11, 'D001', 'A001'),
('MHS0006', 'Hamid Badhawi', '18411016', 144, 138, 6, 14, 3, 11, 'D001', 'A001'),
('MHS0007', 'Rebly Megib Tabuni', '18411017', 144, 92, 52, 14, 3, 11, 'D001', 'A001'),
('MHS0008', 'Samuel Tono Soyan', '18411018', 144, 131, 13, 14, 3, 11, 'D001', 'A001'),
('MHS0009', 'Erland Heri Prasetyo', '18411019', 144, 134, 10, 14, 3, 11, 'D001', 'A001'),
('MHS0010', 'Andreas Wiranata Dien', '18411020', 144, 121, 23, 14, 3, 11, 'D001', 'A001');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `user_id` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`user_id`, `username`, `password`, `role`) VALUES
('U001', 'admin', '$2y$12$HxLREC6Gzxe3/2A3kykTH.4TH39C.HFkioXlrmLiOC4AZJa71F8se', 1),
('U002', 'evanita', '$2y$12$hDXNoYar.RtBeq4XeYGNSOKtDkYSLOzzDF2N4Gt2TtfsndQY6GDWy', 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_foreign` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `tbl_angkatan`
--
ALTER TABLE `tbl_angkatan`
  ADD PRIMARY KEY (`angkatan_id`);

--
-- Indexes for table `tbl_dosen`
--
ALTER TABLE `tbl_dosen`
  ADD PRIMARY KEY (`dosen_id`),
  ADD KEY `tbl_dosen_user_id_foreign` (`user_id`);

--
-- Indexes for table `tbl_hasil`
--
ALTER TABLE `tbl_hasil`
  ADD PRIMARY KEY (`hasil_id`),
  ADD KEY `tbl_hasil_mahasiswa_id_foreign` (`mahasiswa_id`);

--
-- Indexes for table `tbl_kriteria`
--
ALTER TABLE `tbl_kriteria`
  ADD PRIMARY KEY (`kriteria_id`),
  ADD UNIQUE KEY `tbl_kriteria_nama_kriteria_unique` (`nama_kriteria`);

--
-- Indexes for table `tbl_mahasiswa`
--
ALTER TABLE `tbl_mahasiswa`
  ADD PRIMARY KEY (`mahasiswa_id`),
  ADD UNIQUE KEY `tbl_mahasiswa_npm_unique` (`npm`),
  ADD KEY `tbl_mahasiswa_dosen_id_foreign` (`dosen_id`),
  ADD KEY `tbl_mahasiswa_angkatan_id_foreign` (`angkatan_id`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `tbl_users_username_unique` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_dosen`
--
ALTER TABLE `tbl_dosen`
  ADD CONSTRAINT `tbl_dosen_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_hasil`
--
ALTER TABLE `tbl_hasil`
  ADD CONSTRAINT `tbl_hasil_mahasiswa_id_foreign` FOREIGN KEY (`mahasiswa_id`) REFERENCES `tbl_mahasiswa` (`mahasiswa_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_mahasiswa`
--
ALTER TABLE `tbl_mahasiswa`
  ADD CONSTRAINT `tbl_mahasiswa_angkatan_id_foreign` FOREIGN KEY (`angkatan_id`) REFERENCES `tbl_angkatan` (`angkatan_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_mahasiswa_dosen_id_foreign` FOREIGN KEY (`dosen_id`) REFERENCES `tbl_dosen` (`dosen_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

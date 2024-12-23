-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 23, 2024 at 12:58 AM
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
('NnMrE4lcwOPoYQgAkWZzlELQ5EZtAWYI32rzgW8F', 'U002', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRGNiYlUwd3k3clpaU20yanhiMGI4TmYxUXpsYXo5OHAwRFhJc3F1dyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO3M6NDoiVTAwMiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9QZXJoaXR1bmdhbiI7fX0=', 1734915249),
('pnkArFOX0EnL4Q3feZuDrH4jaXPtIv7RT0GUG35e', 'U004', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36 Edg/131.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiaUpwVjl6ZmpKaXJDZXBhc2lvQTg1Tk5DckJXUHlkMVFXaUF1QXprRCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL01haGFzaXN3YSI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvTWFoYXNpc3dhIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO3M6NDoiVTAwNCI7fQ==', 1734907413);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_angkatan`
--

CREATE TABLE `tbl_angkatan` (
  `angkatan_id` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dosen_id` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun_angkatan` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jurusan` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_angkatan`
--

INSERT INTO `tbl_angkatan` (`angkatan_id`, `dosen_id`, `tahun_angkatan`, `jurusan`) VALUES
('A001', 'D001', '2018', 'Teknik Informatika'),
('A002', 'D002', '2019', 'Teknik Informatika');

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
('D001', 'Evanita V Manullang', 'U002'),
('D002', 'Marla S.S. Pieter', 'U003'),
('D003', 'Dosen 1', 'U005');

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
('K01', 'SKS Tempuh', 20, 'Benefit'),
('K02', 'SKS Sisa', 10, 'Cost'),
('K03', 'Studi Tempuh', 20, 'Benefit'),
('K04', 'Studi Sisa', 10, 'Cost'),
('K05', 'SKS Total', 20, 'Benefit'),
('K06', 'Studi Total', 20, 'Benefit');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_mahasiswa`
--

CREATE TABLE `tbl_mahasiswa` (
  `mahasiswa_id` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_mahasiswa` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `npm` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sks_tempuh` int NOT NULL,
  `sks_sisa` int NOT NULL,
  `studi_tempuh` int NOT NULL,
  `studi_sisa` int NOT NULL,
  `sks_total` int NOT NULL,
  `studi_total` int NOT NULL,
  `dosen_id` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `angkatan_id` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
('U002', 'dosen1', '$2y$12$cXkQ3gm4NYcal09jxnqVluXywQTsIgooc0nd474tpjLR4O1tO62gm', 3),
('U003', 'dosen2', '$2y$12$TqMbTn58bxt/mrXC3aoXeOrX8MJF3O/ghCodkLV5FdzhP6SQqR8zS', 3),
('U004', 'staff', '$2y$12$NIEZXWJVpJi/jFDiYI2FNOTeRFTku5PjR6s7.WU21gKpRxX.wvXTa', 2),
('U005', 'dosenbaru', '$2y$12$vtsNJEHHD6Pxt0YAaBvDM.FUvJfpeWDdKBCY6KcuCUkKTz63IRwuK', 3);

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
  ADD PRIMARY KEY (`angkatan_id`),
  ADD KEY `fk_angkatan_dosen_id_foreign` (`dosen_id`);

--
-- Indexes for table `tbl_dosen`
--
ALTER TABLE `tbl_dosen`
  ADD PRIMARY KEY (`dosen_id`),
  ADD KEY `tbl_dosen_user_id_foreign` (`user_id`);

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
-- Constraints for table `tbl_angkatan`
--
ALTER TABLE `tbl_angkatan`
  ADD CONSTRAINT `fk_angkatan_dosen_id_foreign` FOREIGN KEY (`dosen_id`) REFERENCES `tbl_dosen` (`dosen_id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `tbl_dosen`
--
ALTER TABLE `tbl_dosen`
  ADD CONSTRAINT `tbl_dosen_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`user_id`) ON DELETE CASCADE;

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

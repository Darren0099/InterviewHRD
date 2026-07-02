-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 02 Jul 2026 pada 08.21
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
-- Database: `db_hrd`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `divisi`
--

CREATE TABLE `divisi` (
  `id` int(11) NOT NULL,
  `nama_divisi` varchar(100) DEFAULT NULL,
  `kuota_top` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `divisi`
--

INSERT INTO `divisi` (`id`, `nama_divisi`, `kuota_top`) VALUES
(1, 'Graphic Design', 4),
(2, 'Content Creator', 3),
(3, 'Finance', 4),
(4, 'Project Management', 7),
(5, 'Human Resource', 7),
(6, 'Public Relation', 7),
(7, 'Vice Leader', 5),
(8, 'Leader', 5),
(9, 'Social Media Management', 1),
(10, 'Secretary', 4);

-- --------------------------------------------------------

--
-- Struktur dari tabel `penilaian`
--

CREATE TABLE `penilaian` (
  `id` int(11) NOT NULL,
  `regional` varchar(50) NOT NULL,
  `divisi` varchar(100) NOT NULL,
  `nama_kandidat` varchar(150) NOT NULL,
  `nama_hrd` varchar(150) NOT NULL,
  `aspek_teknis` tinyint(4) NOT NULL DEFAULT 0,
  `aspek_komunikasi` tinyint(4) NOT NULL DEFAULT 0,
  `aspek_sikap` tinyint(4) NOT NULL DEFAULT 0,
  `aspek_motivasi` tinyint(4) NOT NULL DEFAULT 0,
  `total` tinyint(4) NOT NULL DEFAULT 0,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `regional`
--

CREATE TABLE `regional` (
  `id` int(11) NOT NULL,
  `nama_regional` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `regional`
--

INSERT INTO `regional` (`id`, `nama_regional`) VALUES
(5, 'BANGKA BELITUNG'),
(4, 'BENGKULU'),
(3, 'JAMBI'),
(2, 'LAMPUNG'),
(1, 'SUMSEL');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `divisi`
--
ALTER TABLE `divisi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `penilaian`
--
ALTER TABLE `penilaian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_regional` (`regional`),
  ADD KEY `idx_divisi` (`divisi`),
  ADD KEY `idx_total` (`total`);

--
-- Indeks untuk tabel `regional`
--
ALTER TABLE `regional`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_regional` (`nama_regional`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `divisi`
--
ALTER TABLE `divisi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `penilaian`
--
ALTER TABLE `penilaian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `regional`
--
ALTER TABLE `regional`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

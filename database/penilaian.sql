-- Struktur tabel untuk di-import lewat DB Explorer (Adminer) di hosting.
-- Sama dengan migrasi Laravel. Aman dijalankan berulang.
CREATE TABLE IF NOT EXISTS `penilaian` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `regional` varchar(100) NOT NULL,
  `divisi` varchar(100) NOT NULL,
  `nama_kandidat` varchar(150) NOT NULL,
  `nama_hrd` varchar(150) NOT NULL,
  `aspek_teknis` tinyint unsigned NOT NULL DEFAULT 0,
  `aspek_komunikasi` tinyint unsigned NOT NULL DEFAULT 0,
  `aspek_sikap` tinyint unsigned NOT NULL DEFAULT 0,
  `aspek_motivasi` tinyint unsigned NOT NULL DEFAULT 0,
  `total` smallint unsigned NOT NULL DEFAULT 0,
  `catatan` text NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `penilaian_regional_divisi_index` (`regional`,`divisi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
